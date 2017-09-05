<?php
include('includes/simplehtmldom/simple_html_dom.php');


class ESV {
	
	// Fields
	private $key = "IP";
	
	private $psg;
	private $ops;
	private $url;
	private $dat;
	
	
	// Constructor
	function __construct() {
		$this->setPSG();
		$this->setOPS();
		$this->setURL();
	}
	
	
	function processData() {
        
        $buffer = file_get_html($this->getURL());
                   
        foreach($buffer->find('span.small-caps, span.selah, h3.psalm-book') as $e) {
            $e->outertext = $e->innertext;
        }
        
        foreach($buffer->find('span.selah, h3.psalm-book') as $e) {
            $e->outertext = "";
        }
                
        //foreach($buffer->find('h3.psalm-book') as $e) {
        //    $e->outertext = "";
        //}
        
        //echo $buffer;
        
		//$data = fopen($this->getURL(), "r");
        
		echo "<table id='selectable'>";
        
		//while (!feof($data)) {
			
			//$buffer = fgets($data);
			$strlen = strlen($buffer);
            		
			for($i = 0; $i <= $strlen; $i++)
			{
				$char = substr($buffer, $i, 1);
				
				if ($char == '<' && substr($buffer, $i+1, 1) != '/') {
					
					// Get the tag name
					$tag = "";
					$char = substr($buffer, ++$i, 1);
					while ($char != ' ' && $char != '>') {
						$tag .= $char;
						$char = substr($buffer, ++$i, 1);
					}
					
					// Go to end of tag
					while ($char != '>') {
						$char = substr($buffer, ++$i, 1);
					}
					
					
					// Handle tag content
					$content = "";
					if (strcmp($tag, "h2") == 0) {  // Reference
						$char = substr($buffer, ++$i, 1);
						while ($char != '<') {
							$content .= $char;
							$char = substr($buffer, ++$i, 1);
						}
						//echo "\nFound reference: " . $content . " \n";
						$this->addChapter($content, true);
					}
					
					if (strcmp($tag, "h3") == 0) {  // Section heading
						$char = substr($buffer, ++$i, 1);
						while ($char != '<') {
							$content .= $char;
							$char = substr($buffer, ++$i, 1);
						}
						//echo "  Found section heading: " . $content . " \n";
						$this->addSection($content);
					}
					
					if (strcmp($tag, "p") == 0) {  // Paragraph
						//$char = substr($buffer, ++$i, 1);
						if (substr($buffer, $i+1, 1) != '<') {
							$char = substr($buffer, ++$i, 1);
							while ($char != '<') {
								$content .= $char;
								$char = substr($buffer, ++$i, 1);
							}
						}
						//$char = substr($buffer, ++$i, 1);
						//echo "" . $content . "\n";
						$this->addVerse("", $content);
					}
					
                    
					if (strcmp($tag, "span") == 0) {
                        
                        // Get the verse number
						$char = substr($buffer, ++$i, 1);
						while ($char != '<' && $char != '&') {
							$content .= $char;
							$char = substr($buffer, ++$i, 1);
						}
						$number = $content;
						
						while ($char != '>') { // Go to end of closing span tag
							$char = substr($buffer, ++$i, 1);
						}
						
						
                        // Get the verse content
						$content = "";
						while (substr($buffer, $i+1, 1) != '<')
						{
                            $char = substr($buffer, ++$i, 1);
							$content .= $char;
						}
						//echo "    Found verse text: " . $content . " \n";
						
						$this->addVerse($number, $content);
					}
				}
			}
            //}
		echo "</table>";
		//fclose($data);
	}
	
	
	function extractFLO($text) {
		
		$out = "";
			
		$textlen = strlen($text);
		
		for($j = 0; $j <= $textlen; $j++)
		{
			$char = substr($text, $j, 1);
			
			if ($char == '&') {
				while ($char != ';') {
					$out .= $char;
					$char = substr($text, ++$j, 1); }
				$out .= $char; }
			
			else if (ctype_alpha($char)) {
				$out .= $char;
				while (ctype_alpha($char)) {
					$char = substr($text, ++$j, 1); }
				if ($char != ' ' && $char != '\'') $j--; 
                if ($char == '\'') $j+=2;}
                
			else {
				$out .= $char; }
		}
		
		return $out;
	}
	
	/* addChapter
	
	*/
	function addChapter($chapter) {
		echo "<tr class='row'> \n";
		echo "  <td class='col chapter flo' colspan='2'> <h2 class='C_text'>" . $this->extractFLO($chapter) . "</h2> </td>\n";
        echo "  <td class='col chapter full' colspan='2'> <h2 class='C_text'>" . $chapter . "</h2> </td>\n";
		echo "</tr> \n";
	}
	
	/* addSection
	
	*/
	function addSection($text) {
        echo "<tr class='row blank'> \n";
		echo "    <td class='col blank'> &nbsp; </td> \n";
		echo "    <td class='col blank'> &nbsp; </td> \n";
		echo "</tr> \n";
		
		echo "<tr class='row'> \n";
		echo "    <td class='col section flo' colspan='2'> <h3 class='S_text'>" . $this->extractFLO($text) . "</h3> </td>\n";
        echo "    <td class='col section full' colspan='2'> <h3 class='S_text'>" . $text . "</h3> </td>\n";
		echo "</tr> \n";
	}
	
	/* addVerse
	
	*/
	function addVerse($number, $text) {
		if (strpos($number, ':')) $number = "1";
		
		$extra = "";
		if (strcmp($number, "") == 0) {
			$extra = "&nbsp;";
		}
		
		echo "<tr class='row'> \n";
		echo "    <td class='col V_num flo'>" . $number . $extra . "</td> \n";
		echo "    <td class='col V_text flo'>" . $this->extractFLO($text) . $extra . "</td> \n";
        echo "    <td class='col V_num full'>" . $number . $extra . "</td> \n";
        echo "    <td class='col V_text full'>" . $text . $extra . "</td> \n";
		echo "</tr> \n";
	}
	
	
	
	function printFull() {
		$data = fopen($this->getURL(), "r");
	
		while (!feof($data)) {
			$buffer = fgets($data, 4096);
			echo $buffer; }
		
		fclose($data);
	}
	
	
	
	// Getters
	function getPassage() { return $this->passage; }
	function getKEY() { return $this->key; }
	function getPSG() { return $this->psg; }
	function getOPS() { return $this->ops; }
	function getURL() { return $this->url; }
	
	// Setters
	function setPassage() { $this->passage = array(); }
	function setPSG() { $this->psg = urlencode($_GET["passage"]); }
	function setOPS() { $this->ops = join("&", array('include-passage-references=true',
													 'include-verse-numbers=true',
													 'include-footnotes=false',
													 'include-footnote-links=false',
													 'include-word-ids=false',
													 'include-copyright=false',
													 'include-subheadings=false',
													 'include-virtual-attributes=false',
													 'include-audio-link=false',
													 'include-short-copyright=false')); }
	function setURL() { $this->url  = "http://www.esvapi.org/v2/rest/passageQuery?";
						$this->url .= "key="     . $this->getKEY() . "&";
						$this->url .= "passage=" . $this->getPSG() . "&";
						$this->url .= "options=" . $this->getOPS(); }
}


?>
<!DOCTYPE html>

<html>


<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <title>ISTHI</title>
    <link rel="stylesheet" href="includes/style.css" type="text/css" media="screen" title="no title" charset="utf-8">
</head>


<body>
    
    <div id="wrapper">
        
        <header>
            
            <div class="logo">
                isthi
            </div>
            
            <div class="query">
                <form action="index.php" method="get">
                    <input type="text" placeholder="Passage" name="passage" value="<?php echo $_GET['passage']; ?>" onclick="this.select();">
                    <input type="submit" value="Go"/></input>
                </form>
            </div>
            
        </header>
        
        <main>
            <div class="extraspace"></div>
            <div class="section">
                <?php
                include("ESV.php");
                $esv = new ESV();
                $esv->processData();
                ?>
            </div>
            <br />
            <br />
        </main>
        
        <footer>
        </footer>
        
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    </script>
    <script>
        $(document).ready(function(){
            
            $("tr").click(function(){
                if ($(this).find("td.flo").is(":visible")) {
                    $("td.flo").show();
                    $("td.full").hide();
                    $(this).find("td.flo").hide();
                    $(this).find("td.full").show();
                }
                else {
                    $(this).find("td.full").hide();
                    $(this).find("td.flo").show();
                }
            });
            
            $("tr").css('cursor','pointer');
        });
    </script>
    
</body>


</html>
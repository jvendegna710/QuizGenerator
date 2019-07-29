<!DOCTYPE html>
<html>
<head>
<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
include_once('\\inetpub\\wwwroot\\user.php.inc');

// if user is manager: style= display ''
if ($db->user == "JVENDEGNA20") {
    $user->manager = '1';
}
if ($user->manager == '1'){
    $disp = '';
}else {$disp = 'none';}
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">    
<title>Quiz Management Menu</title>
<!-- <link rel="stylesheet" type="text/css" href="/ucsStyles.css">-->
<link rel='stylesheet' type='text/css' href='Bulma_APR2018.css'>
<!--<script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>-->
<style>
    HTML
    {
        font-family : sans-serif;            
        BACKGROUND-COLOR: #fff8dc;
        text-align:center;		
    }
</style>
</head>
<body>
<nav class="navbar is-link">
    <div class="navbar-brand">
        <a class="navbar-item" href="Quiz_Management.php">
            <img src="Quiz_button.png" alt="Quiz Management Page" width="35" height="58" class='noprint'>
        </a>
    <div class="navbar-menu">
        <div class="navbar-start">      
        <a class="navbar-item" href="Create_Quiz.php">
        Create a Quiz
        </a>
        <a class="navbar-item" href="Assign_Quiz.php">
        Assign a Quiz
        </a>
        <a class="navbar-item" href="Quiz.php">
        Take a Quiz
        </a>
        <a class="navbar-item" href="Quiz_Review.php">
        Review Quiz Results
        </a>
        </div>
    </div>
</nav>  
<section class='herois-primary is-medium' style='padding:.5em;'>
  <div class="hero-body" style='padding:0;'>
    <div class="container">
        <h1 class="title is-1">
            Quiz Management Menu
        </h1>
    </div>
   </div>
</section>
<section class="section" style='padding:0;'>
    <div class="container">
      <p class="subtitle">
        What would you like to do?
      </p>
    </div><br>     
    <div class='container' style='width:50%; height:30%;'>
        <form method='POST' action=''>
            <div class='columns'>       
              <div class='column' nowrap>
                <div class="card" style='display:<?php echo $disp ?>;'>
                        <div class="card-content style='height:20em;'">
                            <h2 class="title">Create<br>Quiz</h2>                                                     
                        </div><!-- ENDS CARD CONTENT -->                        
                        <footer class="card-footer">                            
                            <span class="card-footer-item">
                                <a href="Create_Quiz.php" class="button is-info is-focused">
                                     Create
                                </a>
                            </span>
                        </footer>
                    </div><!-- ENDS CARD -->               
                </div><!-- ENDS COLUMN -->
                <div class='column' nowrap>
                <div class="card" style='display:<?php echo $disp ?>;'>
                        <div class="card-content style='height:20em;">
                            <h2 class="title">Assign<br>Quiz</h2>                                                     
                        </div><!-- ENDS CARD CONTENT -->
                        <footer class="card-footer">                            
                            <span class="card-footer-item">
                                <a href="Assign_Quiz.php" class="button is-info is-focused">
                                    Assign
                                </a>
                            </span>
                        </footer>
                    </div><!-- ENDS CARD -->                      
                </div><!-- ENDS COLUMN -->
                <div class='column' nowrap>
                <div class="card" style='display:<?php echo $disp ?>;'>
                        <div class="card-content style='height:20em;">
                            <h2 class="title">Take<br>Quiz</h2>                                                     
                        </div><!-- ENDS CARD CONTENT -->
                        <footer class="card-footer">                            
                            <span class="card-footer-item">
                                <a href="Quiz.php" class="button is-success is-focused">
                                    Quiz
                                </a>
                            </span>
                        </footer>
                    </div><!-- ENDS CARD -->                   
                </div><!-- ENDS COLUMN -->
                <div class='column' nowrap>
                <div class="card" style='display:<?php echo $disp ?>;'>
                        <div class="card-content style='height:20em;">
                            <h2 class="title">Review Results</h2>                                                     
                        </div><!-- ENDS CARD CONTENT -->                        
                        <footer class="card-footer">                            
                            <span class="card-footer-item">
                                <a href="Quiz_Review.php" class="button is-danger is-focused">
                                    REVIEW
                                </a>
                            </span>
                        </footer>
                    </div><!-- ENDS CARD -->
                </div><!-- ENDS COLUMN -->                
             </div> 
        </form>
    </div>
  </section>
  </body>
</html>
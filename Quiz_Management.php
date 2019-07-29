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
<title>Quiz Management Menu</title>
<link rel="stylesheet" type="text/css" href="/Resources/bootstrap-4.1.1-dist/css/bootstrap.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="print" href="print_result_style.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="quiz_style.css">
<link rel="stylesheet" type="text/css" href="/Resources/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/Resources/bootstrap-4.1.1-dist/css/bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="http://cs_webserver/Resources/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="http://cs_webserver/Resources/bootstrap-4.1.1-dist/js/bootstrap.js"></script>
<script type="text/javascript" src='http://cs_webserver/Resources/jquery-ui-1.12.1/jquery-ui.js'></script>
<style>
HTML, body
{
    font-family : sans-serif;            
    BACKGROUND-COLOR: #fff8dc;
    text-align:center;		
}
.navbar-dark .navbar-nav
.nav-link{
    color:#fff;
}
.nav-link:hover { 
    color: lightgrey;
}
.navbar-light .navbar-nav .nav-link {
    color: #fff;
    margin-left:1rem;
    margin-right:1rem;
}
</style>
</head>
<body>


<!-- Bootstrap NAVIGATION MENU -->
<nav class="navbar navbar-expand-sm justify-content-center bg-primary navbar-light" style='height:3.5em'>      
        <a class="navbar-brand" href="Quiz_Management.php">
            <img src="Quiz_button_light.png" alt="Quiz Management Page" width="50" height="58" class='noprint'>
        </a>    
    <ul class="navbar-nav" style='font-weight:bold'>
        <li class="nav-item">       
            <a class="nav-link" href="Create_Quiz.php">
                Create a Quiz
            </a>
        </li>
        <li class="nav-item" style='border-left:thin solid grey;'>  
            <a class="nav-link" href="View_Quiz.php">
                View a Quiz
            </a>
        </li>
        <li class="nav-item" style='border-left:thin solid grey;'>  
            <a class="nav-link" href="Edit_Quiz.php">
                Edit a Quiz
            </a>
        </li>
        <li class="nav-item" style='border-left:thin solid grey;'>  
            <a class="nav-link" href="Assign_Quiz.php">
                Assign a Quiz
            </a>
        </li>
        <li class="nav-item" style='border-left:thin solid grey;'>  
            <a class="nav-link" href="Remove_Quiz.php">
                Remove Quiz Assignment
            </a>
        </li>
        <li class="nav-item" style='border-left:thin solid grey;'>  
            <a class="nav-link" href="Quiz.php">
                Take a Quiz
            </a>
        </li>
        <li class="nav-item" style='border-left:thin solid grey;'>  
            <a class="nav-link" href="Quiz_Results.php">
                Individual Results
            </a>
        </li>
        <li class="nav-item" style='border-left:thin solid grey;'>  
            <a class="nav-link" href="Quiz_Review.php">
                Review Quiz Results
            </a>
        </li>        
    </ul>   
</nav> 
<!-- END NAVIGATION MENU -->



    <div class="container" style='margin-top:2em;'>
        <h1 class="title is-1">
            Quiz Management Menu
        </h1>
    </div>
  
    <div class="container">
      <p class="subtitle">
        What would you like to do?
      </p>
    </div>
    <br>     

    <center>
        <form method='POST' action=''>
            <div class='card-deck' style='padding:2em;'>              


              <div class="card" style='display:<?php echo $disp ?>; width:20em;'>
                        <div class="card-body style='height:20em;'">
                            <h2 class="card-title">Create<br>Quiz</h2>                                                  
                            <a href="Create_Quiz.php" class="btn btn-warning">
                                     Create
                                </a>
                      </div><!-- ENDS CARD-BODY -->               
                </div><!-- ENDS CARD -->
               
                <div class="card" style='display:<?php echo $disp ?>; width:20em;'>
                        <div class="card-body style='height:20em;">
                            <h2 class="card-title">View<br>Quiz</h2>                                                     
                            <a href="View_Quiz.php" class="btn btn-primary">
                                View
                            </a>
                     </div><!-- ENDS CARD -->                   
                </div><!-- ENDS COLUMN -->

                <div class="card" style='display:<?php echo $disp ?>; width:20em;'>
                        <div class="card-body style='height:20em;">
                            <h2 class="card-title">Edit<br>Quiz</h2>                                                     
                            <a href="Edit_Quiz.php" class="btn btn-warning">
                                View
                            </a>
                     </div><!-- ENDS CARD -->                   
                </div><!-- ENDS COLUMN -->

                <div class="card" style='display:<?php echo $disp ?>; width:20em;'>
                        <div class="card-body style='height:20em;">
                            <h2 class="card-title">Assign<br>Quiz</h2>                                                     
                            <a href="Assign_Quiz.php" class="btn btn-primary">
                                Assign
                            </a>                       
                    </div><!-- ENDS CARD-BODY -->                      
                </div><!-- ENDS CARD -->

                <div class="card" style='display:<?php echo $disp ?>; width:20em;'>
                        <div class="card-body style='height:20em;">
                            <h2 class="card-title">Remove<br>Quiz</h2>                                                     
                            <a href="Remove_Quiz.php" class="btn btn-danger">
                                View
                            </a>
                     </div><!-- ENDS CARD -->                   
                </div><!-- ENDS COLUMN -->
                
                <div class="card" style='display:<?php echo $disp ?>; width:20em;'>
                        <div class="card-body style='height:20em;">
                            <h2 class="card-title">Take<br>Quiz</h2>                                                     
                            <a href="Quiz.php" class="btn btn-success">
                                Quiz
                            </a>
                     </div><!-- ENDS CARD -->                   
                </div><!-- ENDS COLUMN -->

                <div class="card" style='display:<?php echo $disp ?>; width:20em;'>
                        <div class="card-body style='height:20em;">
                        <h2 class="card-title">Individual Results</h2>                                                     
                        <a href="Quiz_Results.php" class="btn btn-success">
                            Individual Results
                        </a>                     
                   </div><!-- ENDS CARD-BODY -->
                </div><!-- ENDS CARD-->
               
                <div class="card" style='display:<?php echo $disp ?>; width:16em;'>
                        <div class="card-body style='height:20em;">
                        <h2 class="card-title">Review Results</h2>                                                     
                        <a href="Quiz_Review.php" class="btn btn-primary">
                            REVIEW
                        </a>                     
                   </div><!-- ENDS CARD-BODY -->
                </div><!-- ENDS CARD-->              


             </div><!-- ENDS COLUMNS -->
        </form>
    </center> <!-- ENDS CONTAINER --> 
  </body>
</html>
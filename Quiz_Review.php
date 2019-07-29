<!DOCTYPE HTML>
<HTML>
<HEAD>
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

<script>
  $( function() {
    $( ".datepicker" ).datepicker();
  } );
</script>
<style>
HTML, BODY
{               
    background-color:#fff8dc;    		
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

<TITLE>REVIEW RESULTS OF ASSIGNED QUIZZES</TITLE>
</HEAD>
<BODY>
<div class='noprint'>
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
</div>

    <?php
        include_once('\\inetpub\\wwwroot\\database.php.inc');
        include_once('\\inetpub\\wwwroot\\user.php.inc');

        global $db;
        global $user;
       
        $disp = 'none';
        $qName = '';
        $QID = '';
        $quizResults = '';
        $csvInfo = array("USER,DEPARTMENT,PASS FAIL,SCORE,DATE QUIZ ASSIGNED");
        $tkArray = array();
        $utArray = array();
        $hcount = 0;
        $dates = '';

        if(!empty($_REQUEST['DATE1'])){
            //echo $_REQUEST['DATE1']; 
            $dates .= " AND TRUNC(DATE_TAKEN) >= TO_DATE('".$_REQUEST['DATE1']."', 'MM/DD/YYYY')";
        }
        if(!empty($_REQUEST['DATE2'])){
            $dates .= " AND TRUNC(DATE_TAKEN) <= TO_DATE('".$_REQUEST['DATE2']."', 'MM/DD/YYYY')";
        }

        if (!empty($_REQUEST['sort'])){
            if ($_REQUEST['sort'] != 'USERNAME'){
                $sortby = $_REQUEST['sort'].', USERNAME';
            }else{$sortby = $_REQUEST['sort'];}
        }else{ $sortby = 'DEPARTMENT, USERNAME';}

        if ($_REQUEST['sQuiz'] != ''){
            $QID = $_REQUEST['sQuiz'];
        }else if ($_REQUEST['cq'] != ''){ 
               $QID = $_REQUEST['cq']; 
        }else $QID = '';


        if( $QID != '' ) {
            $disp = '';           
            
            $sql = 'SELECT QUIZ_NAME FROM QUIZ_MASTER WHERE QID=:1';
            $results = $db->ExecSQL($sql, $QID);
            
            foreach($results as $r){
                $qName .= $r['QUIZ_NAME'];
            }
           
            $quizResults = "<table class='table table-bordered table-striped table-light' style='width:100%;'>
            <thead class='thead-light'><tr>";
            if(!empty($_SERVER[' QUERY_STRING'])){
                $quizResults .= "
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=USERNAME&cq={$QID}'>USER</a></th>
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=DEPARTMENT&cq={$QID}'>DEPARTMENT</a></th>
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=PASS&cq={$QID}'>PASS/FAIL</th>
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=SCORE&cq={$QID}'>SCORE</a></th>
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=ATTEMPTS&cq={$QID}'>ATTEMPTS</a></th>
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=DATE_TAKEN&cq={$QID}'>DATE QUIZ ASSIGNED</a></th>";
             }else{
                 $quizResults .= "
                 <th><a href='".$_SERVER['PHP_SELF']."?sort=USERNAME&cq={$QID}'>USER</a></th>
                 <th><a href='".$_SERVER['PHP_SELF']."?sort=DEPARTMENT&cq={$QID}'>DEPARTMENT</a></th>
                 <th><a href='".$_SERVER['PHP_SELF']."?sort=PASS&cq={$QID}'>PASS/FAIL</a></th>
                 <th><a href='".$_SERVER['PHP_SELF']."?sort=SCORE&cq={$QID}'>SCORE</a></th>
                 <th><a href='".$_SERVER['PHP_SELF']."?sort=ATTEMPTS&cq={$QID}'>ATTEMPTS</a></th>
                 <th><a href='".$_SERVER['PHP_SELF']."?sort=DATE_TAKEN&cq={$QID}'>DATE QUIZ TAKEN</a></th>";
            }

            $quizResults .= "</tr></thead>";

            $sql = "SELECT DISTINCT Q.USERNAME USERNAME, PASS, SCORE ,ATTEMPTS, MAX (DATE_TAKEN) DATE_TAKEN, DEPARTMENT
                    FROM QUIZ_USERS Q,USERLIST U 
                    WHERE  QUIZID = :1 
                        AND Q.USERID = U.ID
                        AND SCORE IS NOT NULL
                        AND ACTIVE = 'Y'
                        {$dates}                        
                    GROUP BY Q.USERNAME, PASS, SCORE, ATTEMPTS, DEPARTMENT
                    ORDER BY {$sortby}";
            //echo $sql."\n\n".$QID;

            $results = $db->ExecSQL($sql, $QID);
            $tcount = count($results);     
            foreach ($results As $r){
                $quizResults .= "<tr><td>".$r['USERNAME']."</td><td>".$r['DEPARTMENT']."</td><td>".$r['PASS']."</td><td>".$r['SCORE']."</td><td>".$r['ATTEMPTS']."</td><td>".$r['DATE_TAKEN']."</td></tr>";
                $tstr = $r['USERNAME'].",".$r['DEPARTMENT'].",".$r['PASS'].",".$r['SCORE'].",".$r['DATE_TAKEN'];
                array_push($tkArray, $tstr);                

            }
            $quizResults .= '</table>';

            
            $untakenResults = "<table class='table table-bordered table-striped table-light' style='width:100%;' >
                                <thead class='thead-light'><tr>";
            if(!empty($_SERVER[' QUERY_STRING'])){
                $untakenResults .= "
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=USERNAME&cq={$QID}&taken=N'>USER</a></th>
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=DEPARTMENT&cq={$QID}&taken=N'>DEPARTMENT</a></th>
                <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=DATE_TAKEN&cq={$QID}&taken=N'>DATE QUIZ TAKEN</a></th>";
            }else{
                $untakenResults .= "
                <th><a href='?sort=USERNAME&cq={$QID}&taken=N'>USER</a></th>
                <th><a href='?sort=DEPARTMENT&cq={$QID}&taken=N'>DEPARTMENT</a></th>
                <th><a href='?sort=DATE_TAKEN&cq={$QID}&taken=N'>DATE QUIZ ASSIGNED</a></th>";
            }
            $untakenResults .= "    <tr></thead>";
            $sql = "SELECT DISTINCT Q.USERNAME USERNAME, MAX (DATE_TAKEN) DATE_TAKEN, DEPARTMENT
                    FROM QUIZ_USERS Q,USERLIST U 
                    WHERE ATTEMPTS IS NULL 
                        AND QUIZID = :1 
                        AND Q.USERID = U.ID
                        AND ACTIVE = 'Y'
                        {$dates}
                    GROUP BY Q.USERNAME, DEPARTMENT   
                    ORDER BY {$sortby}";
        if ($_REQUEST['taken']=='N'){
                $results = $db->ExecSQL($sql, $QID);
                $ucount = count($results);
                $tstr = '';    
                foreach ($results As $r){
                    $untakenResults .= "<tr><td>".$r['USERNAME']."</td><td>".$r['DEPARTMENT']."</td><td>".$r['DATE_TAKEN']."</td></tr>";
                    $tstr = $r['USERNAME'].",".$r['DEPARTMENT'].",,,".$r['DATE_TAKEN'];
                    array_push($utArray, $tstr);                
                }          
                $untakenResults .= "</table>";                
                $quizResults = $untakenResults;
                $hcount = $ucount;
                
                foreach ($utArray as $a){
                    array_push($csvInfo, $a);
                }                
            }else {
                $hcount = $tcount;
                foreach ($tkArray as $a){
                    array_push($csvInfo, $a);
                }
            }
        
            if ($_REQUEST['printout']=='Y'){                
                $date = date('mdY_his');
                $filename = str_replace(' ', '_', $qName)."_Results_".$date.".csv";
                $file = fopen(".\\QuizResults\\".$filename, "wr");  
               // print_r($csvInfo); 
               // echo '<br>Filename is '.$filename;           
                /** */
                foreach ($csvInfo as $line)
                {
                    fputcsv($file,explode(',',$line));
                }
/** */
                fclose($file);
                $dlFile = "<div style='margin:1em; font-size:1.1em; text-align:center; margin:1em;'>File ready. Download Here: 
                <a href=\".\\QuizResults\\".$filename."\">".$filename."</a></div>"; 
            }
            
        }//ENDS IF STATEMENT

        $sql='SELECT QID, QUIZ_NAME, CLIENT_GROUP
                    FROM QUIZ_MASTER                 
                ORDER BY CLIENT_GROUP';
        $results = $db->ExecSQL($sql);

        foreach($results as $r){
            $quizSelect .= "<option value='".$r['QID']."'>".$r['CLIENT_GROUP']." - ".$r['QUIZ_NAME']."</option>";
        }
    ?>
    <form method='POST' action=''>        
                <div class="container noprint">
                    <h4>
                        CHOOSE QUIZ TO REVIEW
                    </h4>
                </div>                    
      
            <center class='noprint'>
                <table class='table' style='width:85%;' class='noprint' id='qChoice'>
                    <tr style='background-color:#fff8dc;'><td>SELECT QUIZ:</td>
                        <td>
                            <select id='quizDD' name='sQuiz' required>
                                <option value=''>- Select -</option>
                                <?php echo $quizSelect; ?>            
                            </select>
                        </td>
                        <td>
                            <span>START DATE:
                                <input type=text class='datepicker' name='DATE1' id='date1' style='width:6rem;'>
                            </span>
                        </td>
                        <td>
                            <span>END DATE:
                                <input type=text class='datepicker' name='DATE2' id='date2' style='width:6rem;'>
                            </span>
                        </td>
                        <td>
                            <label class="checkbox">
                                <input type="checkbox" name='printout' value='Y'>
                                CREATE PRINTOUT
                            </label>                            
                        </td>
                        <td>
                            <label class="checkbox">
                                <input type="checkbox" name='taken' value='N'>
                                SHOW NOT TAKEN ONLY
                            </label>                            
                        </td>                        
                    </tr>                                        
                    <table>
                        <tr>
                            <div id='btnLaunch' class='noprint' style='margin-left:50%; margin-top:3em;'>
                                <input type='hidden' name='a' value='dR'>
                                <span><input type='submit' class='btn btn-primary' value='Display Results'></span>
                                <span><a href='Quiz_Review.php' class='btn btn-warning'>Clear All</a></span>
                        </tr>                        
                    </table>                    
                </table><!-- ENDtable-->
            </center>
            <div style='display:<?php echo $disp; ?>' id='qresults'>
                <div class="title is-5">
                    <center>
                    <?php
                        if(!empty($dlFile)){
                            echo $dlFile;
                        } 
                    ?>
                    <?php echo $qName; ?> Results
                    <div>Total: <?php echo $hcount ?></div>
                    </center>
                </div>
                <div class='container' style='max-height:30em; overflow-y:scroll;' id='printdata' >
                <?php echo $quizResults; ?>        
                </div>                
            </div> 
        </div><!--Ends tablediv   -->
    </form>    
</BODY>
</HTML>
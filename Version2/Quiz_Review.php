<!DOCTYPE HTML>
<HTML>
<HEAD>
<link rel='stylesheet' type='text/css' href='Bulma_APR2018.css'>
<link rel="stylesheet" type="text/css" media="print" href="print_result_style.css" />
<meta name='viewport' content='width=device-width, initial-scale=1'>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
<!--<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>-->
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
<style>
HTML
{
    font-family : sans-serif;            
    background-color:#fff8dc;    		
}
BODY{
    background-color:#fff8dc;
}  
</style>
<TITLE>REVIEW RESULTS OF ASSIGNED QUIZZES</TITLE>
</HEAD>
<BODY>
<div class='noprint'>
<nav class="navbar is-link">
    <div class="navbar-brand" class='noprint'>
        <a class="navbar-item" href="Quiz_Management.php" class='noprint'>
            <img src="Quiz_button.png" alt="Quiz Management Page" width="35" height="58" class='noprint'>
        </a>
    <div class="navbar-menu" class='noprint'>
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
</div><!--
<div class="field" style='margin-left:90%'>
  <div class="control">
    <div class="select is-danger ">
        <select onChange="window.location.href=this.value" style='background-color:#e5ddb9;'>
            <option value="" disabled selected hidden>Management Menu</option>
            <option value='Quiz_Management.php'>Mangement Page</option>
            <option value='Create_Quiz.php'>Create a Quiz</option>
            <option value='Assign_Quiz.php'>Assign a Quiz</option>
            <option value='Quiz.php'>Take a Quiz</option>
            <option value='Quiz_Review.php'>Review Quiz Results</option>
        </select>
    </div>
  </div>
</div> -->
    <?php
        include_once('\\inetpub\\wwwroot\\database.php.inc');
        include_once('\\inetpub\\wwwroot\\user.php.inc');

        global $db;
        global $user;
       
        $disp = 'none';
        $qName = '';
        $QID = '';
        $quizResults = '';
        $hcount = 0;
        

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

            $quizResults = "<table class='table is-bordered is-striped is-fullwidth'><tr>
            <th><a href='?sort=USERNAME&cq={$QID}'>USER</a></th>
            <th><a href='?sort=DEPARTMENT&cq={$QID}'>DEPARTMENT</a></th>
            <th><a href='?sort=PASS&cq={$QID}'>PASS/FAIL</th>
            <th><a href='?sort=SCORE&cq={$QID}'>SCORE</a></th>
            <th><a href='?sort=ATTEMPTS&cq={$QID}'>ATTEMPTS</a></th>
            <th><a href='?sort=DATE_ASSIGNED&cq={$QID}'>DATE QUIZ ASSIGNED</a></th>
            </tr>";

            $sql = "SELECT DISTINCT USERNAME, PASS, SCORE ,ATTEMPTS, MAX (DATE_ASSIGNED) DATE_ASSIGNED, DEPARTMENT
                    FROM QUIZ_USERS Q,USERLIST U 
                    WHERE  QUIZID = :1 
                        AND Q.USERID = U.ID
                        AND SCORE IS NOT NULL
                        AND ACTIVE = 'Y'
                        AND DEPARTMENT <> 'IT DEPARTMENT'
                    GROUP BY USERNAME, PASS, SCORE, ATTEMPTS, DEPARTMENT
                    ORDER BY {$sortby}";

            $results = $db->ExecSQL($sql, $QID);
            $tcount = count($results);     
            foreach ($results As $r){
                $quizResults .= "<tr><td>".$r['USERNAME']."</td><td>".$r['DEPARTMENT']."</td><td>".$r['PASS']."</td><td>".$r['SCORE']."</td><td>".$r['ATTEMPTS']."</td><td>".$r['DATE_ASSIGNED']."</td></tr>";
            }
            $quizResults .= '</table>';

            $untakenResults = "<table class='table is-bordered is-striped is-fullwidth' ><tr>
            <th><a href='?sort=USERNAME&cq={$QID}'>USER</a></th>
            <th><a href='?sort=DEPARTMENT&cq={$QID}'>DEPARTMENT</a></th>            
            <th><a href='?sort=DATE_ASSIGNED&cq={$QID}'>DATE QUIZ ASSIGNED</a></th>
            <tr>";
            $sql = "SELECT DISTINCT USERNAME, MAX (DATE_ASSIGNED) DATE_ASSIGNED, DEPARTMENT
                    FROM QUIZ_USERS Q,USERLIST U 
                    WHERE ATTEMPTS IS NULL 
                        AND QUIZID = :1 
                        AND Q.USERID = U.ID
                        AND ACTIVE = 'Y'
                        AND DEPARTMENT <> 'IT DEPARTMENT'
                    GROUP BY USERNAME, DEPARTMENT   
                    ORDER BY {$sortby}";

            $results = $db->ExecSQL($sql, $QID);
            $ucount = count($results);
                
            foreach ($results As $r){
                $untakenResults .= "<tr><td>".$r['USERNAME']."</td><td>".$r['DEPARTMENT']."</td><td>".$r['DATE_ASSIGNED']."</td></tr>";
            }          
            $untakenResults .= '</table>';
            if ($_REQUEST['taken']=='N'){
                $quizResults = $untakenResults;
                $hcount = $ucount;                
            }else {$hcount = $tcount;}
            
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
        <section class='noprint' class='herois-primary is-medium' style='padding:.5em;'>
            <div class="hero-body" style='padding:0;'>
                <div class="container">
                    <h1 class="title is-4">
                        CHOOSE QUIZ TO REVIEW
                    </h1>
                </div>
            </div>
        </section>             
        <div>
            <center class='noprint'>
                <table class='table' style='width:50%;' class='noprint' id='qChoice'>
                    <tr style='background-color:#fff8dc;'><td>SELECT QUIZ:</td>
                        <td>
                            <select id='quizDD' name='sQuiz' required>
                            <option value=''>- Select -</option>
                            <?php echo $quizSelect; ?>            
                            </select>
                        </td><td>
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
                                <span><input type='submit' class='button is-info' value='Display Results'></span>
                                <span><a href='Quiz_Review.php' class='button is-warning'>Clear All</a></span>
                        </tr>
                    </table>                    
                </table><!-- ENDtable-->
            </center>
            <div style='display:<?php echo $disp; ?>' id='qresults'>
                <div class="title is-5">
                    <center>
                    <?php echo $qName; ?> Results
                    <div>Total: <?php echo $hcount ?></div>
                    </center>
                </div>
                <div class='medscrollbox' id='printdata'>
                <?php echo $quizResults; ?>        
                </div>                
            </div> 
        </div><!--Ends tablediv   -->
    </form>
</BODY>
</HTML>
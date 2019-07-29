<!DOCTYPE HTML>
<HTML>
<HEAD>
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
html, body{
    background-color:#fff8dc;
}
.navbar-dark .navbar-nav
.nav-link{
    color:#fff;
}
.nav-link:hover { 
    color: lightgrey;
}
</style>



<TITLE>Quiz</TITLE>
<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
include_once('\\inetpub\\wwwroot\\user.php.inc');
?>
<script>
    $(document).ready(function(){  
        $('#userDD').change(function(){
            var userID = $(this).val();
            $.ajax({
                url: 'quiz_dropdowns.php',
                type: 'POST',
                data: {user:userID},
                dataType:'json',
                success:function(response){
                    var len = response.length;
                    $('#quizDD').empty();
                    $('#quizDD').append("<option value='null'>- Select -</option>");
                    $('#quizDD').append("<option value='ALL'>ALL QUIZZES</option>");
                    for( var i = 0; i<len; i++){
                        var id = response[i]['quizID'];
                        var name = response[i]['name'];
                        var group = response[i]['group'];                                    
                        $('#quizDD').append( "<option value='" + id + "'>" + group + " - " + name + "</option>");
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('FAILED TO RETRIEVE QUIZ NAME DATA');
                }//ENDS SUCCESS/ERROR OF CALL
            });//ENDS AJAX
        });//ENDS FUNCTION ON CHANGE OF DROPDOWN


         $('#deptselect').change(function(){
            var department = $(this).val();
            $.ajax({
                url: 'quiz_dropdowns.php',
                type: 'POST',
                data: {department:department},
                dataType:'json',
                success:function(response){
                    var len = response.length;
                    $('#quizDD').empty();
                    $('#quizDD').append("<option value='null'>- Select -</option>");
                    $('#quizDD').append("<option value='ALL'>ALL QUIZZES</option>");
                    for( var i = 0; i<len; i++){
                        var id = response[i]['quizID'];
                        var name = response[i]['name'];
                        var group = response[i]['group'];                                    
                        $('#quizDD').append( "<option value='" + id + "'>" + group + " - " + name + "</option>");
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('FAILED TO RETRIEVE QUIZ NAME DATA');
                }//ENDS SUCCESS/ERROR OF CALL
            });//ENDS AJAX
        });//ENDS FUNCTION ON CHANGE OF DROPDOWN

        

    });//ENDS ON DOCUMENT READY FUNCTION

    $('input[type="checkbox"]').on('change', function() {
    $('input[name="' + this.name + '"]').not(this).prop('checked', false);
});
   
</script>

</HEAD>
<BODY>    
    <div id='pageTitle'>Quiz</div>
    
        
<?php

//**********************************************CHOOSE WHICH PART OF PAGE ***************************************************************** */
//print_r($_REQUEST);
switch( $_REQUEST['a'] ) {    
    case 'results': 
        if($_REQUEST['dName'] != 'null'){
            DeptResults();
        }else{
            Results();
        }
        break;
    default :       
        Menu();
        break;   
 }

//*********************************************END CHOOSE WHICH PART OF PAGE ************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************DISPLAY RESULTS ************************************************************************** */

function Results(){

    global $db;
    global $user;
    //print_r($_REQUEST);
    $filename = "Quiz".$_REQUEST['sQuiz']."_Results_for_".$db->user."_". date("mdY") .".txt";
    $fout = fopen(".\\QuizReceipts\\".$filename, "wt");

    $sql = "SELECT USERNAME
    FROM USERLIST
    WHERE ID = :1 ";
    $result = $db->ExecSQL($sql, $_REQUEST['uName']);
    
    foreach ($result as $r){
    $uName = $r['USERNAME'];    
    }      
        

    $date = date('m/d/Y');
    echo "<center style='margin-bottom:1em;'>";    
    if((empty($_REQUEST['uName'])) || (empty($_REQUEST['sQuiz']))){
        echo 'CANNOT PROCESS, MISSING DATA';
        echo '<br>Quiz ID: '.$_REQUEST['sQuiz'];
        echo '<br>Associate Assigned: '.$_REQUEST['uName'];        
    }else if($_REQUEST['sQuiz'] == 'ALL'){
         $name = $_REQUEST['uName'];
         if (empty($_REQUEST['sort'])){
             $_REQUEST['sort'] = 'QUIZ_NAME';
         }
         $sql = "SELECT 
            QUIZ_NAME,
            CLIENT_GROUP,
            MIN_SCORE,
            SCORE,
            PASS,
            ATTEMPTS,
            TRUNC(DATE_TAKEN) DATE_TAKEN,
            TO_CHAR(DATE_TAKEN, 'HH:MM:SS AM') TIME_TAKEN
            FROM QUIZ_MASTER INNER JOIN QUIZ_USERS ON (QID = QUIZID)
            WHERE USERID =:1
            ORDER BY :2";
        $results = $db->ExecSQL($sql,array($name, $_REQUEST['sort']));
        $quizResults = "<div>Test Results for: ".$uName."<br>User ID: ".$name." </div><div class='container' style='max-height:30em; overflow-y:scroll;'>";
        $quiz_results = "Test Results for: ".$uName."\nUser ID: ".$name."\r\n";
        $quizResults .= "<table class='table table-bordered table-striped table-light' style='width:100%;'>
            <thead class='thead-light'><tr>";
       /* $quizResults .= "
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=QUIZ_NAME&user={$uName}'>QUIZ NAME</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=CLIENT_GROUP&user={$uName}'>CLIENT</th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=MIN_SCORE&user={$uName}'>MIN SCORE</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=SCORE&user={$uName}'>SCORE</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=PASS&user={$uName}'>PASS</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=ATTEMPTS&user={$uName}'>ATTEMPTS</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=DATE_TAKEN&user={$uName}'>DATE QUIZ TAKEN</a></th>";/** */
        
        $quizResults .= "
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=QUIZ_NAME&user={$uName}'>QUIZ NAME</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=CLIENT_GROUP&user={$uName}'>CLIENT</th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=SCORE&user={$uName}'>SCORE</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=PASS&user={$uName}'>PASS</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=DATE_TAKEN&user={$uName}'>DATE QUIZ TAKEN</a></th>
                    <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=TIME_TAKEN&user={$uName}'>TIME QUIZ TAKEN</a></th>";
        
        foreach ($results as $r){
            $qName = $r['QUIZ_NAME'];
            $client = $r['CLIENT_GROUP'];
            $min = $r['MIN_SCORE'];
            $score = $r['SCORE'];
            $pass = $r['PASS'];
            $attempts = $r['ATTEMPTS'];          
            $dateTaken = $r['DATE_TAKEN'];
            $timeTaken = $r['TIME_TAKEN'];

           // $quizResults .= "<tr><td>".$qName."</td><td>".$client."</td><td>".$min."</td><td>".$score."</td><td>".$pass."</td><td>".$attempts."</td><td>".$dateTaken."</td></tr>";          
            $quizResults .= "<tr><td>".$qName."</td><td>".$client."</td><td>".$score."</td><td>".$pass."</td><td>".$dateTaken."</td><td>".$timeTaken."</td></tr>";    
            //$quiz_results .= $qName."\n".$client."\n".$min."\n".$score."\n".$pass."\t".$attempts."\t".$dateTaken."\n";
            $quiz_results .= "\r\nQuiz Name: ".$qName."
                            \nClient: ".$client."
                            \nSCORE: ".$score."%
                            \nPASS: ".$pass." 
                            \nDate Taken: ".$dateTaken."
                            \nTime Taken: ".$timeTaken."\r\n\n";                        
                         
        }
        $quizResults .= "</table></div>";
        echo $quizResults;
        fwrite($fout, $quiz_results);
        fclose($fout);
        echo "<div style='padding-left:65%; margin:1em;'>               
                <span> <a href=\".\\QuizReceipts\\".$filename."\" class='btn btn-small btn-primary' download>PRINT RESULTS</a></span>
                <!-- <span> <a href='http://cs_webserver/oracle1/trainingdocs.php' class='btn btn-warning' id='qCloser'>Close Quiz</a></span>   -->
                </div>
                ";
    }else{
        $name = $_REQUEST['uName'];
        $quizID = $_REQUEST['sQuiz'];

        $sql = 'SELECT QUIZ_NAME, MIN_SCORE FROM QUIZ_MASTER WHERE QID =:1';
        $results = $db->ExecSQL($sql,$quizID);
        foreach ($results as $r){
            $qName = $r['QUIZ_NAME'];
            $min = $r['MIN_SCORE'];
        }
        
        global $attempts;
        $sql = "SELECT SCORE, PASS, ATTEMPTS, DATE_TAKEN, TO_CHAR(DATE_TAKEN, 'HH:MM:SS AM') TIME_TAKEN FROM QUIZ_USERS WHERE USERID=:1 AND QUIZID=:2";        
        $results = $db->ExecSQL($sql,array($name,$quizID));
        foreach ($results as $r){
            $attempts = $r['ATTEMPTS'];
            $score = $r['SCORE'];
            $pass = $r['PASS'];
            $dateTaken = $r['DATE_TAKEN'];
            $timeTaken = $r['TIME_TAKEN'];

        }/** */

        $sql = 'SELECT * FROM QUIZ_QUESTIONS WHERE QUIZ_ID =:1 ORDER BY QUESTION_ID';
        $results = $db->ExecSQL($sql,$quizID);

        

        //SHOW RESULTS HERE
        echo "        
            <div class=\"container\">
                <h4>
                RESULTS
                </h4>
            </div>                
            <div style='padding-left:25em; text-align:left;'>
            <br>User ID: ".$name."
            <br>User: ".$uName."
            <br>Date Taken: ".$dateTaken."
            <br>Time Taken: ".$timeTaken."              
            <br>Quiz Name: ".$qName."<br><br>
            YOUR SCORE: ".$score."%<br><br>";
        $quiz_results = "RESULTS
                        \nUser: ".$uName."
                        \nDate Taken: ".$dateTaken."
                        \nTime Taken: ".$dateTaken."
                        \nQuiz Name: ".$qName."                        
                        \n\nYOUR SCORE: ".$score."%\n\n";
        
        if ($pass == 'Y'){
            echo "<b>Congratulations, you've passed!</b><br>";
            echo "</div>";
            $quiz_results .= "\nCongratulations, you've passed!\n";
            //$opStr = ob_get_contents();
            //$opStr =ob_get_flush();
            //file_put_contents($filename, $opStr);
            fwrite($fout, $quiz_results);
            fclose($fout);            
            echo "<div style='padding-left:65%; margin:1em;'>    
                <span><!--<a href='' class=\"btn btn-primary\">Take Another Quiz</a>--></span>
                <!--<span><button onclick=\"window.open('', '_self', ''); window.close();\" class='btn btn-warning'>Close Quiz</button></span>      -->
                <span> <a href=\".\\QuizReceipts\\".$filename."\" class='btn btn-small btn-primary' download>PRINT RESULTS</a></span>
                <!-- <span> <a href='http://cs_webserver/oracle1/trainingdocs.php' class='btn btn-warning' id='qCloser'>Close Quiz</a></span>   -->
                </div>
                ";
        }else{
            echo "<div>Unfortunately the minimum to pass was ".$min."%. You will need to retake this quiz.</div><br><br>
            <span> <a href='http://cs_webserver/oracle1/trainingdocs.php' class='btn btn-danger'>Training Docs</a></span>
            </div>";
        }
        
        


    }//ENDS MAIN ELSE STATEMENT 
    echo "</center><!-- END formdiv-->";

    
    
      
    
}//ENDS FUNCTION


//***********************************************END RESULTS ****************************************************************************** */
/*
################################################################################################################################################
*/

/*
################################################################################################################################################
*/
//***********************************************DEPARTMENT RESULTS **************************************************************************** */
function DeptResults(){
    global $db;
    global $user;
    //print_r($_REQUEST);
    $dept = $_REQUEST['dName'];
    $filename = "Quiz".$_REQUEST['sQuiz']."_Results_for_".$dept."_". date("mdY") .".txt";
    $fout = fopen(".\\QuizReceipts\\".$filename, "wt");

    $quiz_results = '';   
        

    $date = date('m/d/Y');
    echo "<center style='margin-bottom:1em;'>";
    echo "<h4>
    RESULTS
    </h4> ";    
    if((empty($_REQUEST['dName']))){
        echo 'CANNOT PROCESS, MISSING DATA';
        echo '<br>Quiz ID: '.$_REQUEST['sQuiz'];
        echo '<br>Department Assigned: '.$_REQUEST['dName'];        
    }else if($_REQUEST['sQuiz'] == 'ALL'){
         $dept = $_REQUEST['dName'];
         if (empty($_REQUEST['sort'])){
             $_REQUEST['sort'] = 'QUIZ_NAME';
         }
         $sql = "SELECT ID, USERNAME FROM USERLIST WHERE DEPARTMENT = :1 AND ACTIVE = 'Y'";
         $results = $db->ExecSQL($sql, $dept);
         foreach ($results as $d){
             $uName = $d['USERNAME'];
             $name = $d['ID'];
            $sql = "SELECT 
                QUIZ_NAME,
                CLIENT_GROUP,
                MIN_SCORE,
                SCORE,
                PASS,
                ATTEMPTS,
                TRUNC(DATE_TAKEN) DATE_TAKEN,
                TO_CHAR(DATE_TAKEN, 'HH:MM:SS AM') TIME_TAKEN
                FROM QUIZ_MASTER, QUIZ_USERS 
                WHERE QID = QUIZID AND USERID =:1 
                ORDER BY :2";
            $results = $db->ExecSQL($sql,array($d['ID'], $_REQUEST['sort']));
            
            $quizResults = "<div>Test Results for: ".$uName."<br>User ID: ".$name." </div><div class='container' style='max-height:30em; overflow-y:scroll;'>";
            $quiz_results .= "Test Results for: ".$uName."\nUser ID: ".$name."\r\n";
            $quizResults .= "<table class='table table-bordered table-striped table-light' style='width:100%;'>
                <thead class='thead-light'><tr>";
        /* $quizResults .= "
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=QUIZ_NAME&user={$uName}'>QUIZ NAME</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=CLIENT_GROUP&user={$uName}'>CLIENT</th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=MIN_SCORE&user={$uName}'>MIN SCORE</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=SCORE&user={$uName}'>SCORE</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=PASS&user={$uName}'>PASS</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=ATTEMPTS&user={$uName}'>ATTEMPTS</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=DATE_TAKEN&user={$uName}'>DATE QUIZ TAKEN</a></th>";/** */
            
            $quizResults .= "
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=QUIZ_NAME&user={$uName}'>QUIZ NAME</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=CLIENT_GROUP&user={$uName}'>CLIENT</th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=SCORE&user={$uName}'>SCORE</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=PASS&user={$uName}'>PASS</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=DATE_TAKEN&user={$uName}'>DATE QUIZ TAKEN</a></th>
                        <th><a href='".$_SERVER['PHP_SELF'].$_SERVER[' QUERY_STRING']."&sort=TIME_TAKEN&user={$uName}'>TIME QUIZ TAKEN</a></th>";
            
            foreach ($results as $r){
                $qName = $r['QUIZ_NAME'];
                $client = $r['CLIENT_GROUP'];
                $min = $r['MIN_SCORE'];
                $score = $r['SCORE'];
                $pass = $r['PASS'];
                $attempts = $r['ATTEMPTS'];          
                $dateTaken = $r['DATE_TAKEN'];
                $timeTaken = $r['TIME_TAKEN'];
                if (!empty($score)){
                    // $quizResults .= "<tr><td>".$qName."</td><td>".$client."</td><td>".$min."</td><td>".$score."</td><td>".$pass."</td><td>".$attempts."</td><td>".$dateTaken."</td></tr>";          
                        $quizResults .= "<tr><td>".$qName."</td><td>".$client."</td><td>".$score."</td><td>".$pass."</td><td>".$dateTaken."</td><td>".$timeTaken."</td></tr>";    
                        //$quiz_results .= $qName."\n".$client."\n".$min."\n".$score."\n".$pass."\t".$attempts."\t".$dateTaken."\n";
                        $quiz_results .= "\r\nQuiz Name: ".$qName."
                                        \nClient: ".$client."
                                        \nSCORE: ".$score."%
                                        \nPASS: ".$pass." 
                                        \nDate Taken: ".$dateTaken."
                                        \nTime Taken: ".$timeTaken."\r\n\n";                        
                }                 
            }
            $quizResults .= "</table></div>";
            echo $quizResults;
            
        }
        fwrite($fout, $quiz_results);
            fclose($fout);
            echo "<div style='padding-left:65%; margin:1em;'>               
                    <span> <a href=\".\\QuizReceipts\\".$filename."\" class='btn btn-small btn-primary' download>PRINT RESULTS</a></span>
                    <!--<span> <a href='http://cs_webserver/oracle1/trainingdocs.php' class='btn btn-warning' id='qCloser'>Close Quiz</a></span>-->   
                    </div>
                    ";
    }else{
        $dept = $_REQUEST['dName'];
        $quizID = $_REQUEST['sQuiz'];

        $sql = "SELECT ID, USERNAME FROM USERLIST WHERE DEPARTMENT = :1 AND ACTIVE = 'Y'";
         $results = $db->ExecSQL($sql, $dept);
         foreach ($results as $d){
            $uName = $d['USERNAME'];
            $sql = 'SELECT QUIZ_NAME, MIN_SCORE FROM QUIZ_MASTER WHERE QID =:1';
            $results = $db->ExecSQL($sql,$quizID);
            foreach ($results as $r){
                $qName = $r['QUIZ_NAME'];
                $min = $r['MIN_SCORE'];
            }
            $name = $d['ID'];
            global $attempts;
            $sql = "SELECT SCORE, PASS, ATTEMPTS, DATE_TAKEN, TO_CHAR(DATE_TAKEN, 'HH:MM:SS AM') TIME_TAKEN FROM QUIZ_USERS WHERE USERID=:1 AND QUIZID=:2";        
            $results = $db->ExecSQL($sql,array($name,$quizID));
            foreach ($results as $r){
                $attempts = $r['ATTEMPTS'];
                $score = $r['SCORE'];
                $pass = $r['PASS'];
                $dateTaken = $r['DATE_TAKEN'];
                $timeTaken = $r['TIME_TAKEN'];

            }/** */

            $sql = 'SELECT * FROM QUIZ_QUESTIONS WHERE QUIZ_ID =:1 ORDER BY QUESTION_ID';
            $results = $db->ExecSQL($sql,$quizID);

            
            if(!empty($score)){
                //SHOW RESULTS HERE
                echo "        
                    <div class=\"container\">                    
                    </div>
                    <hr>                
                    <div style='padding-left:25em; text-align:left;'>

                    <br>User ID: ".$name."
                    <br>User: ".$uName."
                    <br>Date Taken: ".$dateTaken."
                    <br>Time Taken: ".$timeTaken."              
                    <br>Quiz Name: ".$qName."<br>
                    ".$uName."'S SCORE: ".$score."%<br><br>";
                $quiz_results .= "
                                \nUser: ".$uName."
                                \nDate Taken: ".$dateTaken."
                                \nTime Taken: ".$dateTaken."
                                \nQuiz Name: ".$qName."                        
                                \n".$uName."'S SCORE: ".$score."%\n\n";
                
                if ($pass == 'Y'){
                    echo "<b>Congratulations, you've passed!</b><br>";
                    echo "</div>";
                    $quiz_results .= "\nCongratulations, you've passed!\n";
                    //$opStr = ob_get_contents();
                    //$opStr =ob_get_flush();
                    //file_put_contents($filename, $opStr);
                    
                }else{
                    echo "<div>Unfortunately the minimum to pass was ".$min."%. You will need to retake this quiz.</div><br><br>
                    <span> <a href='http://cs_webserver/oracle1/trainingdocs.php' class='btn btn-danger'>Training Docs</a></span>
                    </div>";
                }
            }
        
        }//ENDS FOREACH FOR D
        fwrite($fout, $quiz_results);
                fclose($fout);            
                echo "<div style='padding-left:65%; margin:1em;'>    
                    <span><!--<a href='' class=\"btn btn-primary\">Take Another Quiz</a>--></span>
                    <!--<span><button onclick=\"window.open('', '_self', ''); window.close();\" class='btn btn-warning'>Close Quiz</button></span>      -->
                    <span> <a href=\".\\QuizReceipts\\".$filename."\" class='btn btn-small btn-primary' download>PRINT RESULTS</a></span>
                    <!-- <span> <a href='http://cs_webserver/oracle1/trainingdocs.php' class='btn btn-warning' id='qCloser'>Close Quiz</a></span>   -->
                    </div>
                    ";


    }//ENDS MAIN ELSE STATEMENT 
}
    echo "</center><!-- END formdiv-->";

//***********************************************END DEPT RESULTS ****************************************************************************** */
/*
################################################################################################################################################
*/


/*
################################################################################################################################################
*/
//***********************************************MENU FOR QUIZ **************************************************************************** */
function Menu(){
    global $db;
    global $user;
    echo "<div id='formdiv' style='width:60%;'>"; 
    $sql = "SELECT USERNAME, ID
            FROM USERLIST
            WHERE ACTIVE = 'Y' AND DEPARTMENT IS NOT NULL 
            ORDER BY USERNAME";
    $result = $db->ExecSQL($sql);
    $userselect = '';
    global $uID;
    foreach ($result as $r){
        $uName = $r['USERNAME'];
        $uID = $r['ID'];
        $dept = $r['DEPARTMENT'];
        $userselect .= "<option value='".$uID."'>".$uName."</option>";
    }

    $sql = "SELECT DEPARTMENT
    FROM USERLIST
    WHERE  DEPARTMENT IS NOT NULL
    GROUP BY DEPARTMENT 
    ORDER BY DEPARTMENT"; 
    $result = $db->ExecSQL($sql);
    $deptselect = '';
    global $dID;
    foreach ($result as $r){
        $dept = $r['DEPARTMENT'];
        
        $deptselect .= "<option value='".$dept."'>".$dept."</option>";
    }
    


    echo "    
    <form method='POST' action=''>
    <!--<div align=center colspan='2' class='container'><h3></h3></div>-->
    
			<div class=\"container\">
				<h3>
                    CHOOSE QUIZ
				</h3>
			</div>
		               
    <div>
        ";        
        echo "<table class='table' style='width:100%;'>";
             
        if($user->department == "IT DEPRTMENT" || $user->department == "HUMAN RESORCES" || $user->manager == 23 || $db->user =='MICHELLED' || $db->user == 'JVENDEGNA20' || $db->user == 'MSELIP'){
            $userchoice = 
            "<tr style='height:3em;'><td align=center width=50%>SELECT USER:</td>
                <td align=center width=50%>
                    <select id='userDD' name='uName'>
                        <option value='null'>- Select -</option>
                        ".$userselect."
                    </select>
                </td>
            </tr>
            <tr style='height:3em;'><td align=center width=50%><b>OR</b> SELECT DEPARTMENT:</td>
                <td align=center width=50%>
                    <select id='deptselect' name='dName'>
                        <option value='null'>- Select -</option>
                        ".$deptselect."
                    </select>
                </td>
            </tr>
            <tr style='height:3em;'><td align=center width=50%>SELECT QUIZ:</td>
                <td align=center width=50%>
                    <select id='quizDD' name='sQuiz' onchange='showBtn();'>
                    <!--<option value='null'>- Select -</option>  -->          
                    </select>
                </td>
            </tr>
         ";
        }else{
            $uID = $user->id;
            $sql="SELECT QID, QUIZ_NAME, CLIENT_GROUP
                FROM QUIZ_MASTER INNER JOIN QUIZ_USERS ON (QID = QUIZID)
                WHERE USERID = :1
                AND ATTEMPTS > 0
                ORDER BY CLIENT_GROUP";                
            $results = $db->ExecSQL($sql,$uID);
              
            global $quizSelect;
            $quizSelect = ''; 
            
            foreach($results as $r){
                $id = $r['QID'];
                $qName = $r['QUIZ_NAME'];
                $group = $r['CLIENT_GROUP'];
                $quizSelect .= "<option value='ALL'>ALL</option>";
                $quizSelect .= "<option value='".$id."'>".$group." - ".$qName."</option>";
            }           

            $userchoice = "
            
                
            
            <tr style='height:3em;'><td align=center width=50%>SELECT QUIZ:</td>
                <td align=center width=50%>
                <input type='hidden' name='uName' value='".$uID."'>
                    <select  name='sQuiz' onchange='showBtn();'>
                    <!--<option value='null'>- Select -</option>  --> 
                        ".$quizSelect."            
                    </select>
                </td>
            </tr>
         ";
        }
        echo $userchoice;
        echo "
        <table><tr>
        <div id='btnLaunch' style='margin-left:50%; margin-top:3em; white-space:nowrap; /*display:none*/'>
            <span><input type='submit' class='btn btn-primary' value='Display Results'></span>
            <span><button type='reset' class='btn btn-danger' value='Reset'>Cancel</button></span>
        </div> 
         </tr></table>                      
                                
    </table><!-- ENDtable-->
    </div><!--Ends tablediv   -->
    <script>
        function showBtn(){                      
                document.getElementById('btnLaunch').style.display = '';                       
        }
    </script>
    <input type='hidden' name='a' value='results'>    
    </form>
    </div><!-- END formdiv-->
    ";
}
//*********************************************END MENU FOR QUIZ ************************************************************************** */
?>         
         
</BODY>
</HTML>
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

switch( $_REQUEST['a'] ) {
    case 'test':    Test();
                     break;
    case 'results': Results();
                     break;
    default :       Menu();
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
    $filename = "Quiz".$_POST['sQuiz']."_Results_for_".$db->user."_". date("mdY") .".txt";
    $fout = fopen(".\\QuizReceipts\\".$filename, "wt");

     

    $date = date('m/d/Y');
    echo "<center style='margin-bottom:1em;'>";    
    if((empty($_POST['uName'])) || (empty($_POST['sQuiz']))){
        echo 'CANNOT PROCESS, MISSING DATA';
        echo '<br>Quiz ID: '.$_POST['sQuiz'];
        echo '<br>Associate Assigned: '.$_POST['uName'];        
    }else{
        $name = $_POST['uName'];
        $quizID = $_POST['sQuiz'];

        $sql = 'SELECT QUIZ_NAME, MIN_SCORE FROM QUIZ_MASTER WHERE QID =:1';
        $results = $db->ExecSQL($sql,$quizID);
        foreach ($results as $r){
            $qName = $r['QUIZ_NAME'];
            $min = $r['MIN_SCORE'];
        }
        
        global $attempts;
        $sql = 'SELECT ATTEMPTS FROM QUIZ_USERS WHERE USERID=:1 AND QUIZID=:2';        
        $results = $db->ExecSQL($sql,array($name,$quizID));
        foreach ($results as $r){
            $attempts = $r['ATTEMPTS'];
        }/** */

        $sql = 'SELECT * FROM QUIZ_QUESTIONS WHERE QUIZ_ID =:1 ORDER BY QUESTION_ID';
        $results = $db->ExecSQL($sql,$quizID);

        global $numCorrect;
        global $numWrong;
        $numQuestions = count($results);
        $qc = 0;
        $numCorrect = 0;
        $numWrong = 0;
        $answerSheet ='';        
        global $answer;
        
        foreach ($results as $r){            
                $answer = '';                
                $correctAnswers = strtoupper(urldecode($r['ANSWER']));
                $numAnswers = $r['NUM_ANSWERS'];
                $qn = $qc+1;
                $questionID = $_POST['question_'.$qc];
                if(is_array($_REQUEST['answer_'.$qc])){                    
                    foreach($_REQUEST['answer_'.$qc] as $a){                        
                        $answer .= (urldecode($a));                        
                    }                    
                }else{
                    $answer .= $_POST['answer_'.$qc];                    
                }                
                
                if (!empty($_REQUEST['answer_'.$qc])){
                    $answerArray = $_REQUEST['answer_'.$qc];
                }else { $answerArray = '';}

                $answerSheet .= "<tr><td>".$qn.".) Question: ".urldecode($r['QUESTION'])."</td><td>Correct Response(s): ".urldecode($r['ANSWER'])."</td>";
                $alength = count($answerArray);
                if ($alength > 1){
                    $calist = count($_REQUEST['answer_'.$qc]);
                    for ($i = 0; $i < $calist; $i++){                                                
                        $found = strpos( strtoupper(urldecode($correctAnswers)) , strtoupper(urldecode($_REQUEST['answer_'.$qc][$i])) );                            if($found !== false){
                            $resp++;
                         }
                    }
                }else{
                    $found = strpos( $correctAnswers , strtoupper(urldecode($answer)));                   
                    if($found !== false){
                       $resp++;
                    }
                }
                if ($numAnswers == $resp){
                    $numCorrect++;
                    $answerSheet .="<td style='color:green;'>Your Response: ".urldecode($answer)."</td></tr>";                                              
                }else{
                    $numWrong++;
                    $answerSheet .="<td style='color:red;'>Your Response: ".urldecode($answer)."</td></tr>"; 
                }
                $qc++;
                $resp = 0;                
        }
        $attempts++;
        $score = ($numCorrect/$numQuestions)*100;

        if ($score >= $min){
            $pass = 'Y';
        }else{$pass = 'N';}

        $sql = 'UPDATE QUIZ_USERS SET SCORE=:1, PASS=:2, ATTEMPTS=:3, DATE_TAKEN=SYSDATE WHERE USERID=:4 AND QUIZID=:5';
        //echo 'UPDATE QUIZ_USERS SET SCORE='.$score.', PASS='.$pass.', ATTEMPTS='.$attempts.' WHERE USERID='.$name.' AND QUIZID='.$quizID.'<br>';
        $params = array($score,$pass,$attempts,$name,$quizID);
        $db->ExecSQL($sql,$params);        

        //SHOW RESULTS HERE
        echo "        
            <div class=\"container\">
                <h4>
                RESULTS
                </h4>
            </div>
                
            <div style='padding-left:25em; text-align:left;'>
            <br>User: ".$user->fullname."
            <br>Date Taken: ".$date."            
            <br>Quiz Name: ".$qName."<br><br><!--Attempt #: ".$attempts."<br><br>-->Total Number of Questions: ".$numQuestions."<br><br>Number Answered Correctly: ".$numCorrect."
            <br><br>Number Answered Incorrectly: ".$numWrong."<br><br>YOUR SCORE: ".$score."%<br><br>";
        $quiz_results = "RESULTS
                        \nUser: ".$user->fullname."
                        \nDate Taken: ".$date."            
                        \nQuiz Name: ".$qName."
                        \n\nTotal Number of Questions: ".$numQuestions."
                        \n\nNumber Answered Correctly: ".$numCorrect."
                        \n\nNumber Answered Incorrectly: ".$numWrong."
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
                <span> <a href='http://cs_webserver/oracle1/trainingdocs.php' class='btn btn-warning' id='qCloser'>Close Quiz</a></span>   
                </div>
                ";
        }else{
            echo "<div>Unfortunately the minimum to pass was ".$min."%. You will need to retake this quiz.</div><br><br>
            <span> <a href='http://cs_webserver/oracle1/trainingdocs.php' class='btn btn-danger'>Training Docs</a></span>
            </div>";
        }
        echo "<table style='background-color:#EDFAFC; width:80%;' id='qtable' class='table table-bordered table-striped'><tr><td colspan='3' style='text-align:center'><h4>ANSWER KEY<h4></td></tr>".$answerSheet."</table>";
        


    }//ENDS MAIN ELSE STATEMENT 
    echo "</center><!-- END formdiv-->";

    
    
      
    
}//ENDS FUNCTION


//***********************************************END RESULTS ****************************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************TEST USER ******************************************************************************** */

function Test(){
    global $db;
    global $user;
    if(empty($noback)){
        $noback = true;
    }else{
        header("Expires: Sun, 1 Jan 2017 05:00:00 GMT");
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
        $page = $_SERVER['PHP_SELF'];
        header("url=$page");
    }
/** */
    echo '<center>';
    if((empty($_POST['uName'])) || (empty($_POST['sQuiz']))){
        echo 'CANNOT PROCESS, MISSING DATA';
        echo '<br>Quiz ID: '.$_POST['sQuiz'];
        echo '<br>Associate Assigned: '.$_POST['uName'];        
    }else{
        $name = $_POST['uName'];
        $quizID = $_POST['sQuiz'];
        
        $sql = 'SELECT QUESTION_ID,QUESTION,MULT_ANS,CHOICE1,CHOICE2,CHOICE3,CHOICE4,CHOICE5,CHOICE6,CHOICE7,CHOICE8,CHOICE9,CHOICE10,CHOICE11,CHOICE12, QUIZ_NAME
                FROM QUIZ_QUESTIONS, QUIZ_MASTER
                WHERE QUIZ_ID =:1
                AND QID = QUIZ_ID
                ORDER BY QUESTION_ID';
        $result = $db->ExecSQL($sql,$quizID);
        $quizName = $result[0]['QUIZ_NAME'];
        if(!$result){
                echo '<br>ERROR, QUESTIONS COULD NOT BE RETRIEVED';
                echo '<br>UserID is '.$name.'<br>Quiz ID is '.$quizID.'<br>';
            }else{            
            $questionArray = array();
            global $questionArray;
           $answers ='';
           $qNum = 0;
           $qCt = 1;
           $cr = count($result);
           /*
           echo "<div>Result Count is: ".$cr."</div>";
           echo"
            <style>           
              input[type=\"radio\"] {
                  display: inline-block;
                  width: 15px;
                  vertical-align: top;
              }              
              .label-text {
                  display: inline-block;
                  width: 30em;
                  word-break: break-all;
              }
            </style>            
            <form method='POST' class=\"field\" action=''>
                    <!--<div align=center colspan='2' class='container'><h3>Begin!</h3></div>-->                   
                            <div class=\"container\">
                                <h4>
                                    Begin!
                                </h4>
                            </div>                                  
                    <div>                   
                        <table style='background-color:#EDFAFC; width:80%;' id='qtable' class='table table-bordered table-striped'>";
                        /** */
            foreach($result as $r){
                
                if(!empty($r['CHOICE12'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],16,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4'],$r['CHOICE5'],$r['CHOICE6'],$r['CHOICE7'],$r['CHOICE8'],$r['CHOICE9'],$r['CHOICE10'],$r['CHOICE11'],$r['CHOICE12']);
                }else if(!empty($r['CHOICE11'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],15,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4'],$r['CHOICE5'],$r['CHOICE6'],$r['CHOICE7'],$r['CHOICE8'],$r['CHOICE9'],$r['CHOICE10'],$r['CHOICE11']);
                }else if(!empty($r['CHOICE10'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],14,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4'],$r['CHOICE5'],$r['CHOICE6'],$r['CHOICE7'],$r['CHOICE8'],$r['CHOICE9'],$r['CHOICE10']);
                }else if(!empty($r['CHOICE9'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],13,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4'],$r['CHOICE5'],$r['CHOICE6'],$r['CHOICE7'],$r['CHOICE8'],$r['CHOICE9']);
                }else if(!empty($r['CHOICE8'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],12,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4'],$r['CHOICE5'],$r['CHOICE6'],$r['CHOICE7'],$r['CHOICE8']);
                }else if(!empty($r['CHOICE7'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],11,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4'],$r['CHOICE5'],$r['CHOICE6'],$r['CHOICE7']);
                }else if(!empty($r['CHOICE6'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],10,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4'],$r['CHOICE5'],$r['CHOICE6']);
                }else if(!empty($r['CHOICE5'])){
                $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],9,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4'],$r['CHOICE5']);
                }else if(!empty($r['CHOICE4'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],8,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4']);                  
                }else if(!empty($r['CHOICE3'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],7,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3']);                    
                }else {
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],6,$r['CHOICE1'],$r['CHOICE2']);                    
                }
            }
            /** */
            //shuffle($questionArray);               
            $subArray = array();
            global $subArray;
            echo"
            <style>           
              input[type=\"radio\"] {
                  display: inline-block;
                  width: 15px;
                  vertical-align: top;
              }              
              .label-text {
                  display: inline-block;
                  width: 30em;
                  word-break: break-all;
              }
            </style>
            <div style='text-align:left; width:30%; margin:.5em auto; font-weight:600; border:thin solid #b5b1b1; padding-left:.5em' nowrap>
                Quiz: {$quizName}<br>User: {$user->fullname}
            </div>            
            <form method='POST' class=\"field\" action=''>
                    <!--<div align=center colspan='2' class='container'><h3>Begin!</h3></div>-->                   
                            <div class=\"container\">
                                <h4>
                                    Begin!
                                </h4>
                            </div>                                  
                    <div>                   
                        <table style='background-color:#EDFAFC; width:80%;' id='qtable' class='table table-bordered table-striped'>";
              /*
            if ($question == 'success'){
                $question = 'warning';
            }else{
                $question = 'success';
            }
            echo "<tr align='left'><td class='".$question."'><input type='hidden' name='question_".$qNum."' value='".$r['QUESTION_ID']."'>Question ".($qCt).": <br>".urldecode($r['QUESTION'])."</td><td class='".$question."' class=\"control\" align='left'> ";

            if($r['MULT_ANS'] =='Y'){
                $itype = 'checkbox';
            }else $itype='radio';
            $letter = 'A';
            for($i = 1; $i <= 12; $i++){
                if (empty($r['CHOICE'.$i])){
                    break;
                }
                $answers .= " 
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$qNum."[]' id='cb".$i."_".$qNum."' style='vertical-align:middle;' value='".$ch."'></div>
                        <div style=' display:table-cell; overflow:auto;'>".$letter.")&nbsp</div>
                        <div style=' display:table-cell; overflow:auto;'>".urldecode($r['CHOICE'.$i])."</div>
                        </div>                     
                        ";
                $letter++;
            }            
            if (empty($r['CHOICE1'])){
                echo '<br>Error: Questions and Answers not loading<br>';
            }else{echo $answers;}
            echo "</td></tr>";
            $answers = '';
            $qNum++;
            $qCt++;
        }
             /** */         
            $countqArr = count($questionArray);   
            for($i = 0; $i < $countqArr; $i++ ){
                if(($i%2)==0){
                    $question = 'success';                    
                }else {$question = 'warning';}
                echo "<tr align='left'><td class='".$question."'><input type='hidden' name='question_".$i."' value='".$questionArray[$i][0]."'>Question ".($i+1).": <br>".urldecode($questionArray[$i][1])."</td>";

                if (strtoupper($questionArray[$i][4])== 'TRUE'){
                    $ch_A = strtoupper($questionArray[$i][4]);
                    $ch_B = strtoupper($questionArray[$i][5]);
                    $answers = "<td class='".$question."' align='left'><input type='radio' style='vertical-align:middle;' name='answer_".$i."' value='".$ch_A."' required>A)&nbsp".urldecode($ch_A)."<br><input type='radio' name='answer_".$i."' style='vertical-align:middle;' value='".$ch_B."' required>B)&nbsp".urldecode($ch_B)."&nbsp</td></tr>";
                }else if(strtoupper($questionArray[$i][5])== 'TRUE'){
                    $ch_A = strtoupper($questionArray[$i][5]);
                    $ch_B = strtoupper($questionArray[$i][4]);
                    $answers = "<td class='".$question."' align='left'><input type='radio' style='vertical-align:middle;' name='answer_".$i."' value='".$ch_A."' required>A)&nbsp ".urldecode($ch_A)."<br><input type='radio' style='vertical-align:middle;' name='answer_".$i."' value='".$ch_B."' required>B)&nbsp".urldecode($ch_B)."&nbsp</td></tr>";
                }else{
                   // echo "<tr><td>".count($questionArray[$i])."</td></tr>";
                    $nct = count($questionArray[$i]);
                    //echo "<div> NCT is ".$nct."</div>";
                    $qcount = $questionArray[$i][3];
                    for ($j = 4; $j < $qcount; $j++){
                        $subArray[] = $questionArray[$i][$j];
                    }
                    if($questionArray[$i][2] =='Y'){
                        $itype = 'checkbox';
                    }else {$itype='radio';}
                    //echo "<div>Sub Array is: <br>";
                   // print_r($subArray);
                   // echo "<br></div>";
                    if($qcount == 16){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];
                        $ch_E = $subArray[4];
                        $ch_F = $subArray[5];
                        $ch_G = $subArray[6];
                        $ch_H = $subArray[7];
                        $ch_I = $subArray[8];
                        $ch_J = $subArray[9];
                        $ch_K = $subArray[10]; 
                        $ch_L = $subArray[11];                       
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb5_".$i."' style='vertical-align:middle;' value='".$ch_E."'></div>
                        <div style=' display:table-cell; overflow:auto;'>E)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_E)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb6_".$i."' style='vertical-align:middle;' value='".$ch_F."'></div>
                        <div style=' display:table-cell; overflow:auto;'>F)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_F)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb7_".$i."' style='vertical-align:middle;' value='".$ch_G."'></div>
                        <div style=' display:table-cell; overflow:auto;'>G)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_G)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb8_".$i."' style='vertical-align:middle;' value='".$ch_H."'></div>
                        <div style=' display:table-cell; overflow:auto;'>H)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_H)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb9_".$i."' style='vertical-align:middle;' value='".$ch_I."'></div>
                        <div style=' display:table-cell; overflow:auto;'>I)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_I)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb10_".$i."' style='vertical-align:middle;' value='".$ch_J."'></div>
                        <div style=' display:table-cell; overflow:auto;'>J)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_J)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb11_".$i."' style='vertical-align:middle;' value='".$ch_K."'></div>
                        <div style=' display:table-cell; overflow:auto;'>K)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_K)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb12_".$i."' style='vertical-align:middle;' value='".$ch_L."'></div>
                        <div style=' display:table-cell; overflow:auto;'>L)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_L)."&nbsp</div>
                        </div>                     
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 15){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];
                        $ch_E = $subArray[4];
                        $ch_F = $subArray[5];
                        $ch_G = $subArray[6];
                        $ch_H = $subArray[7];
                        $ch_I = $subArray[8];
                        $ch_J = $subArray[9];
                        $ch_K = $subArray[10];                      
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb5_".$i."' style='vertical-align:middle;' value='".$ch_E."'></div>
                        <div style=' display:table-cell; overflow:auto;'>E)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_E)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb6_".$i."' style='vertical-align:middle;' value='".$ch_F."'></div>
                        <div style=' display:table-cell; overflow:auto;'>F)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_F)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb7_".$i."' style='vertical-align:middle;' value='".$ch_G."'></div>
                        <div style=' display:table-cell; overflow:auto;'>G)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_G)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb8_".$i."' style='vertical-align:middle;' value='".$ch_H."'></div>
                        <div style=' display:table-cell; overflow:auto;'>H)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_H)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb9_".$i."' style='vertical-align:middle;' value='".$ch_I."'></div>
                        <div style=' display:table-cell; overflow:auto;'>I)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_I)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb10_".$i."' style='vertical-align:middle;' value='".$ch_J."'></div>
                        <div style=' display:table-cell; overflow:auto;'>J)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_J)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb11_".$i."' style='vertical-align:middle;' value='".$ch_K."'></div>
                        <div style=' display:table-cell; overflow:auto;'>K)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_K)."&nbsp</div>
                        </div>                     
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 14){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];
                        $ch_E = $subArray[4];
                        $ch_F = $subArray[5];
                        $ch_G = $subArray[6];
                        $ch_H = $subArray[7];
                        $ch_I = $subArray[8];
                        $ch_J = $subArray[9];                      
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb5_".$i."' style='vertical-align:middle;' value='".$ch_E."'></div>
                        <div style=' display:table-cell; overflow:auto;'>E)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_E)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb6_".$i."' style='vertical-align:middle;' value='".$ch_F."'></div>
                        <div style=' display:table-cell; overflow:auto;'>F)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_F)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb7_".$i."' style='vertical-align:middle;' value='".$ch_G."'></div>
                        <div style=' display:table-cell; overflow:auto;'>G)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_G)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb8_".$i."' style='vertical-align:middle;' value='".$ch_H."'></div>
                        <div style=' display:table-cell; overflow:auto;'>H)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_H)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb9_".$i."' style='vertical-align:middle;' value='".$ch_I."'></div>
                        <div style=' display:table-cell; overflow:auto;'>I)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_I)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb10_".$i."' style='vertical-align:middle;' value='".$ch_J."'></div>
                        <div style=' display:table-cell; overflow:auto;'>J)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_J)."&nbsp</div>
                        </div>                     
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 13){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];                      
                        $ch_E = $subArray[4];
                        $ch_F = $subArray[5];
                        $ch_G = $subArray[6];
                        $ch_H = $subArray[7];
                        $ch_I = $subArray[8];                     
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb5_".$i."' style='vertical-align:middle;' value='".$ch_E."'></div>
                        <div style=' display:table-cell; overflow:auto;'>E)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_E)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb6_".$i."' style='vertical-align:middle;' value='".$ch_F."'></div>
                        <div style=' display:table-cell; overflow:auto;'>F)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_F)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb7_".$i."' style='vertical-align:middle;' value='".$ch_G."'></div>
                        <div style=' display:table-cell; overflow:auto;'>G)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_G)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb8_".$i."' style='vertical-align:middle;' value='".$ch_H."'></div>
                        <div style=' display:table-cell; overflow:auto;'>H)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_H)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb9_".$i."' style='vertical-align:middle;' value='".$ch_I."'></div>
                        <div style=' display:table-cell; overflow:auto;'>I)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_I)."&nbsp</div>
                        </div>                     
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 12){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];                      
                        $ch_E = $subArray[4];
                        $ch_F = $subArray[5];
                        $ch_G = $subArray[6];
                        $ch_H = $subArray[7];                     
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb5_".$i."' style='vertical-align:middle;' value='".$ch_E."'></div>
                        <div style=' display:table-cell; overflow:auto;'>E)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_E)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb6_".$i."' style='vertical-align:middle;' value='".$ch_F."'></div>
                        <div style=' display:table-cell; overflow:auto;'>F)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_F)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb7_".$i."' style='vertical-align:middle;' value='".$ch_G."'></div>
                        <div style=' display:table-cell; overflow:auto;'>G)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_G)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb8_".$i."' style='vertical-align:middle;' value='".$ch_H."'></div>
                        <div style=' display:table-cell; overflow:auto;'>H)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_H)."&nbsp</div>
                        </div>                     
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 11){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];                      
                        $ch_E = $subArray[4];
                        $ch_F = $subArray[5];
                        $ch_G = $subArray[6];                     
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb5_".$i."' style='vertical-align:middle;' value='".$ch_E."'></div>
                        <div style=' display:table-cell; overflow:auto;'>E)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_E)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb6_".$i."' style='vertical-align:middle;' value='".$ch_F."'></div>
                        <div style=' display:table-cell; overflow:auto;'>F)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_F)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb7_".$i."' style='vertical-align:middle;' value='".$ch_G."'></div>
                        <div style=' display:table-cell; overflow:auto;'>G)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_G)."&nbsp</div>
                        </div>                    
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 10){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];                      
                        $ch_E = $subArray[4];
                        $ch_F = $subArray[5];                    
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb5_".$i."' style='vertical-align:middle;' value='".$ch_E."'></div>
                        <div style=' display:table-cell; overflow:auto;'>E)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_E)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb6_".$i."' style='vertical-align:middle;' value='".$ch_F."'></div>
                        <div style=' display:table-cell; overflow:auto;'>F)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_F)."&nbsp</div>
                        </div>                     
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 9){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];                      
                        $ch_E = $subArray[4];                    
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb5_".$i."' style='vertical-align:middle;' value='".$ch_E."'></div>
                        <div style=' display:table-cell; overflow:auto;'>E)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_E)."&nbsp</div>
                        </div>                     
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 8){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];                      
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell; overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell; overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell; overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'></div>
                        <div style=' display:table-cell; overflow:auto;'>D)&nbsp</div>
                        <div style=' display:table-cell; overflow:auto;'>".urldecode($ch_D)."&nbsp</div>
                        </div>                     
                        </td></tr>";
                        $subArray = array();
                        
                    }else if($qcount == 7){
                       // shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];

                        $answers = "
                        <td class='".$question."' class=\"control\"  align='left'>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'></div>
                        <div style=' display:table-cell; overflow:auto;'>C)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_C)."&nbsp</div>
                        </div>
                        </td></tr>";
                        $subArray = array();

                    }else{
                       // shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];

                        $answers = "
                        <td class='".$question."' class=\"control\"  align='left'>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' style='vertical-align:middle;' value='".$ch_A."'></div>
                        <div style=' display:table-cell; overflow:auto;'>A)&nbsp</div>
                        <div style=' display:table-cell; '>".urldecode($ch_A)."&nbsp</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top;  white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' style='vertical-align:middle;' value='".$ch_B."'></div>
                        <div style=' display:table-cell; overflow:auto;'>B)&nbsp</div>
                        <div style=' display:table-cell;  overflow:auto;'>".urldecode($ch_B)."&nbsp</div>
                        </div> 
                        </td></tr>";
                        $subArray = array();
                    }

                 }
                if (empty($answers)){
                    echo '<br>Error: Questions and Answers not loading or shuffling<br>';
                }else{echo $answers;}
            }
        /** */
            
            echo "</table><!-- ENDtable-->
                </div><!--Ends qtable   -->
                <input type='hidden' name='uName' value='".$name."'> 
                <input type='hidden' name='sQuiz' value='".$quizID."'> 
                <input type='hidden' name='a' value='results'>
                <div style='margin-left:70%; margin-top:.5em;'>
                <span><input type='submit' class='btn btn-primary' value='Confirm Answers'></span>
                <span><button type='reset' class='btn btn-danger' value='Reset'>Cancel</button></span>
                </div>
                </form>";   
        
        }//ENDS RESULTS ELSE
    }//ENDS ELSE THAT CHECKS FOR NAME AND QUIZ ID
echo "</center>";/** */

}//ENDS function PROCESS
//*********************************************END TEST USER ****************************************************************************** */
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
        if($user->department == "IT DEPRTMENT" || $user->department == "HUMAN RESORCES" || $user->manager == 23){
            $userchoice = 
            "<tr style='height:3em;'><td align=center width=50%>SELECT USER:</td>
                <td align=center width=50%>
                    <select id='userDD' name='uName'>
                        <option value='null'>- Select -</option>
                        ".$userselect."
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
                AND NVL(PASS, 'Q') != 'Y'
                ORDER BY CLIENT_GROUP";                
            $results = $db->ExecSQL($sql,$uID);
              
            global $quizSelect;
            $quizSelect = ''; 
            
            foreach($results as $r){
                $id = $r['QID'];
                $qName = $r['QUIZ_NAME'];
                $group = $r['CLIENT_GROUP'];
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
            <span><input type='submit' class='btn btn-primary' value='Launch Test'></span>
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
    <input type='hidden' name='a' value='test'>    
    </form>
    </div><!-- END formdiv-->
    ";
}
//*********************************************END MENU FOR QUIZ ************************************************************************** */
?>         
         
</BODY>
</HTML>
<!DOCTYPE HTML>
<HEAD>
<link rel='stylesheet' type='text/css' href='quiz_style.css'>
<link rel='stylesheet' type='text/css' href='Bulma_APR2018.css'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>


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
    /*
    $("input:checkbox").on('click', function() {
        // in the handler, 'this' refers to the box clicked on
        var $box = $(this);
        if ($box.is(":checked")) {
            // the name of the box is retrieved using the .attr() method
            // as it is assumed and expected to be immutable
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            // the checked state of the group/box on the other hand will change
            // and the current value is retrieved using .prop() method
            $(group).prop("checked", false);
            $box.prop("checked", true);
        } else {
            $box.prop("checked", false);
        }
    }); */
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
    echo "<center>";    
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
        }

        $sql = 'SELECT * FROM QUIZ_QUESTIONS WHERE QUIZ_ID =:1';
        $results = $db->ExecSQL($sql,$quizID);

        global $numCorrect;
        global $numWrong;
        $numQuestions = count($results);
        //echo'<br><br>NumQuestions:'.$numQuestions.'<br><br>';
        $numCorrect = 0;
        $numWrong = 0;
        $answerSheet ='';
        global $answer;
        
        foreach ($results as $r){
            //for($i = 0; $i < $numQuestions; $i++){
                $answer = '';                
                $correctAnswers = strtoupper(urldecode($r['ANSWER']));
                
                for ($i=0;$i < $numQuestions; $i++){
                    $questionID = $_POST['question_'.$i];
                    if (!empty($_REQUEST['answer_'.$i])){
                        $answerArray = $_REQUEST['answer_'.$i];
                    }else { $answerArray = '';}
                   
                if($r['QUESTION_ID'] == $questionID){
                    $answerSheet .= "<tr><td>Question: ".urldecode($r['QUESTION'])."</td><td>Correct Response(s): ".urldecode($r['ANSWER'])."</td>";
                    global $resp;
                    $resp = 0;
                    $alength = count($answerArray);                   
                    for ($j=0;$j<$alength;$j++){
                        $answer  .=  " ".$answerArray[$j];
                        //echo "<SCRIPT>alert('".$answer."');</SCRIPT>";
                        if(strpos( $correctAnswers , strtoupper(urldecode($answerArray[$j]))) !== false){
                            $resp++;
                        }
                    }                                        
                    if ($alength == $resp){
                        $numCorrect++;
                        $answerSheet .="<td style='color:green;'>Your Response: ".urldecode($answer)."</td></tr>";                                              
                    }else{
                        $numWrong++;
                        $answerSheet .="<td style='color:red;'>Your Response: ".urldecode($answer)."</td></tr>"; 
                    }
                    $resp=0;                    
                }//else{ echo 'rQUESTIONID IS'.$r['QUESTION_ID'].'<br>$QUESTIONID is '.$questionID.'<br><br>';}
            }
        }
        $attempts++;
        $score = ($numCorrect/$numQuestions)*100;

        if ($score >= $min){
            $pass = 'Y';
        }else{$pass = 'N';}

        $sql = 'UPDATE QUIZ_USERS SET SCORE=:1, PASS=:2, ATTEMPTS=:3 WHERE USERID=:4 AND QUIZID=:5';
        //echo 'UPDATE QUIZ_USERS SET SCORE='.$score.', PASS='.$pass.', ATTEMPTS='.$attempts.' WHERE USERID='.$name.' AND QUIZID='.$quizID.'<br>';
        $params = array($score,$pass,$attempts,$name,$quizID);
        $db->ExecSQL($sql,$params);        

        //SHOW RESULTS HERE
        echo "
        <section class='herois-primary is-medium' style='padding:.5em;'>
        <div class=\"hero-body\" style='padding:0;'>
            <div class=\"container\">
                <h1 class=\"title is-4\">
                RESULTS
                </h1>
            </div>
        </div>
    </section>         
            <div style='padding-left:25em; text-align:left;'>            
            <br>Quiz Name: ".$qName."<br><br><!--Attempt #: ".$attempts."<br><br>-->Total Number of Questions: ".$numQuestions."<br><br>Number Answered Correctly: ".$numCorrect."
            <br><br>Number Answered Incorrectly: ".$numWrong."<br><br>YOUR SCORE: ".$score."%<br><br>";
        if ($pass == 'Y'){
            echo "<b>Congratulations, you've passed!</b><br>";
        }else{
            echo "Unfortunately the minimum to pass was ".$min."%. You will need to retake this quiz.<br>";
        }
        echo "</div>
        <div style='padding-left:65%; margin:1em;'>    
        <span><!--<a href='' class=\"button is-info\">Take Another Quiz</a>--></span>
        <!--<span><button onclick=\"window.open('', '_self', ''); window.close();\" class='button is-info'>Close Quiz</button></span>      -->
        <span> <a href='http://cs_webserver/oracle1/index.php' class='button is-info'>Close Quiz</a></span>   
        </div>
        <table style='background-color:#EDFAFC; width:80%;' id='qtable' class='table is-bordered is-striped'><tr><td colspan='3' style='text-align:center'><h4>ANSWER KEY<h4></td></tr>".$answerSheet;
        echo "</table>       
        
        ";


    }//ENDS MAIN ELSE STATEMENT 
    echo "</center<!-- END formdiv-->";   
}//ENDS FUNCTION


//***********************************************END RESULTS ****************************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************TEST USER ******************************************************************************** */

function Test(){
    global $db;
    
/** */
    echo '<center>';
    if((empty($_POST['uName'])) || (empty($_POST['sQuiz']))){
        echo 'CANNOT PROCESS, MISSING DATA';
        echo '<br>Quiz ID: '.$_POST['sQuiz'];
        echo '<br>Associate Assigned: '.$_POST['uName'];        
    }else{
        $name = $_POST['uName'];
        $quizID = $_POST['sQuiz'];
        
        $sql = 'SELECT QUESTION_ID,QUESTION,MULT_ANS,CHOICE1,CHOICE2,CHOICE3,CHOICE4
                FROM QUIZ_QUESTIONS
                WHERE QUIZ_ID =:1
                ORDER BY QUESTION_ID';
        $result = $db->ExecSQL($sql,$quizID);
        if(!$result){
                echo '<br>ERROR, QUESTIONS COULD NOT BE RETRIEVED';
                echo '<br>UserID is '.$name.'<br>Quiz ID is '.$quizID.'<br>';
            }else{            
            $questionArray = array();
            global $questionArray;
           
            foreach($result as $r){
                
                if (!empty($r['CHOICE4'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],8,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3'],$r['CHOICE4']);                                      
                }else if(!empty($r['CHOICE3'])){
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],7,$r['CHOICE1'],$r['CHOICE2'],$r['CHOICE3']);                    
                }else {
                    $questionArray[] = array($r['QUESTION_ID'],$r['QUESTION'],$r['MULT_ANS'],6,$r['CHOICE1'],$r['CHOICE2']);                    
                }
            }
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
            <form method='POST' class=\"field\" action=''>
                    <!--<div align=center colspan='2' class='container'><h3>Begin!</h3></div>-->
                    <section class='herois-primary is-medium' style='padding:.5em;'>
                        <div class=\"hero-body\" style='padding:0;'>
                            <div class=\"container\">
                                <h1 class=\"title is-4\">
                                    Begin!
                                </h1>
                            </div>
                        </div>
                    </section>           
                    <div>                   
                        <table style='background-color:#EDFAFC; width:80%;' id='qtable' class='table is-bordered is-striped'>";
            $countqArr = count($questionArray);   
            for($i = 0; $i < $countqArr; $i++ ){
                if(($i%2)==0){
                    $question = 'success';                    
                }else {$question = 'warning';}
                echo "<tr align='left'><td class='".$question."'><input type='hidden' name='question_".$i."' value='".$questionArray[$i][0]."'>Question ".($i+1).": <br>".urldecode($questionArray[$i][1])."</td>";

                if (strtoupper($questionArray[$i][4])== 'TRUE'){
                    $ch_A = strtoupper($questionArray[$i][4]);
                    $ch_B = strtoupper($questionArray[$i][5]);
                    $answers = "<td class='".$question."' align='left'><input type='radio' style='vertical-align:middle;' name='answer_".$i."' value='".$ch_A."' required>A)&nbsp".urldecode($ch_A)."<br><input type='radio' name='answer_".$i."' style='vertical-align:middle;' value='".$ch_B."' required>B)&nbsp".urldecode($ch_B)."</td></tr>";
                }else if(strtoupper($questionArray[$i][5])== 'TRUE'){
                    $ch_A = strtoupper($questionArray[$i][5]);
                    $ch_B = strtoupper($questionArray[$i][4]);
                    $answers = "<td class='".$question."' align='left'><input type='radio' style='vertical-align:middle;' name='answer_".$i."' value='".$ch_A."' required>A)&nbsp ".urldecode($ch_A)."<br><input type='radio' style='vertical-align:middle;' name='answer_".$i."' value='".$ch_B."' required>B)&nbsp".urldecode($ch_B)."</td></tr>";
                }else{
                   // echo "<tr><td>".count($questionArray[$i])."</td></tr>";
                    $nct = count($questionArray[$i]);
                    $qcount = $questionArray[$i][3];
                    for ($j = 4; $j < $qcount; $j++){
                        $subArray[] = $questionArray[$i][$j];
                    }
                    if($questionArray[$i][2] =='Y'){
                        $itype = 'checkbox';
                    }else $itype='radio';

                    if($qcount == 8){
                        //shuffle($subArray);          
                        $ch_A = $subArray[0];
                        $ch_B = $subArray[1];
                        $ch_C = $subArray[2];
                        $ch_D = $subArray[3];                      
                        

                        $answers = "
                        <td class='".$question."' class=\"control\" align='left'>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'>A)&nbsp</div>
                        <div style=' display:table-cell; width:90%; overflow:auto;'>".urldecode($ch_A)."</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'>B)&nbsp</div>
                        <div style=' display:table-cell; width:90%; overflow:auto;'>".urldecode($ch_B)."</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'>C)&nbsp</div>
                        <div style=' display:table-cell; width:90%; overflow:auto;'>".urldecode($ch_C)."</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb4_".$i."' style='vertical-align:middle;' value='".$ch_D."'>D)&nbsp</div>
                        <div style=' display:table-cell; width:90%; overflow:auto;'>".urldecode($ch_D)."</div>
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
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb1_".$i."' style='vertical-align:middle;' value='".$ch_A."'>A)&nbsp</div>
                        <div style=' display:table-cell; width:90%; overflow:auto;'>".urldecode($ch_A)."</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb2_".$i."' style='vertical-align:middle;' value='".$ch_B."'>B)&nbsp</div>
                        <div style=' display:table-cell; width:90%; overflow:auto;'>".urldecode($ch_B)."</div>
                        </div>                        
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' id='cb3_".$i."' style='vertical-align:middle;' value='".$ch_C."'>C)&nbsp</div>
                        <div style=' display:table-cell; width:90%; overflow:auto;'>".urldecode($ch_C)."</div>
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
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' style='vertical-align:middle;' value='".$ch_A."'>A)&nbsp</div>
                        <div style=' display:table-cell; width:90%;'>".urldecode($ch_A)."</div>
                        </div>
                        <div style='table-row'>
                        <div style='display:table-cell; vertical-align:top; width:10%; white-space:nowrap;'>
                        <input type='".$itype."' name='answer_".$i."[]' style='vertical-align:middle;' value='".$ch_B."'>B)&nbsp</div>
                        <div style=' display:table-cell; width:90%; overflow:auto;'>".urldecode($ch_B)."</div>
                        </div> 
                        </td></tr>";
                        $subArray = array();
                    }

                 }
                if (empty($answers)){
                    echo '<br>Error: Questions and Answers not loading or shuffling<br>';
                }else{echo $answers;}
            }
            
            echo "</table><!-- ENDtable-->
                </div><!--Ends qtable   -->
                <input type='hidden' name='uName' value='".$name."'> 
                <input type='hidden' name='sQuiz' value='".$quizID."'> 
                <input type='hidden' name='a' value='results'>
                <div style='margin-left:70%; margin-top:.5em;'>
                <span><input type='submit' class='button is-info' value='Confirm Answers'></span>
                <span><button type='reset' class='button is-info' value='Reset'>Cancel</button></span>
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
    <section class='herois-primary is-medium' style='padding:.5em;'>
		  <div class=\"hero-body\" style='padding:0;'>
			<div class=\"container\">
				<h1 class=\"title is-4\">
                    CHOOSE QUIZ
				</h1>
			</div>
		   </div>
	</section>             
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
            $sql='SELECT QID, QUIZ_NAME, CLIENT_GROUP
                FROM QUIZ_MASTER INNER JOIN QUIZ_USERS ON (QID = QUIZID)
                WHERE USERID =:1
                ORDER BY CLIENT_GROUP';
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
            <span><input type='submit' class='button is-info' value='Launch Test'></span>
            <span><button type='reset' class='button is-info' value='Reset'>Cancel</button></span>
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
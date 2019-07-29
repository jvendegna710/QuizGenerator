<!DOCTYPE HTML>
<HTML>
<HEAD>
<link rel='stylesheet' type='text/css' href='quiz_style.css'>
<link rel='stylesheet' type='text/css' href='Bulma_APR2018.css'>
<TITLE>Create a Quiz</TITLE>

<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
include_once('\\inetpub\\wwwroot\\user.php.inc');

// 30 MAR 2018 The following javascript function and added checkboxes to question creation are added to give possibilities of multiple answers. 


?>

<script>
var answernum ='';
function answerChecks(box,choicenum,answernum){
   if(box.checked){
        answernum.value += ' ' + choicenum.value;
    }else{
        var str = answernum.value;
        var s_str = choicenum.value;
        var stlen = s_str.length;
        var sres = str.search(s_str);
        var end = sres + stlen;
        var firsthalf = str.slice(0,sres);
        var secondhalf = str.slice(end);
        res = firsthalf + secondhalf;   
        answernum.value = res;
    }
}
</script>
</HEAD>
<BODY>
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
<!--
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
</div>
-->
<div id='pageTitle'>Quiz Creator</div>    

<?php


//**********************************************CHOOSE WHICH PART OF PAGE ***************************************************************** */
switch( $_POST['a'] ) {
    case 'questions' :               
        Questions();
        break;
    case 'confirmation' :               
        Confirmation();
        break;
    case 'process' :
        Process();
        break;    
    default :
        Menu();
        break;   
    }

//*********************************************END CHOOSE WHICH PART OF PAGE ************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************PROCESS RESULTS ************************************************************************** */


function Process(){
    global $db;
    echo "<form method='POST' action=''><div id='formdiv'>";

    if((empty($_POST['clientName'])) || (empty($_POST['qName'])) || (empty($_POST['qSubject'])) || (empty($_POST['min'])) || ($_POST['qNum'] < 1)){
        echo 'CANNOT PROCESS, MISSING DATA';
        echo '<br>Client Name: '.$_POST['clientName'];
        echo '<br>Quiz Name'.$_POST['qName'];
        echo '<br>Subject'.$_POST['qSubject'];
        echo '<br>Number of questions'.$_POST['qNum'];
        echo '<br>Minimum Score to Pass'.$_POST['min'];
    }else{
        $inst = 0;
        $sql = 'INSERT INTO QUIZ_MASTER (QUIZ_NAME,CLIENT_GROUP,Q_SUBJ,MIN_SCORE) VALUES (:1,:2,:3,:4)';        
        $iresult = $db->ExecSQL($sql, array($_POST['qName'], $_POST['clientName'], $_POST['qSubject'],$_POST['min']));
        if(!$iresult){
            // var_dump($insertvalues);
            $sql = str_replace ("'","\"",$sql);
            echo "<script>
                        alert('Quiz not created, failed to add to QUIZ_MASTER table\\n\"'.$sql.'\"');
                        window.location.replace('../Quiz/QUIZ_CREATOR.php');
                </script>";                
        }else{$inst = 1;}
        //insert all data into proper tables     
        if($inst == 1){        
            $inst = 0;
            $sql = 'SELECT QID FROM QUIZ_MASTER WHERE QUIZ_NAME=:1 AND CLIENT_GROUP=:2';
            $result = $db->ExecSQL($sql, array($_POST['qName'],$_POST['clientName']));
            
            //echo 'qName is '.$_POST['qName'].'<br> Client is '.$_POST['clientName'].'<br><br>';
            foreach($result as $r){
                if(count($r)<=0){
                    $error = '<br><br>Quiz creation failed, does not show up in results<br><br>'; 
                //                        
                }else if(count($r) == 1){
                    $quizID = $r['QID'];                                    
                }else{
                    $error = '<br><br>Quiz search failed, there is already a quiz with the same name and client<br><br>'; 
                }
            }     
        }

        if (!empty($quizID)){                                               
            for($i=0; $i < $_POST['qNum']; $i++){
            
                $question = $_POST['question'.$i];
                $answer = $_POST['correctAnswer_'.$i];
                $choice1 = $_POST['choice1_'.$i];
                $mult = $_POST['mult'.$i];

                if (!empty($_POST['choice2_'.$i])){
                $choice2 =$_POST['choice2_'.$i];
                }else{$choice2 = '';}

                if (!empty($_POST['choice3_'.$i])){
                    $choice3 = $_POST['choice3_'.$i];
                }else{$choice3 = '';}

                if (!empty($_POST['choice4_'.$i])){
                    $choice4 = $_POST['choice4_'.$i];
                }else{$choice4 = '';}
                
            $sql = 'INSERT INTO QUIZ_QUESTIONS (QUIZ_ID,QUESTION,ANSWER,CHOICE1,CHOICE2,CHOICE3,CHOICE4,MULT_ANS) VALUES (:1,:2,:3,:4,:5,:6,:7,:8)';
            
            $insertion = $db->ExecSQL($sql, array($quizID,$question,$answer,$choice1,$choice2,$choice3,$choice4,$mult));
            
            if(!$insertion){
                    echo '<br><br>Quiz not created, failed to add to QUIZ_QUESTIONS table<br>';            
                }else{$inst =1;}//  ENDS IF                          
            }//ENDS FOR LOOP
        }
        if($inst == 1){
            echo "
            <div id='header' colspan='2'>QUIZ CREATED!</div>                 
            <div id='qtable'>
                <table style='width:100%;'>
                <tr><td colspan='2'>QUIZ Name Is: ".$_POST['qName']."</td></tr>
                <tr><td colspan='2'>QUIZ ID Is: ".$quizID."</td></tr>
            ";

            $sql = 'SELECT * FROM QUIZ_QUESTIONS WHERE QUIZ_ID=:1 ORDER BY QUESTION_ID';
            $rows = $db->ExecSQL($sql,$quizID);
            
            foreach($rows as $row){
                if ($row['MULT_ANS'] == 'Y'){
                    $multr = 'Yes';
                }else{ $multr = 'No';}
                echo "<tr>
                        <table>
                            <tr><th align='center'>QUESTION ID:</th><th align='center'>".$row['QUESTION_ID']."</th></tr>
                            <tr><td>QUESTION:</td><td>".urldecode($row['QUESTION'])."</td></tr>
                            <tr><td>MULTIPLE ANSWERS?:</td><td>".$multr."</td></tr>
                            <tr><td>ANSWER:</td><td>".urldecode($row['ANSWER'])."</td></tr>
                            <tr><td>CHOICE 1:</td><td>".urldecode($row['CHOICE1'])."</td></tr>
                            <tr><td>CHOICE 2:</td><td>".urldecode($row['CHOICE2'])."</td></tr>
                            <tr><td>CHOICE 3:</td><td>".urldecode($row['CHOICE3'])."</td></tr>  
                            <tr><td>CHOICE 4:</td><td>".urldecode($row['CHOICE4'])."</td></tr>  
                        </table>                             
                    </tr>";
            }//Ends Foreach
            echo "
            </table><!-- ENDtable-->
            </div><!--Ends tablediv   -->
            <div style='margin-top:.5em; margin-left:70%;'><a href='../Quiz/Assign_Quiz.php'>Continue to Quiz Selector</a></div>";
        }//Ends IF $inst
    }//Ends Else
    echo "</div><!-- END formdiv--></form><!-- END form-->";
}//ENDS function PROCESS


//********************************************END PROCESS RESULTS ************************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************CONFIRM RESULTS ************************************************************************** */


function Confirmation(){
    echo "<form method='POST' action=''><div id='formdiv' style='height:50em;'>";
    
    if((empty($_POST['clientName'])) || (empty($_POST['qName'])) ||  ($_POST['qNum'] < 1)){
        echo "<script>
                alert('Basic Quiz information Not set, returning you to initial page\\n
                Client Name:".$_POST['clientName']."\\n
                Quiz Name:".$_POST['qName']."\\n
                Amount of Test Questions:".$_POST['qNum']."');
                window.location.replace('../Quiz/QUIZ_CREATOR.php');
            </script>";
    }

    global $db;
    $cName =$_POST['clientName'];
    $qName = $_POST['qName'];
    $qNum = $_POST['qNum'];
    $qSubject = $_POST['qSubject'];
    $min = $_POST['min'];  
    echo "
    <form method='POST' action=''>
    <div id='header' colspan='2'>CONFIRM QUIZ DETAILS</div>
    <div id='qidRow' colspan='2'>Client is ".$cName."</div>           
    <div id='qtable' style='height:80%;'>
        <table style='width:100%;'>";

    for($i=0; $i < $_POST['qNum']; $i++){        
        $question =urlencode( $_POST['question'.$i]);
        $mult = $_POST['mult'.$i];
        $answer = urlencode($_POST['correctAnswer_'.$i]);
        $choice1 = urlencode($_POST['choice1_'.$i]);

        if ($mult == 'Y'){
           $multTXT="<tr><td>MULTIPLE ANSWERS:</td><td>YES</td></tr>";                    
         }else{ $multTXT='<tr><td>MULTIPLE ANSWERS:</td><td>NO</td></tr>'; }
         $multTXT .= "<input type='hidden' name='mult".$i."' value='".$mult."'>";

        echo "<tr>
                <table class='table is-striped is-fullwidth'>                    
                    <tr style='height:auto;'><td>QUESTION:</td><td>".urldecode($question)."</td></tr>
                    <input type='hidden' name='question".$i."' value='".$question."'>
                    ".$multTXT."
                    <tr><td>ANSWER:</td><td>".urldecode($answer)."</td></tr>
                    <input type='hidden' name='correctAnswer_".$i."' value='".$answer."'>
                    <tr><td>CHOICE 1:</td><td>".urldecode($choice1)."</td></tr>
                    <input type='hidden' name='choice1_".$i."' value='".$choice1."'>
                    ";
        if ($_POST['choice2_'.$i]){
            $choice2 = urlencode($_POST['choice2_'.$i]);            
            echo "  <tr><td>CHOICE 2:</td><td>".urldecode($choice2)."</td></tr>
            <input type='hidden' name='choice2_".$i."' value='".$choice2."'>
            ";            
        }    
        if ($_POST['choice3_'.$i]){            
            $choice3 = urlencode($_POST['choice3_'.$i]);
            echo "  <tr><td>CHOICE 3:</td><td>".urldecode($choice3)."</td></tr>
            <input type='hidden' name='choice3_".$i."' value='".$choice3."'>
            ";
        } 
        if ($_POST['choice4_'.$i]){            
            $choice4 = urlencode($_POST['choice4_'.$i]);
            echo "  <tr><td>CHOICE 4:</td><td>".urldecode($choice4)."</td></tr>
            <input type='hidden' name='choice4_".$i."' value='".$choice4."'>
            ";
        }                   
             
        echo '</table></tr>';
    }
    echo "                           
    </table><!-- ENDtable-->
    </div><!--Ends tablediv   -->
    <input type='hidden' name='a' value='process'>
    <input type='hidden' name='clientName' value='".$cName."'>
    <input type='hidden' name='qName' value='".$qName."'>
    <input type='hidden' name='qNum' value='".$qNum."'>
    <input type='hidden' name='qSubject' value='".$qSubject."'>
    <input type='hidden' name='min' value='".$min."'>   
    <div style='margin-left:70%; margin-top:.5em;'>
        <span><input type='submit' class='button is-info' value='Submit'></span>
        <span><a href='Create_Quiz.php' class='button is-warning' >Cancel</a></span>
    </div>  
    ";
    echo "</div><!-- END formdiv--></form><!-- END form-->";
}//ENDS FUNCTION CONFIRMATION

//***********************************************END CONFIRM RESULTS *********************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************ENTER QUESTIONS *************************************************************************** */

function Questions(){
    global $db;
    
    echo "<form method='POST' action=''><div id='formdiv'>";

    if((empty($_POST['clientName'])) || (empty($_POST['qName']))  || (empty($_POST['min']))  || ($_POST['qNum'] < 1)){
        if (empty($_POST['qSubject'])){
            $tDate=date('YmdHis');
            $_POST['qSubject'] = 'none_'.$tDate;
            echo $_POST['qSubject'];
        }
        echo '<SCRIPT>alert("MISSING DATA, TRY AGAIN");</SCRIPT>';
    }else{        
        $sql = 'SELECT * FROM QUIZ_MASTER WHERE CLIENT_GROUP =:1 AND QUIZ_NAME=:2';
        $results = $db->ExecSQL($sql, array($_POST['clientName'] , $_POST['qName']));
        //echo $results.length;      
        if(count($results)>0){
            echo '<SCRIPT>alert("THIS CLIENT ALREADY HAS TEST WITH THIS NAME, TRY AGAIN")</SCRIPT>';
            Menu();
        }else{
            echo "
                <div id='header'>QUESTIONS</div>
                <div id='qtable'>                
                    <table style='width:100%;'>
            ";

            for($i=0; $i < $_POST['qNum']; $i++){
                if ($letter == 'B'){$letter='A';}else{$letter='B';}
            
            echo "<tr class='question".$letter."' style='vertical-align:text-top;'>
                    <td class='questions' align='left' nowrap>
                        &nbsp&nbspQuestion:
                        <input type='text' name='question".$i."' class='qChoice' length='20' maxlength='200'><br><br>
                        <center><input type='checkbox' name='mult".$i."' value='Y'>Multiple Answers?</center>
                    </td>
                    <td class='respTD' style='vertical-align:text-top; padding-top:0;'>
                        Check the boxes of the correct answer(s).
                        <span class='tbox' align:'right'>
                            <input type='hidden' name='correctAnswer_".$i."' id='correctAnswer_".$i."' class='qChoice'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <input type ='checkbox' id='cbox1_".$i."' onclick='answerChecks(this,choice1_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice1_".$i."' id='choice1_".$i."' class='qChoice' length='20' maxlength='200' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <input type ='checkbox' id='cbox2_".$i."' onclick='answerChecks(this,choice2_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice2_".$i."' id='choice2_".$i."' class='qChoice' length='20' maxlength='200'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <input type ='checkbox' id='cbox3_".$i."' onclick='answerChecks(this,choice3_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice3_".$i."' id='choice3_".$i."' class='qChoice' length='20' maxlength='200'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <input type ='checkbox' id='cbox4_".$i."' onclick='answerChecks(this,choice4_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice4_".$i."' id='choice4_".$i."'class='qChoice' length='20' maxlength='200'' align:'right'>
                        </span><br>                       
                    </td>                                    
                </tr>";
            }//ENDS QUESTION FOR LOOP
        
    echo"
        </table><!-- END qtable-->
        </div><!-- END qtable div--> 
        <div id='bottomDiv'>                 
            <table>
                <tr>
                    <td  style='width:50%'>
                    <input type='hidden' name='clientName' value='".$_POST['clientName']."'>
                    <input type='hidden' name='qName' value='".$_POST['qName']."'>
                    <input type='hidden' name='qNum' value='".$_POST['qNum']."'> 
                    <input type='hidden' name='qSubject' value='".$_POST['qSubject']."'>
                    <input type='hidden' name='min' value='".$_POST['min']."'>
                    <input type='hidden' name='a' value='confirmation'>  
                    </td>
                    <td  style='padding-left:70%;' nowrap>
                        <span><input type='submit' class='button is-info' value='Submit'></span>
                        <span><button type='reset' class='button is-warning' value='Reset'>Cancel</button></span>
                    </td>
                </tr>
            </table>
        </div><!-- END bottomdiv-->
        ";
    
        }//ends secondary else
    
    }//ENDS MAIN ELSE
    echo "</div><!-- END formdiv--></form><!-- END form-->";
}//ENDS FUNCTION Questions

//*******************************************END ENTER QUESTIONS *************************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************DISPLAY MENU ***************************************************************************** */

    
function Menu(){
global $db;

echo "<form method='POST' action=''><div id='formdiv'>";

$options = '';
$sql = "SELECT DISTINCT CLIENT_GROUP
        FROM CLIENT_GROUP WHERE CLIENT_GROUP <> '(NOT USED)'       
        ORDER BY CLIENT_GROUP";
    $results = $db->execSQL($sql);
    if (count($results) > 0){
        foreach($results as $r) {
            $options .= "<option value='{$r['CLIENT_GROUP']}'>{$r['CLIENT_GROUP']}</option>";
            }//end for loop
        }
 echo "
        <!--<div id='header'>Basic Information</div>-->
        <section class='herois-primary is-medium' style='padding:.5em;'>
            <div class=\"hero-body\" style='padding:0;'>
                <div class=\"container\">
                    <h1 class=\"title is-3\">
                    BASIC INFORMATION
                    </h1>
                </div>
            </div>
        </section> 


        <table id='qtable' class='table is-bordered is-striped is-fullwidth'>                
            <tr class='questionA'>
                <td class='questions' nowrap>
                    Which client is this quiz for?
                </td>
                <td class='respTD'>
                    <select class='dropDowns' name='clientName' id='clientName' maxlength:'20'>
                        <option value='null'>-SELECT-</option>
                        ".$options. "                              
                    </select>
                </td>
            </tr>
            <tr class='questionB'>
                <td class='questions' nowrap>
                    What is the title of this Quiz?
                </td>
                <td class='respTD'>
                    <input type='text' name='qName' id='qName' maxlength='30'>
                </td>                                    
            </tr>
            <tr class='questionA'>
                <td class='questions' nowrap>
                    What is the subject of this Quiz?
                </td>
                <td class='respTD'>
                    <input type='text' name='qSubject' id='qSubject' maxlength='30'>
                </td>                                    
            </tr>
            <tr class='questionB'>
                <td class='questions' id='1_3' nowrap>
                    How Many Questions will this quiz contain?
                </td>
                <td class='respTD'>
                    <input type='text' name='qNum' id='qNum' maxlength='3'>
                </td>                                    
            </tr>
            <tr class='questionA'>
                <td class='questions' nowrap>
                    What is the minimum passing score (out of 100)?
                </td>
                <td class='respTD'>
                    <input type='text' name='min' id='min' maxlength='3'>
                </td>                                    
            </tr>
        </table>
        <div style='margin-top:.5em; margin-left:60%;'>  
            <table>
                <tr>
                    <td ><input type='hidden' name='a' value='questions'> </td>
                    <td  nowrap>
                        <button type='submit' class='button is-info' value='Submit'>Submit</button>&nbsp&nbsp
                        <button type='reset' class='button is-warning' value='Reset'>Cancel</button>
                    </td>
                </tr>    
            </table><!-- ENDtable-->
        </div><!-- End bottomDiv -->";
        echo "</div><!-- END formdiv--></form><!-- END form-->";
}//ENDS FUNCTION MENU
//*************************************************END MENU ******************************************************************************* */
?>
</BODY>
</HTML>
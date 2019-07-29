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
<TITLE>Create a Quiz</TITLE>

<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
include_once('\\inetpub\\wwwroot\\user.php.inc');

// 30 MAR 2018 The following javascript function and added checkboxes to question creation are added to give possibilities of multiple answers. 


?>
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
.navbar-light .navbar-nav .nav-link {
    color: #fff;
    margin-left:1rem;
    margin-right:1rem;
}
</style>
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
function tcl(box, qACount){
    var count = parseInt(qACount.value);    
    if(box.checked){
        count++;        
    }else{
        count--;
    }
    qACount.value = count;
    //alert(qACount.id + " value is " + parseInt(qNum.value));
}
</script>
</HEAD>
<BODY>


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
        $sql = 'INSERT INTO QUIZ_MASTER (QUIZ_NAME,CLIENT_GROUP,Q_SUBJ,MIN_SCORE,ACTIVE) VALUES (:1,:2,:3,:4,:5)';
                
        $iresult = $db->ExecSQL($sql, array($_POST['qName'], $_POST['clientName'], $_POST['qSubject'],$_POST['min'],'Y'));
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
                $qACount = $_POST['qACount_'.$i];
                //echo "<div> QAcount is ".$qACount."</div>";
                if (!empty($_POST['choice2_'.$i])){
                $choice2 =$_POST['choice2_'.$i];
                }else{$choice2 = '';}

                if (!empty($_POST['choice3_'.$i])){
                    $choice3 = $_POST['choice3_'.$i];
                }else{$choice3 = '';}

                if (!empty($_POST['choice4_'.$i])){
                    $choice4 = $_POST['choice4_'.$i];
                }else{$choice4 = '';}

                if (!empty($_POST['choice5_'.$i])){
                    $choice5 = $_POST['choice5_'.$i];
                }else{$choice5 = '';}

                if (!empty($_POST['choice6_'.$i])){
                    $choice6 = $_POST['choice6_'.$i];
                }else{$choice6 = '';}

                if (!empty($_POST['choice7_'.$i])){
                    $choice7 = $_POST['choice7_'.$i];
                }else{$choice7 = '';}

                if (!empty($_POST['choice8_'.$i])){
                    $choice8 = $_POST['choice8_'.$i];
                }else{$choice8 = '';}

                if (!empty($_POST['choice9_'.$i])){
                    $choice9 = $_POST['choice9_'.$i];
                }else{$choice9 = '';}

                if (!empty($_POST['choice10_'.$i])){
                    $choice10 = $_POST['choice10_'.$i];
                }else{$choice10 = '';}

                if (!empty($_POST['choice11_'.$i])){
                    $choice11 = $_POST['choice11_'.$i];
                }else{$choice11 = '';}

                if (!empty($_POST['choice12_'.$i])){
                    $choice12 = $_POST['choice12_'.$i];
                }else{$choice12 = '';}
                
            $sql = 'INSERT INTO QUIZ_QUESTIONS (QUIZ_ID,QUESTION,ANSWER,CHOICE1,CHOICE2,CHOICE3,CHOICE4,MULT_ANS,CHOICE5,CHOICE6,CHOICE7,CHOICE8,CHOICE9,CHOICE10,CHOICE11,CHOICE12,NUM_ANSWERS) VALUES (:1,:2,:3,:4,:5,:6,:7,:8,:9,:10,:11,:12,:13,:14,:15,:16,:17)';
            $params = array($quizID,$question,$answer,$choice1,$choice2,$choice3,$choice4,$mult,$choice5,$choice6,$choice7,$choice8,$choice9,$choice10,$choice11,$choice12,$qACount);
            //print_r($params);
            $insertion = $db->ExecSQL($sql, $params);
            
            if(!$insertion){
                    echo '<br><br>Quiz not created, failed to add to QUIZ_QUESTIONS table<br>';            
                }else{$inst =1;}//  ENDS IF                          
            }//ENDS FOR LOOP
        }
        if($inst == 1){
            echo "
            <div id='header' colspan='2'>QUIZ CREATED!</div>                 
            <div id='qtable' style='height:80%; background-color:#fffccc; padding-left:.5em;'>
                <table style='width:100%;'>
                <tr><td colspan='2'>QUIZ Name Is: ".$_POST['qName']."</td></tr>
                <tr><td colspan='2' style='margin-bottom:2em;'>QUIZ ID Is: ".$quizID."</td></tr>
            ";

            $sql = 'SELECT * FROM QUIZ_QUESTIONS WHERE QUIZ_ID=:1 ORDER BY QUESTION_ID';
            $rows = $db->ExecSQL($sql,$quizID);
            
            foreach($rows as $row){
                if ($row['MULT_ANS'] == 'Y'){
                    $multr = 'Yes';
                }else{ $multr = 'No';}
                echo "<tr>
                        <table style='margin-bottom:2rem;' class='table-bordered'>
                            <tr><th align='center'>QUESTION ID:</th><th align='center'>".$row['QUESTION_ID']."</th></tr>
                            <tr><td>QUESTION:</td><td>".urldecode($row['QUESTION'])."</td></tr>
                            <tr><td>MULTIPLE ANSWERS?:</td><td>".$multr."</td></tr>
                            <tr><td>NUMBER OF ANSWERS:</td><td>".$row['NUM_ANSWERS']."</td></tr>
                            <tr><td>ANSWER:</td><td>".urldecode($row['ANSWER'])."</td></tr>
                            <tr><td>CHOICE 1:</td><td>".urldecode($row['CHOICE1'])."</td></tr>
                            <tr><td>CHOICE 2:</td><td>".urldecode($row['CHOICE2'])."</td></tr>
                            <tr><td>CHOICE 3:</td><td>".urldecode($row['CHOICE3'])."</td></tr>  
                            <tr><td>CHOICE 4:</td><td>".urldecode($row['CHOICE4'])."</td></tr>
                            <tr><td>CHOICE 5:</td><td>".urldecode($row['CHOICE5'])."</td></tr> 
                            <tr><td>CHOICE 6:</td><td>".urldecode($row['CHOICE6'])."</td></tr> 
                            <tr><td>CHOICE 7:</td><td>".urldecode($row['CHOICE7'])."</td></tr> 
                            <tr><td>CHOICE 8:</td><td>".urldecode($row['CHOICE8'])."</td></tr> 
                            <tr><td>CHOICE 9:</td><td>".urldecode($row['CHOICE9'])."</td></tr> 
                            <tr><td>CHOICE 10:</td><td>".urldecode($row['CHOICE10'])."</td></tr>
                            <tr><td>CHOICE 11:</td><td>".urldecode($row['CHOICE11'])."</td></tr> 
                            <tr><td>CHOICE 12:</td><td>".urldecode($row['CHOICE12'])."</td></tr>    
                        </table>                             
                    </tr>";
            }//Ends Foreach
            echo "
            </table><!-- ENDtable-->
            </div><!--Ends tablediv   -->
            <div style='margin-top:.5em; margin-left:70%;'><a href='../Quiz/Assign_Quiz.php' class='btn btn-primary'>Continue to Quiz Selector</a></div>";
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
        $qACount = $_POST['qACount_'.$i];
        $answer = urlencode($_POST['correctAnswer_'.$i]);
        $choice1 = urlencode($_POST['choice1_'.$i]);

        if ($mult == 'Y'){
           $multTXT="<tr><td>MULTIPLE ANSWERS:</td><td>YES</td></tr>";                    
         }else{ $multTXT='<tr><td>MULTIPLE ANSWERS:</td><td>NO</td></tr>'; }
         $multTXT .= "<input type='hidden' name='mult".$i."' value='".$mult."'>";
        
        echo "<tr>
                <table class='table is-striped is-fullwidth'>                    
                    <tr style='height:auto;'><td>QUESTION:</td><td>".urldecode($question)."</td></tr>
                    <input type='hidden' name='qACount_".$i."' value=".$qACount.">
                    <input type='hidden' name='question".$i."' value='".$question."'>
                    ".$multTXT."
                    <tr style='height:auto;'><td>Num OF Answers:</td><td>".$qACount."</td></tr>
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
        if ($_POST['choice5_'.$i]){            
            $choice5 = urlencode($_POST['choice5_'.$i]);
            echo "  <tr><td>CHOICE 5:</td><td>".urldecode($choice5)."</td></tr>
            <input type='hidden' name='choice5_".$i."' value='".$choice5."'>
            ";
        }
        if ($_POST['choice6_'.$i]){            
            $choice6 = urlencode($_POST['choice6_'.$i]);
            echo "  <tr><td>CHOICE 6:</td><td>".urldecode($choice6)."</td></tr>
            <input type='hidden' name='choice6_".$i."' value='".$choice6."'>
            ";
        }
        if ($_POST['choice7_'.$i]){            
            $choice7 = urlencode($_POST['choice7_'.$i]);
            echo "  <tr><td>CHOICE 7:</td><td>".urldecode($choice7)."</td></tr>
            <input type='hidden' name='choice7_".$i."' value='".$choice7."'>
            ";
        }
        if ($_POST['choice8_'.$i]){            
            $choice8 = urlencode($_POST['choice8_'.$i]);
            echo "  <tr><td>CHOICE 8:</td><td>".urldecode($choice8)."</td></tr>
            <input type='hidden' name='choice8_".$i."' value='".$choice8."'>
            ";
        }
        if ($_POST['choice9_'.$i]){            
            $choice9 = urlencode($_POST['choice9_'.$i]);
            echo "  <tr><td>CHOICE 9:</td><td>".urldecode($choice9)."</td></tr>
            <input type='hidden' name='choice9_".$i."' value='".$choice9."'>
            ";
        }
        if ($_POST['choice10_'.$i]){            
            $choice10 = urlencode($_POST['choice10_'.$i]);
            echo "  <tr><td>CHOICE 10:</td><td>".urldecode($choice10)."</td></tr>
            <input type='hidden' name='choice10_".$i."' value='".$choice10."'>
            ";
        }
        if ($_POST['choice11_'.$i]){            
            $choice11 = urlencode($_POST['choice11_'.$i]);
            echo "  <tr><td>CHOICE 11:</td><td>".urldecode($choice11)."</td></tr>
            <input type='hidden' name='choice11_".$i."' value='".$choice11."'>
            ";
        }
        if ($_POST['choice12_'.$i]){            
            $choice12 = urlencode($_POST['choice12_'.$i]);
            echo "  <tr><td>CHOICE 12:</td><td>".urldecode($choice12)."</td></tr>
            <input type='hidden' name='choice12_".$i."' value='".$choice12."'>
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
        <span><input type='submit' class='btn btn-primary' value='Submit'></span>
        <span><a href='Create_Quiz.php' class='btn btn-warning' >Cancel</a></span>
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
                <div id='qtable' style='height:80%;'>                
                    <table style='width:100%;'>
            ";

            for($i=0; $i < $_POST['qNum']; $i++){
                if ($letter == 'B'){$letter='A';}else{$letter='B';}
            
            echo "<tr class='question".$letter."' style='vertical-align:text-top;'>
                    <td class='questions' align='left' nowrap>                        
                        &nbsp&nbsp".($i+1).":
                        <input type='text' name='question".$i."' placeholder='Question' class='qChoice' length='20' maxlength='1000'><br><br>
                        <center><input type='checkbox' name='mult".$i."' value='Y'>Multiple Answers?</center>
                        <input type='hidden' name='qACount_".$i."' id='qACount_".$i."' value=0>
                    </td>
                    <td class='respTD' style='vertical-align:text-top; padding-top:0;'>
                        Check the boxes of the correct answer(s).
                        <span class='tbox' align:'right'>
                            <input type='hidden' name='correctAnswer_".$i."' id='correctAnswer_".$i."' class='qChoice'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>A</span>
                            <input type ='checkbox' id='cbox1_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice1_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice1_".$i."' id='choice1_".$i."' class='qChoice' length='20' maxlength='500' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>B</span>
                            <input type ='checkbox' id='cbox2_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice2_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice2_".$i."' id='choice2_".$i."' class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>C</span>
                            <input type ='checkbox' id='cbox3_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice3_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice3_".$i."' id='choice3_".$i."' class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>D</span>
                            <input type ='checkbox' id='cbox4_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice4_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice4_".$i."' id='choice4_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>E</span>
                            <input type ='checkbox' id='cbox5_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice5_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice5_".$i."' id='choice5_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span><br>                        
                        <span class='tbox' align:'right'>
                            <span>F</span>
                            <input type ='checkbox' id='cbox6_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice6_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice6_".$i."' id='choice6_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>G</span>
                            <input type ='checkbox' id='cbox7_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice7_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice7_".$i."' id='choice7_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>H</span>
                            <input type ='checkbox' id='cbox8_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice8_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice8_".$i."' id='choice8_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>I</span>
                            <input type ='checkbox' id='cbox9_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice9_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice9_".$i."' id='choice9_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>J</span>
                            <input type ='checkbox' id='cbox10_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice10_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice10_".$i."' id='choice10_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>K</span>
                            <input type ='checkbox' id='cbox11_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice11_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice11_".$i."' id='choice11_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>
                        <span class='tbox' align:'right'>
                            <span>L</span>
                            <input type ='checkbox' id='cbox12_".$i."' onclick='tcl(this, qACount_".$i."); answerChecks(this,choice12_".$i.",correctAnswer_".$i.")'>
                            <input type='text' name='choice12_".$i."' id='choice12_".$i."'class='qChoice' length='20' maxlength='500'' align:'right'>
                        </span>
                        <br>                       
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
                        <span><input type='submit' class='btn btn-primary' value='Submit'></span>
                        <span><button type='reset' class='btn btn-warning' value='Reset'>Cancel</button></span>
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
        <section style='padding:.5em;'>            
                <div class=\"container\">
                    <h1>
                    BASIC INFORMATION
                    </h1>
                </div>            
        </section> 


        <table id='qtable' class='table table-bordered table-striped table-responsive' style='max-width:fit-content;'>                
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
                    <input type='text' name='qName' id='qName' maxlength='500'>
                </td>                                    
            </tr>
            <tr class='questionA'>
                <td class='questions' nowrap>
                    What is the subject of this Quiz?
                </td>
                <td class='respTD'>
                    <input type='text' name='qSubject' id='qSubject' maxlength='500'>
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
                        <button type='submit' class='btn btn-primary' value='Submit'>Submit</button>&nbsp&nbsp
                        <button type='reset' class='btn btn-warning' value='Reset'>Cancel</button>
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
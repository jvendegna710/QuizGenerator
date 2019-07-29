<!DOCTYPE HTML>
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
<TITLE>Edit a Quiz</TITLE>
<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
include_once('\\inetpub\\wwwroot\\user.php.inc');
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

<?php
switch( $_REQUEST['a'] ) {
    case 'process' :
        Process();
        break;
    case 'edit' :               
        Edit();
        break;      
    default :
        Menu();
        break;   
} 

 /*
################################################################################################################################################
*/
//***********************************************PROCESS CHANEFES ******************************************************************************* */
function Process(){
    global $db;
    //print_r($_REQUEST);
    $counter = $_REQUEST['qcount'];
    $success = 0;
    $fail = 0;
    for($i = 0; $i < $counter; $i++){

        $sql = "UPDATE QUIZ_QUESTIONS
            SET
            QUESTION =:1,
            MULT_ANS =:2,
            NUM_ANSWERS =:3,
            ANSWER =:4,
            CHOICE1 =:5,
            CHOICE2 =:6,
            CHOICE3 =:7,
            CHOICE4 =:8,
            CHOICE5 =:9,
            CHOICE6 =:10,
            CHOICE7 =:11,
            CHOICE8 =:12,
            CHOICE9 =:13,
            CHOICE10 =:14,
            CHOICE11 =:15,
            CHOICE12 =:16
            WHERE QUESTION_ID =:17
            ";
           $question = "'".urlencode($_REQUEST['question_'.$i])."'";
           $multans= "'".urlencode($_REQUEST['multans_'.$i])."'"; 
           $numans= "'".urlencode($_REQUEST['numans_'.$i])."'"; 
           $answer= "'".urlencode($_REQUEST['answer_'.$i])."'";
           $choice1= "'".urlencode( $_REQUEST['choice1_'.$i])."'";
           $choice2= "'".urlencode( $_REQUEST['choice2_'.$i])."'";
           $choice3= "'".urlencode($_REQUEST['choice3_'.$i])."'"; 
           $choice4= "'".urlencode($_REQUEST['choice4_'.$i])."'"; 
           $choice5= "'".urlencode($_REQUEST['choice5_'.$i])."'";
           $choice6= "'".urlencode($_REQUEST['choice6_'.$i])."'"; 
           $choice7= "'".urlencode($_REQUEST['choice7_'.$i])."'"; 
           $choice8= "'".urlencode($_REQUEST['choice8_'.$i])."'"; 
           $choice9= "'".urlencode($_REQUEST['choice9_'.$i])."'";
           $choice10= "'".urlencode( $_REQUEST['choice10_'.$i])."'";
           $choice11= "'".urlencode($_REQUEST['choice11_'.$i])."'";
           $choice12= "'".urlencode($_REQUEST['choice12_'.$i])."'";
           $id= $_REQUEST['id_'.$i];

           $sql = "UPDATE QUIZ_QUESTIONS
           SET
           QUESTION = {$question},
           MULT_ANS = {$multans},
           NUM_ANSWERS = {$numans},
           ANSWER ={$answer},
           CHOICE1 ={$choice1},
           CHOICE2 ={$choice2},
           CHOICE3 ={$choice3},
           CHOICE4 ={$choice4},
           CHOICE5 ={$choice5},
           CHOICE6 ={$choice6},
           CHOICE7 ={$choice7},
           CHOICE8 ={$choice8},
           CHOICE9 ={$choice9},
           CHOICE10 ={$choice10},
           CHOICE11 ={$choice11},
           CHOICE12 ={$choice12}
           WHERE QUESTION_ID ={$id}
           ";/** */
            
        //$params = array($question, $multans, $numans, $answer, $choice1, $choice2, $choice3, $choice4, $choice5, $choice6, $choice7, $choice8, $choice9, $choice10, $choice11, $choice12, $id);
        if($db->ExecSQL($sql)){
        //if($db->ExecSQL($sql, $params)){
            $success++;           
        }else{
            $fail++;            
            //echo $sql;           
        }
    }
    if ($success > 0){
        echo "<div><h6>Changes were successfully made.</h6></div>";
    }
    if($fail > 0){
    echo "<div><h6>Changes FAILED!</h6></div><br>";
    }
    if($success == 0 && $fail ==0){
        echo "<div><h6>No changes made.</h6></div><br>";
    }
    Menu();

}
//*******************************************END PROCESS *************************************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************EDIT QUIZ ************************************************************************************ */

    function Edit(){
        global $db;
        if (!empty($_POST['quizID'])){
            $qID = $_POST['quizID'];
            
            $tableinput ="";

            $sql = "SELECT QUESTION_ID,
                        QUESTION,
                        MULT_ANS,
                        NUM_ANSWERS,
                        ANSWER,
                        CHOICE1,
                        CHOICE2,
                        CHOICE3,
                        CHOICE4,
                        CHOICE5,
                        CHOICE6,
                        CHOICE7,
                        CHOICE8,
                        CHOICE9,
                        CHOICE10,
                        CHOICE11,
                        CHOICE12
                    FROM QUIZ_QUESTIONS
                    WHERE QUIZ_ID = ".$qID." ORDER BY QUESTION_ID";
            
            $result = $db->ExecSQL($sql);
            $rcount = count($result);
            $i = 0;        
            $returnArray = array();            
            foreach($result as $r){
                $question = $r['QUESTION'];
                $multans = $r['MULT_ANS'];
                $numans = $r['NUM_ANSWERS'];
                $answer = $r['ANSWER'];
                $choice1 = $r['CHOICE1'];
                $choice2 = $r['CHOICE2'];
                $choice3 = $r['CHOICE3'];
                $choice4 = $r['CHOICE4'];
                $choice5 = $r['CHOICE5'];
                $choice6 = $r['CHOICE6'];
                $choice7 = $r['CHOICE7'];
                $choice8 = $r['CHOICE8'];
                $choice9 = $r['CHOICE9'];
                $choice10 = $r['CHOICE10'];
                $choice11 = $r['CHOICE11'];
                $choice12 = $r['CHOICE12'];
                //<textarea name='DETAILS' cols='50' rows='4' class='ntext'>
                $tableinput .= "<tr>
                                    <td align='left' ><input type='hidden' name='id_".$i."' value='{$r['QUESTION_ID']}'>
                                    <textarea cols='50' rows='4' name='question_".$i."'>".urldecode($question)."</textarea></td>
                                    <td align='center' style='min-width:8em;' >
                                    <input type=text style='width:2em;' name='multans_".$i."'value='".urldecode($multans)."'></td>
                                    <td align='center' style='min-width:11em;'>
                                    <input type=text' style='width:2em;' rows='4' name='numans_".$i."' value='".urldecode($numans)."'></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='answer_".$i."'>".urldecode($answer)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice1_".$i."'>".urldecode($choice1)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice2_".$i."'>".urldecode($choice2)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice3_".$i."'>".urldecode($choice3)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice4_".$i."'>".urldecode($choice4)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice5_".$i."'>".urldecode($choice5)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice6_".$i."'>".urldecode($choice6)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice7_".$i."'>".urldecode($choice7)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice8_".$i."'>".urldecode($choice8)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice9_".$i."'>".urldecode($choice9)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice10_".$i."'>".urldecode($choice10)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice11_".$i."'>".urldecode($choice11)."</textarea></td>
                                    <td align='center' >
                                    <textarea cols='50' rows='4' name='choice12_".$i."'>".urldecode($choice12)."</textarea></td>
                                </tr>";
                $i++;
            }
        }
        echo "       


<style>
#qdiv{
    background-color: #f3f2f0;
    width:80%;
    margin: 0pt auto 0pt auto;
    border: .3em double darkgrey;    
    height:50em;    
    padding: .5em;
}
</style>
<div id='pageTitle'>Quiz Editor</div>        
<form action='' method='POST'> 
    <div id='qdiv'>               
            <div style='overflow:scroll; height:95%;' >
                <table class='table table-bordered table-striped table-condensed table-hoverable table-sm' id='nameTable'>
                    <thead>
                        <th style='width:20%;'>Question</th>
                        <th style='width:16%;'>Multiple Answers?(Y/N)</th>
                        <th>Number of Answers (If Multiple is 'Y')</th>                        
                        <th style='width:16%;'>Answer</th>
                        <th style='width:16%;'>Choice1</th>
                        <th style='width:16%;'>Choice2</th>
                        <th style='width:16%;'>Choice3</th>
                        <th style='width:16%;'>Choice4</th>
                        <th style='width:16%;'>Choice5</th>
                        <th style='width:16%;'>Choice6</th>
                        <th style='width:16%;'>Choice7</th>
                        <th style='width:16%;'>Choice8</th>
                        <th style='width:16%;'>Choice9</th>
                        <th style='width:16%;'>Choice10</th>
                        <th style='width:16%;'>Choice11</th>
                        <th style='width:16%;'>Choice12</th>
                    </thead>
                    ".$tableinput."
                </table>
            </div>            
        <input type='hidden' name='qcount' value={$rcount}>
        <input type='hidden' name='a' value='process'>
        <input type='submit' class='btn btn-sm btn-info'> 
        <a href='Edit_Quiz.php' class='btn btn-sm btn-danger'>Cancel</a>           
    </div><!-- END FORMDIV  -->   
";

    }//ENDS FUNCTION ASSIGNMENT

//*******************************************END EDIT QUIZ ************************************************************************************ */
/*
################################################################################################################################################
*/
//***********************************************DISPLAY MENU ******************************************************************************** */

    function Menu(){
        global $db;
       
        //CREATE ARRAY WITH OPTIONS FOR DROPDOWN FOR CLIENT GROUP
        $clients = array();
        $clientSelect = '';
       // $sql = "SELECT CLIENT_GROUP FROM QUIZ_MASTER GROUP BY CLIENT_GROUP";
       /*
        $sql = "SELECT DISTINCT CLIENT_GROUP
                FROM CLIENT_GROUP WHERE CLIENT_GROUP <> '(NOT USED)'       
                ORDER BY CLIENT_GROUP";/** */
        $sql = "SELECT DISTINCT CLIENT_GROUP
                FROM QUIZ_MASTER      
                ORDER BY CLIENT_GROUP";                    
        $results = $db->ExecSQL($sql);
        foreach($results as $r){
                array_push($clients, $r['CLIENT_GROUP']);
                $clientSelect .= "<option value='".$r['CLIENT_GROUP']."'>".$r['CLIENT_GROUP']."</option>";
        }//ENDS FOREACH FOR SQL CALL
        echo "
        <script>
        $(document).ready(function(){ 

            //CLIENT DROPDOWN *************************************************************************************************/
            $(\"#clientDD\").change(function(){
                var clientName = $(this).val();
                $.ajax({
                    url: 'quiz_dropdowns.php',
                    type: 'POST',
                    data: {client:clientName},
                    dataType: 'json',
                    success:function(response){
                        var len = response.length;
                        $(\"#quizNameDD\").empty();
                        $(\"#quizNameDD\").append(\"<option value='null'>- Select -</option>\");
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            var subject = response[i]['subject'];                
                            $(\"#quizNameDD\").append(\"<option value='\"+id+\"'>\"+name+\" | \"+subject+\"</option>\");
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(\"FAILED TO RETRIEVE QUIZ NAME DATA \");
                    }//ENDS SUCCESS/ERROR OF CALL
                });//ENDS AJAX
            });//ENDS FUNCTION ON CHANGE OF DROPDOWN
        });//ENDS ON DOCUMENT READY FUNCTION
        </script>
        <div id='pageTitle'>Quiz Edit</div>
        <br>
        <div id='formdiv' style='height:auto;'>
        <div id='header'>Choose Which Exam You Would Like to Edit</div><br>  
        <form method='POST' action=''> 
        <div>
            <table class='table table-striped' style='padding-top:1.5em; width:100%;'>
                <tr class='c_row'>
                    <td class='qlabel'>Client:</td>
                    <td style='text-align:center'>
                        <select id='clientDD' class='ddown'>
                            <option value='null'>- Select -</option>
                           ".$clientSelect."
                        </select>
                        </td>
                    </tr>
                    <tr class='c_row'>
                        <td class='qlabel'>Quiz Name & Subject:</td>
                        <td style='text-align:center'>
                            <select id='quizNameDD' name='quizID' class='ddown'>
                                <option value='null'>- Select -</option>                            
                            </select>
                        </td>
                    </tr>           
                </table>
            </div>
            <div style='margin-left:70%; margin-bottom:.5em; padding-top:.5em;' nowrap>
            <input type='hidden' name='a' value='edit'>
                <span><button type='submit' class='btn btn-primary' value='Submit'>Submit</button></span>
                <span><a href='Edit_Quiz.php' class='btn btn-warning' >Cancel</a></span>
            </div><!-- END bottomdiv-->  
            </form><!-- END form-->
            </div><!-- END FORMDIV      -->  
            ";                     

    }//ENDS FUNCTION MENU
//*************************************************END MENU ********************************************************************************** */
?>
</BODY>
</HTML>
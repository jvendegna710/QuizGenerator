<!DOCTYPE HTML>
<HEAD>
<link rel="stylesheet" type="text/css" href="quiz_style.css">
<link rel='stylesheet' type='text/css' href='Bulma_APR2018.css'>
<TITLE>Assign a Quiz</TITLE>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
include_once('\\inetpub\\wwwroot\\user.php.inc');
?>
 </HEAD>
<BODY>
<nav class="navbar  is-link">
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
</div> -->
<?php
switch( $_REQUEST['a'] ) {
    case 'assignment' :               
        Assignment();
        break;
    case 'confirm' :               
        Confirm();
        break;
    case 'process' :
        Process();
        break;    
    default :
        Menu();
        break;   
} 
?>
<!-- ******************************************************************************************************************************************************************* -->   
<?php
 //***********************************************PROCESS AND SHOW RECEIPT FOR QUIZ ASSIGNMENT******************************************* */
 function Process(){
    global $db;
    
    if((empty($_POST['quizID'])) || (empty($_POST['numNames']))){
        echo "CANNOT PROCESS, MISSING DATA";
        echo "<br>Quiz ID: ".$_POST['quizID'];
        echo "<br>Number of People To Be Assigned: ".$_POST['numNames'];        
    }else{
        $nNum = intval($_POST['numNames']);
        $errormsg = '';
        $date=date("m/d/y"); 
        $qid = $_POST['quizID'];
        //echo "Number is ".$nNum;       
        for($i=0; $i < $nNum; $i++){
            //echo "<br>".$i."<br>";
            $name = $_POST['assignedName_'.$i];
            //echo $name;
            //echo "<br>INSERT INTO QUIZ_USERS (USERID,QUIZID,DATE_ASSIGNED) VALUES (".$name.",".$qid.",".SYSDATE.")<br>";
            $sql = "INSERT INTO QUIZ_USERS (USERID,QUIZID,DATE_ASSIGNED) VALUES (:1,:2,SYSDATE)";
            $results = $db->ExecSQL($sql,array($name,$qid));
            if(!$results){
                echo "\nERROR INSERTING: \nUSER ".$name."\nQuiz ID: ".$qid."\nDATE OF ASSIGNMENT: ".$date."\n"; 
            }/** */
        }
        if (!empty($error)){
            echo "ERROR INSERTING DATA";
        }else{
            $sql="SELECT QUIZ_NAME FROM QUIZ_MASTER WHERE QID=:1";
            $rows = $db->ExecSQL($sql,$qid);        
            foreach($rows as $r){
                $qName = $r['QUIZ_NAME'];
            }
        
            echo "          
           
            <div id='pageTitle'>Quiz Assignment</div>
            <div id='formdiv'>
                <div id='header' colspan='2'>QUIZ ASSIGNED!</div>
                <div style='table-row; text-align:center;'>QUIZ Name Is: ".$qName."</div>
                <div style='table-row; text-align:center;'>QUIZ ID Is: ".$qid."</div>                 
                <div id='qtable' style='height:75%;!important'>
                    <table class='table is-bordered is-striped is-narrow is-hoverable is-fullwidth' >
                    ";
    
                $sql = "SELECT USERID FROM QUIZ_USERS WHERE QUIZID=:1 AND TRUNC(DATE_ASSIGNED)=TO_DATE(:2,'mm/dd/yy')";
                $rows = $db->ExecSQL($sql,array($qid,$date));
                if(!$rows){
                    echo "<tr><td>FAILURE TO RETRIEVE USER ID</td></tr>";
                }else{            
                foreach($rows as $r){
                    $sql = "SELECT USERNAME FROM USERLIST WHERE ID=:1";
                    $results = $db->ExecSQL($sql,$r['USERID']);
                    foreach($results as $n){
                        $uName = $n['USERNAME'];
                        echo "<tr><td align:center>USERNAME: </td><td align:center>".$uName."</td></tr>";
                    }                
                    
                }//Ends Foreach
            }
                echo "
                </table><!-- ENDtable-->
                </div><!--Ends tablediv   -->
                <div style='margin-top:.5em; margin-left:70%;'><a href='../Quiz/QUIZ.php' class='button is-info'>Continue to Quiz</a></div>
                </div><!-- END formdiv--> 
                ";
            }//Ends IF $inst
        }
    
    }//ENDS function PROCESS

//********************************************END ASSIGNMENT RESULTS ************************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************CONFIRM ASSIGNMENT *************************************************************************** */


function Confirm(){
    global $db;   
    $qID = $_POST['quizID'];
    $nNum = $_POST['numNames'];
    $sql = "SELECT QUIZ_NAME FROM QUIZ_MASTER WHERE QID= :1";
    $result = $db->ExecSQL($sql,$qID);
    foreach ($result as $r){
        $qName = $r['QUIZ_NAME'];
    }
    echo "   
    <div id='pageTitle'>Quiz Assignment</div>
    <div id='formdiv'>
    <div id='header' colspan='2'>CONFIRM QUIZ ASSIGNMENTS</div>
    <div style='table-row; text-align:center;'><h2><u>Quiz Name Is ".$qName."</u></h2></div>
    <div style='table-row; text-align:center;'><h3>ASSIGNEES<h3></div>       
    <form method='POST' action='' style='height:75%;'>    
    <div style='overflow:scroll; height:95%;'>              
        <table class='table is-bordered is-striped is-narrow is-hoverable is-fullwidth'>
        ";
    
    $count =0; 
    for($i=0; $i < $nNum; $i++){        
        echo "<input type='hidden' name='assignedName_".$i."' value='".$_POST['assignedName_'.$i]."'>";
        if ($count == 2){
            echo "<tr>";
            $count = 0;
            } 
        $sql = "SELECT USERNAME FROM USERLIST WHERE ID =:1";
        $results = $db->ExecSQL($sql,(int)$_POST['assignedName_'.$i]);   
        foreach($results as $r){
            echo "<td style='padding-left:6em; border:thin solid black; width:50%;' align:center>".$r['USERNAME']."</td>";
            if ($count == 2){
                echo "/<tr>";                
            }
            $count++;
        }
    }    
    if ($count == 1){echo "<td style='padding-left:6em; border:thin solid black; width:50%;' align:center></td></tr>";}
    echo "
    </table><!-- END TABLE -->                           
    
    </div><!--Ends tablediv   -->
    <input type='hidden' name='a' value='process'>
    <input type='hidden' name='quizID' value='". $qID."'>
    <input type='hidden' name='numNames' value='".$nNum."'>
    <div style='margin-left:70%; margin-top:.5em;'>
        <span><input type='submit' class='button is-info' value='Confirm'></span>
        <span><a href='Assign_Quiz.php' class='button is-warning' >Cancel</a></span>
    </div>
    </form><!-- END form-->
    </div><!-- END FORMDIV      -->  
    ";
}//ENDS FUNCTION CONFIRM
//***********************************************END CONFIRM ASSIGNMENT *********************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************ENTER ASSIGNMENTS **************************************************************************** */
    function Assignment(){
        global $db;
        echo "        
        <script>

//Javascript FUNCTION to toggle all checkboxes on and off
function checkAll(allBox){  
    var boxes = document.getElementsByClassName('selectedUsers');                                 
    for(var i=0; i<boxes.length; i++)
    {
        if (allBox.checked){
            boxes[i].checked = true;                                                
        }else{
            boxes[i].checked = false;                                               
        }//ENDS IF / ELSE                    
    }//ENDS FOR LOOP                
}//ENDS checkall function 

function sendChecked(){
    var boxes = document.getElementsByClassName('selectedUsers');
    var names = '';
    var numNames = 0;    
    for(var i=0; i<boxes.length; i++)
    {
        if (boxes[i].checked){
            names += \"<input type=hidden value='\" + boxes[i].value + \"' name='assignedName_\" + numNames + \"'>\";
            numNames++;                                              
        }//ENDS IF / ELSE                    
    }//ENDS FOR LOOP
    names+=  \"<input type=hidden value='\" + numNames + \"' name='numNames'>\"
    document.getElementById('chAll').innerHTML = names;    
}
//JQUERY FUNCTION TO POPULATE TABLE WITH NAMES OF EMPLOYEES BASED ON DEPARTMENT   
$(document).ready(function(){  
    $(\"#deptSelect\").change(function(){
        var deptName = $(this).val();
        if(deptName == 'null'){
          
            $(\"#nameTable\").empty();
            $(\"#chAll\").empty();
            $(\"#nameTable\").css(\"display\", \"none\");
        }else{
            $.ajax({
                url: 'quiz_dropdowns.php',
                type: 'POST',
                data: {dept:deptName},
                dataType: 'json',
                success:function(response){
                    var len = response.length;

                    $(\"#nameTable\").empty();
                    $(\"#chAll\").empty();                   
                    $(\"#nameTable\").append(\"<tr><th width='40%'>DEPARTMENT</th><th>EMPLOYEE</th><th>ASSIGN?</th></tr>\");
                    $(\"#nameTable\").css(\"display\", \"\");

                    for( var i = 0; i<len; i++){
                        var id = response[i]['id'];
                        var name = response[i]['name'];
                        var department = response[i]['department'];                
                        $(\"#nameTable\").append(\"<tr nowrap><td align='center' nowrap>\"+department+\"</td><td align='center' nowrap>\"+name+\"<td align='center' nowrap><input type='checkbox' class='selectedUsers' value='\"+id+\"'></td></tr>\");                        
                    }
                    
                    $(\"#chAll\").append(\"<input type=checkbox id='checkAllBox' align='center' onchange='checkAll(this)'>SELECT ALL\");
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(\"FAILED TO RETRIEVE DEPARTMENT DATA\");
                }//ENDS SUCCESS/ERROR OF CALL
            });//ENDS AJAX
        }//ENDS ELSE
    });//ENDS FUNCTION ON CHANGE OF DROPDOWN
});//ENDS ON DOCUMENT READY FUNCTION
</script>


<div id='pageTitle'>Quiz Assignment</div>        
<form method='POST' action=''>
    <div id='formdiv' style='height:45em'>
        <div id='header'>Choose Whom The Quiz shall be assigned To:</div>            
            <div style='margin:0 auto .5em auto; width:80%; '>Department:                    
                <select id='deptSelect'  class='ddown'>
                    <option value='null'>- Select -</option>
                    <option value='IS NOT NULL'>ALL DEPARTMENTS</option>
                    <option value=\"= 'ACCOUNTING'\">ACCOUNTING</option>
                    <option value=\"LIKE '%CLERICAL%'\">ALL CLERICAL</option>
                    <option value=\"= 'CLERICAL'\">CLERICAL</option>
                    <option value=\"= 'CLERICAL - SUIT'\">CLERICAL-SUIT</option> 
                    <option value=\"= 'COLLECTIONS'\">COLLECTIONS</option>
                    <option value=\"= 'COMPLIANCE'\">COMPLIANCE</option>
                    <option value=\"= 'DATA PROCESSING'\">DATA PROCESSING</option>
                    <option value=\"= 'ENFORCEMENT'\">ENFORCEMENT</option>
                    <option value=\"= 'EXECUTIVE'\">EXECUTIVE</option>
                    <option value=\"= 'HUMAN RESOURCES'\">HUMAN RESOURCES</option>
                    <option value=\"= 'IT DEPARTMENT'\">IT DEPARTMENT</option>                            
                    <option value=\"= 'LEGAL'\">LEGAL</option>
                    <option value=\"= 'MAIL ROOM'\">MAIL ROOM</option>                           
                    <option value=\"= 'SKIP TRACING'\">SKIP TRACING</option>                            
                </select>
            </div>
            <div style='overflow:scroll; height:75%;' >
            <table class='table is-bordered is-striped is-narrow is-hoverable is-fullwidth' id='nameTable'></table>
            </div><br>             
        <div style='display:inline-block; width:70%; align-content:center;' id='chAll' align='center'>            
        </div>    
        <div style='display:inline; width:30%; '>
            <input type='hidden' name='quizID' value='".$_POST['quizID']."'>
            <input type='hidden' name='a' value='confirm'>
            <button type='submit' class='button is-info' onclick='sendChecked()' value='Submit'>Submit</button>
            <a href='Assign_Quiz.php' class='button is-warning' >Cancel</a>
        </div><!-- END bottomdiv-->            
    </div><!-- END FORMDIV      --> 
</form><!-- END form-->   
";

    }//ENDS FUNCTION ASSIGNMENT

//*******************************************END ENTER ASSIGNMENTS *************************************************************************** */
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
        <div id='pageTitle'>Quiz Assignment</div>
        <br>
        <div id='formdiv' style='height:auto;'>
        <div id='header'>Choose Which Exam You Would Like to Assign</div><br>  
        <form method='POST' action=''> 
        <div>
            <table class='table is-fullwidth is-striped' style='padding-top:1.5em;'>
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
            <div style='margin-left:70%; padding-top:.5em;' nowrap>
            <input type='hidden' name='a' value='assignment'>
                <span><button type='submit' class='button is-info' value='Submit'>Submit</button></span>
                <span><a href='Assign_Quiz.php' class='button is-warning' >Cancel</a></span>
            </div><!-- END bottomdiv-->  
            </form><!-- END form-->
            </div><!-- END FORMDIV      -->  
            ";                     

    }//ENDS FUNCTION MENU
//*************************************************END MENU ********************************************************************************** */
?>
</BODY>
</HTML>
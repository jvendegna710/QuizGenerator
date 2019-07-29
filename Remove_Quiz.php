<!DOCTYPE HTML>
<HEAD>
<TITLE>Remove Quiz Assignment</TITLE>
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
    margin-left:1rem;
    margin-right:1rem;
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
    case 'removal' :               
        Removal();
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
    
    //echo "You made it here!<br>";
    $userID = $_POST['user'];
    $sql = "SELECT USERNAME FROM USERLIST WHERE ID =".$userID;
    $result = $db->ExecSQL($sql);
    $user = $result[0]['USERNAME'];
    echo "
    <div id='pageTitle'>Quiz Assignment</div>
    <div id='formdiv'>
    <div class='h4' colspan='2' style='text-align:center'>QUIZZES REMOVED</div>";
    echo "\r\n<br> USER: ".$user."<br><br>";
    //print_r($_REQUEST);
    
    for ($i = 0; $i < $_POST['qiCount']; $i++){
        $qid = $_POST['quizID_'.$i];
        $date = $_POST['quizDate_'.$i];
        echo "\r\n<div>QUIZ ID: ".$qid;
        echo "\r\n<br> QUIZ DATE: ".$date."<br>";
        //$sql = "DELETE FROM QUIZ_USERS WHERE USERID = :1 AND QUIZID =:2 AND TRUNC(DATE_ASSIGNED) =TO_DATE(':3', 'DD-MON-YY')";
        $sql = "DELETE FROM QUIZ_USERS WHERE USERID = ".$userID." AND QUIZID =".$qid." AND TRUNC(DATE_ASSIGNED) = TO_DATE('".$date."', 'DD-MON-YY')";
        //$params = array($userID, $qid, $date);
        if($db->ExecSQL($sql)){
            echo "DELETE SUCCESSFUL!</div>";
        }else{
            echo "DELETE FAILED</div>";
        }
        
    }/** */
  echo "</div>";//ends formdiv

}//ENDS function PROCESS

//********************************************END ASSIGNMENT RESULTS ************************************************************************** */
/*
################################################################################################################################################
*/
//***********************************************CONFIRM ASSIGNMENT *************************************************************************** */


function Confirm(){
    global $db;
    //echo "Requests are ";
    //print_r($_REQUEST);
    $idlist = '';
    $qIDs = array();
    $qDates = array();
    $inlist = '( ';
    $hiddenInputs = '';
    $qc = 0;
    $qiCount = count($_POST['quizIDs']);
    foreach($_POST['quizIDs'] as $q){
        $qIDsplit = explode(" ",$q);
        array_push($qIDs, $qIDsplit[0]);
        array_push($qDates, $qIDsplit[1]);
        $hiddenInputs .= "\r\n<input type='hidden' name='qiCount' value='".$qiCount."'>";
        $hiddenInputs .= "\r\n<input type='hidden' name='quizID_{$qc}' value='".$qIDsplit[0]."'>";
        $hiddenInputs .= "\r\n<input type='hidden' name='quizDate_{$qc}' value='".$qIDsplit[1]."'>";
        $qc++;
        //$idlist .= '<input type="hidden" name="quizID[]" value="'. $value. '">'."\n";
    }
      
    foreach($qIDs as $q){
        $inlist .= "'".$q."', ";
    }
   
    $inlist = substr($inlist, 0, -2);
    $inlist .= ' )';
    
    
    $qNameList = "<tr><th>Quiz Name</th><th>Quiz Date</th></tr>";
    
    $sql = "SELECT QUIZ_NAME FROM QUIZ_MASTER WHERE QID IN ".$inlist;
    $results = $db->ExecSQL($sql);
    
    $qCount = count($_POST['quizIDs']);
    for ($i = 0; $i < $qCount; $i++){
        $qNameList .= "<tr><td>".$qIDs[$i]."</td><td>".$qDates[$i]."</td></tr>";        
    }

    echo "   
    <div id='pageTitle'>Quiz Assignment</div>
    <div id='formdiv'>
    <div id='header' colspan='2'>CONFIRM REMOVAL OF QUIZ ASSIGNMENTS</div>
    <div style='table-row; text-align:center;'><h2><u>User Name Is ".$_POST['user']."</u></h2></div>              
    <form method='POST' action='' style='height:75%;'>    
    <div style='overflow:scroll; height:90%;'>              
        <table class='table table-bordered table-striped table-narrow table-hoverable' style='width:100%;'>
    ";/** */
    
        echo $qNameList;
   
    echo "
    </table><!-- END TABLE -->                           
    
    </div><!--Ends tablediv   -->
    <input type='hidden' name='a' value='process'>
    ".$hiddenInputs."
    <input type='hidden' name='user' value='".$_POST['user']."'>
    <div style='margin-left:70%; margin-top:.5em; margin-bottom:.5em;'>
        <span><input type='submit' class='btn btn-primary' value='Confirm'></span>
        <span><a href='Assign_Quiz.php' class='btn btn-warning' >Cancel</a></span>
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
    function Removal(){
        global $db;
        $userID = $_POST['user'];
        $quizSelect ='';
        $sql = "SELECT QUIZID, QUIZ_NAME, TRUNC(DATE_ASSIGNED) DATE_ASSIGNED
                FROM QUIZ_MASTER 
                    INNER JOIN QUIZ_USERS ON (QID = QUIZID)
                WHERE USERID =:1";
        $results = $db->ExecSql($sql, $userID);
        foreach($results as $r){
            $quizSelect .= "<option value='".$r['QUIZID']." ".$r['DATE_ASSIGNED']."'>".$r['QUIZ_NAME']."&nbsp&nbsp&nbsp&nbsp&nbsp|&nbsp&nbsp&nbsp&nbsp&nbsp".$r['DATE_ASSIGNED']."</option>";
            //$quizSelect .= "<tr><td><input type='checkbox' class='selectedUsers' value='".$r['QUIZID']." ".$r['DATE_ASSIGNED']."'</td><td>".$r['QUIZNAME']."</td><td>".$r['CLIENT_GROUP']."</td><td>".$r['DATE_ASSIGNED']."</td></tr>"
        }
        echo "  
        
<div id='pageTitle'>Remove Quiz Assignment</div>        
<form method='POST' action=''>
    <div id='formdiv' style='height:45em; width:65%;'>
        <div id='header'>Which Quizzes?</div>           
            
            <div style='overflow:scroll; height:75%;' >
            <div>
            <table class='table table-striped' style='padding-top:1.5em; width:100%;'>
                <tr class='c_row'>
                    <td class='qlabel'>Quiz Name & Subject:</td>
                    <td style='text-align:left'>
                        <select id='quizNameDD' name='quizIDs[]' style='width:100%;' multiple>
                        ".$quizSelect."                                                          
                        </select>
                    </td>
                </tr>                            
            </table>
            </div>
            <input type='hidden' name='a' value='confirm'>
            <input type='hidden' name='user' value='".$_POST['user']."'>
            <button type='submit' class='btn btn-primary' onclick='sendChecked()' value='Submit'>Submit</button>
            <a href='Remove_Quiz.php' class='btn btn-warning' >Cancel</a>
        </div><!-- END bottomdiv-->            
    </div><!-- END FORMDIV      --> 
</form><!-- END form-->
</div>   
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
        $usersArray = array();
        $userSelect = '';
       // $sql = "SELECT CLIENT_GROUP FROM QUIZ_MASTER GROUP BY CLIENT_GROUP";
       /*
        $sql = "SELECT DISTINCT CLIENT_GROUP
                FROM CLIENT_GROUP WHERE CLIENT_GROUP <> '(NOT USED)'       
                ORDER BY CLIENT_GROUP";/** */
        $sql = "SELECT DISTINCT USERID, USERNAME FROM QUIZ_USERS 
                WHERE (SELECT ACTIVE FROM USERLIST WHERE USERID=ID) = 'Y'
                ORDER BY USERNAME";                    
        $results = $db->ExecSQL($sql);
        foreach($results as $r){
                array_push($usersArray, $r['USERID']);
                $userSelect .= "<option value='".$r['USERID']."'>".$r['USERNAME']."</option>";
        }//ENDS FOREACH FOR SQL CALL
        echo "
        
        <div id='pageTitle'>Remove Quiz Assignment</div>
        <br>
        <div id='formdiv' style='height:auto; max-height:40em; width:75%;'>
        <div id='header'>Choose Which Exam You Would Like to Assign</div><br>  
        <form method='POST' action=''> 
        <div>
            <table class='table table-striped' style='padding-top:1.5em; width:100%;'>
                <tr class='c_row'>
                    <td class='qlabel'>User:</td>
                    <td style='text-align:center'>
                        <select id='user' name='user' class='ddown'>
                            <option value='null'>- Select -</option>
                           ".$userSelect."
                        </select>
                        </td>
                    </tr>                              
                </table>
            </div>
            <div style='margin-left:70%; margin-bottom:.5em; padding-top:.5em;' nowrap>
            <input type='hidden' name='a' value='removal'>
                <span><button type='submit' class='btn btn-primary' value='Submit'>Submit</button></span>
                <span><a href='Remove_Quiz.php' class='btn btn-warning' >Cancel</a></span>
            </div><!-- END bottomdiv-->  
            </form><!-- END form-->
            </div><!-- END FORMDIV      -->  
            ";                     

    }//ENDS FUNCTION MENU
//*************************************************END MENU ********************************************************************************** */
?>
</BODY>
</HTML>
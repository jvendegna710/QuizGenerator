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
<TITLE>View a Quiz</TITLE>
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
    case 'view' :               
        View();
        break;      
    default :
        Menu();
        break;   
} 

 /*
################################################################################################################################################
*/
//***********************************************View Quiz *************************************************************************** */

    function View(){
        global $db;
              

        if (!empty($_POST['quizID'])){
            $qID = $_POST['quizID'];
            
            $tableinput ="";

            $sql = "SELECT QUESTION,
                        ANSWER,
                        CHOICE1,
                        CHOICE2,
                        CHOICE3,
                        CHOICE4,
                        ACTIVE,
                        QUIZ_NAME
                    FROM QUIZ_QUESTIONS, QUIZ_MASTER
                    WHERE QUIZ_ID = :1
                    AND QID = QUIZ_ID 
                    ORDER BY QUESTION_ID";
            
            $result = $db->ExecSQL($sql, array($qID));        
            $returnArray = array();            
            foreach($result as $r){
                $question = $r['QUESTION'];
                $answer = $r['ANSWER'];
                $choice1 = $r['CHOICE1'];
                $choice2 = $r['CHOICE2'];
                $choice3 = $r['CHOICE3'];
                $choice4 = $r['CHOICE4'];
               
                $tableinput .= "<tr>
                                    <td align='left' >".urldecode($question)."</td>
                                    <td align='left' >".urldecode($answer)."</td>
                                    <td align='center' >".urldecode($choice1)."</td>
                                    <td align='center' >".urldecode($choice2)."</td>
                                    <td align='center' >".urldecode($choice3)."</td>
                                    <td align='center' >".urldecode($choice4)."</td>
                                </tr>";
            }
        }
        $qName = $result[0]['QUIZ_NAME'];
        if($_REQUEST['archive'] == 'on'){
            //echo "It's on!<br>";
            //echo "Archive is ".$_REQUEST['archive'];
            if ($result[0]['ACTIVE'] == 'N'){
                $arch = 'Y';
            }else {$arch = 'N';}

            $result[0]['ACTIVE'] = $arch;

            $sql = "UPDATE QUIZ_MASTER SET ACTIVE = :1 WHERE QID = :2";
            if($db->ExecSQL($sql, array($arch, $_POST['quizID']))){
                echo 'Archive Status Successfully changed<br>';
                //echo $sql.'<br>'.$arch.'<br>'.$_POST['quizID'];
            }else{
                echo 'Archive Status Change Failed';
            }
        }

        $archiveStatus = '';
        if ($result[0]['ACTIVE'] == 'N'){
            $archiveStatus = "<span class='container btn-outline-danger'>ARCHIVED</span>";
        }else{
            $archiveStatus = "<span class='container btn-outline-success'>ACTIVE</span>";
        }
        //echo $archiveStatus;
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
    <div id='pageTitle'>Quiz View</div>
        <div class='container'><center><h4>".$qName."</h4></center></div>
        <br><br>        
        <div class='container' style='margin-left:50%;'>
            <form  method='POST' action=''>
                <div class='form-check'>
                    <input type = 'hidden' name='a' value='".$_REQUEST['a']."'>
                    <input type = 'hidden' name='quizID' value='".$qID."'>
                    <span style='font-size:1rem; font-weight:500; line-height:1.2; margin-right:20%; '>CURRENT STATUS: ".$archiveStatus."</span>
                    <input class='form-check-input' type='checkbox' name='archive' id='archive'>
                    <label class='form-check-label' for='archive'>
                     Toggle Archive Status?
                    </label>
                    <input type='submit' class='btn btn-sm btn-primary' value='SUBMIT' style='margin-bottom:.3rem; margin-left:.3rem;'>
                </div>
            </form>
    </div>
    <div id='qdiv'>
                           
            <div style='overflow:scroll; height:95%;' >
                <table class='table table-bordered table-striped table-condensed table-hoverable table-sm' id='nameTable'>
                    <thead>
                        <th style='width:20%;'>Question</th>
                        <th style='width:16%;'>Answer</th>
                        <th style='width:16%;'>Choice1</th>
                        <th style='width:16%;'>Choice2</th>
                        <th style='width:16%;'>Choice3</th>
                        <th style='width:16%;'>Choice4</th>
                    </thead>
                    ".$tableinput."
                </table>
            </div>
            <br>           
    </div><!-- END FORMDIV  -->   
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
                    data: {vclient:clientName},
                    dataType: 'json',
                    success:function(response){
                        var len = response.length;
                        $(\"#quizNameDD\").empty();
                        $(\"#quizNameDD\").append(\"<option value='null'>- Select -</option>\");
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            var subject = response[i]['subject'];
                            var active = response[i]['active'];
                            if (active == 'Y'){
                                $(\"#quizNameDD\").append(\"<option value='\"+id+\"' class='btn-outline-success'>\"+name+\" | \"+subject+\"</option>\");
                            }else{
                                $(\"#quizNameDD\").append(\"<option value='\"+id+\"' class='btn-outline-danger'>\"+name+\" | \"+subject+\"</option>\");
                            }             
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(\"FAILED TO RETRIEVE QUIZ NAME DATA \");
                    }//ENDS SUCCESS/ERROR OF CALL
                });//ENDS AJAX
            });//ENDS FUNCTION ON CHANGE OF DROPDOWN
        });//ENDS ON DOCUMENT READY FUNCTION
        </script>
        <div id='pageTitle'>Quiz View</div>
        <br>
        <div id='formdiv' style='height:auto;'>
        <div id='header'>Choose Which Exam You Would Like to View</div><br>  
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
            <input type='hidden' name='a' value='view'>
                <span><button type='submit' class='btn btn-primary' value='Submit'>Submit</button></span>
                <span><a href='Assign_Quiz.php' class='btn btn-warning' >Cancel</a></span>
            </div><!-- END bottomdiv-->  
            </form><!-- END form-->
            </div><!-- END FORMDIV      -->  
            ";                     

    }//ENDS FUNCTION MENU
//*************************************************END MENU ********************************************************************************** */
?>
</BODY>
</HTML>
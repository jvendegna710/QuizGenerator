<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
include_once('\\inetpub\\wwwroot\\user.php.inc');
global $db;

if (!empty($_POST['client'])){
    $client = $_POST['client'];
       
    $sql = "SELECT QID, QUIZ_NAME, Q_SUBJ FROM QUIZ_MASTER WHERE CLIENT_GROUP=:1";
    $result = $db->ExecSQL($sql,array($client));
    $returnArray = array();
    
    foreach($result as $r){
        $id = $r['QID'];
        $name = $r['QUIZ_NAME'];
        $qSubj = $r['Q_SUBJ'];
        $returnArray[] = array("id"=>$id, "name" => $name, "subject" => $qSubj);
    }
    
}else if (!empty($_POST['user'])){
    $user = $_POST['user'];      
    $sql = "SELECT QUIZID FROM QUIZ_USERS WHERE USERID=:1";
    $result = $db->ExecSQL($sql,$user);    
    $returnArray = array(); 

    foreach($result as $r){
    $id = $r['QUIZID'];
    $sql = "SELECT QUIZ_NAME, CLIENT_GROUP FROM QUIZ_MASTER WHERE QID =:1 ORDER BY CLIENT_GROUP";
    $responses = $db->ExecSQL($sql,array($r['QUIZID']));
        foreach($responses as $p){
        $qName = $p['QUIZ_NAME'];
        $group = $p['CLIENT_GROUP'];
        $returnArray[] = array("name" => $qName, "quizID" => $id, "group"=> $group) ;
        }
    }
     
}else if (!empty($_POST['dept'])){
    $dept = $_POST['dept'];
    // department id
    $sql = "SELECT DEPARTMENT, USERNAME, ID FROM USERLIST 
            WHERE ACTIVE = 'Y' AND ID<>86 AND ID<>58 AND ID<>1225 AND ID<>663 AND ID<>1743 AND ID<>60 AND DEPARTMENT ".$dept." ORDER BY DEPARTMENT, USERNAME";
    $result = $db->ExecSQL($sql);
        
    $returnArray = array();

    foreach($result as $r){
        $id = $r['ID'];
        $name = $r['USERNAME'];
        $department = $r['DEPARTMENT'];
        $returnArray[] = array("id"=>$id, "name" => $name, "department" => $department);
    }
}else {
    $returnArray = array("name" => 'didNotRetreieveResults');
}
// encoding array to json format
echo json_encode($returnArray);
/** */
?>
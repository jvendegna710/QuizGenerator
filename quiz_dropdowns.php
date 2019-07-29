<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
include_once('\\inetpub\\wwwroot\\user.php.inc');
global $db;

if (!empty($_POST['client'])){ //this is from Assign Quiz 
    $client = $_POST['client'];
       
    $sql = "SELECT QID, QUIZ_NAME, Q_SUBJ FROM QUIZ_MASTER WHERE CLIENT_GROUP=:1 AND ACTIVE = 'Y'";
    $result = $db->ExecSQL($sql,array($client));
    $returnArray = array();
    
    foreach($result as $r){
        $id = $r['QID'];
        $name = $r['QUIZ_NAME'];
        $qSubj = $r['Q_SUBJ'];
        $returnArray[] = array("id"=>$id, "name" => $name, "subject" => $qSubj);
    }
    
}else if (!empty($_POST['vclient'])){ //this is from View Quiz
    $client = $_POST['vclient'];
       
    $sql = "SELECT QID, QUIZ_NAME, Q_SUBJ, ACTIVE FROM QUIZ_MASTER WHERE CLIENT_GROUP=:1";
    $result = $db->ExecSQL($sql,array($client));
    $returnArray = array();
    
    foreach($result as $r){
        $id = $r['QID'];
        $name = $r['QUIZ_NAME'];
        $qSubj = $r['Q_SUBJ'];
        $active = $r['ACTIVE'];
        $returnArray[] = array("id"=>$id, "name" => $name, "subject" => $qSubj, "active" => $active);
    }
    
}else if (!empty($_POST['user'])){ //this is for Quiz and Quiz Results
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
     
}else if (!empty($_POST['userR'])){ //NOT SURE IF THIS IS USED BY ANY PAGE
    $user = $_POST['user'];      
    $sql = "SELECT QUIZID FROM QUIZ_USERS WHERE USERID=:1";
    $result = $db->ExecSQL($sql,$user);    
    $returnArray = array(); 

    foreach($result as $r){
    $id = $r['QUIZID'];
    $sql = "SELECT QUIZ_NAME, CLIENT_GROUP FROM QUIZ_MASTER WHERE QID =:1 AND ATTEMPTS > 0 ORDER BY CLIENT_GROUP";
    $responses = $db->ExecSQL($sql,array($r['QUIZID']));
        foreach($responses as $p){
        $qName = $p['QUIZ_NAME'];
        $group = $p['CLIENT_GROUP'];
        $returnArray[] = array("name" => $qName, "quizID" => $id, "group"=> $group) ;
        }
    }
     
}else if (!empty($_POST['dept'])){ //FOR ASSIGN QUIZ TO DETERMINE DEPARTMENT
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
}else if (!empty($_POST['department'])){ //FOR REVIEW QUIZ TO DETERMINE WHICH QUIZZES DEPENDING ON DEPARTMENT
    $dept = $_POST['department'];
    // department id
    $sql = "SELECT QUIZ_NAME, QID, CLIENT_GROUP
    FROM QUIZ_MASTER
   WHERE QID IN (SELECT QUIZID
                   FROM QUIZ_USERS
                  WHERE USERID IN (SELECT ID
                                     FROM USERLIST
                                    WHERE DEPARTMENT = '{$dept}'))";
    $result = $db->ExecSQL($sql);
        
    $returnArray = array();

    foreach($result as $r){
        $qid = $r['QID'];
        $qname = $r['QUIZ_NAME'];
        $group = $r['CLIENT_GROUP'];
        $returnArray[] = array("name" => $qname, "quizID" => $qid, "group"=> $group);        
    }
}else {
    $returnArray = array("qid" => 'didNotRetreieveResults');
}
// encoding array to json format
echo json_encode($returnArray);
/** */
?>
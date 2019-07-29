<?php
include_once('\\inetpub\\wwwroot\\database.php.inc');
$sql = "SELECT ID
FROM   USERLIST U
WHERE  NOT EXISTS (SELECT 1 
                   FROM   QUIZ_USERS Q 
                   WHERE  Q.USERID = U.ID)
       AND ACTIVE = 'Y'
       AND USERNAME LIKE '% %'
       AND USERNAME NOT LIKE '%ABC%'
       AND USERNAME NOT LIKE '%SYSTEMS%'
       AND USERNAME NOT LIKE '%LOGIN%'";

$rows = $db->ExecSQL($sql);

foreach ($rows as $r){
    $name =$r['ID'];
    $qid = 44;
    $sql = "INSERT INTO QUIZ_USERS (USERID,QUIZID,DATE_ASSIGNED) VALUES (:1,:2,SYSDATE)";
            $results = $db->ExecSQL($sql,array($name,$qid));
}

?>
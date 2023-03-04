<?php

$months_array = ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"];
$zones = ["1" => " Zone 1","2" => " Zone 2","3" => " Zone 3","4" => " Zone 4"];
function testLoginAgent($db,$username,$password){
    $password_hashed = sha1($password);
    $req = $db->prepare("SELECT * FROM agents WHERE username=? AND password=? LIMIT 1");
    $req->execute(array($username,$password_hashed));
    $res=$req->fetch();
    if ($res){
        // Disconnect other type of user
        session_start();
        $_SESSION = [];
        session_destroy();
        // Connecting the user
        session_start();
        $_SESSION["agent"] = true;
        $_SESSION["id_agent"] = $res["id_agent"];
        return true;
    }
    else {
        return false;
    }
}

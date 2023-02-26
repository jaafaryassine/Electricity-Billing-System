<?php

$months_array = ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"];

function testLoginAdmin($db,$username,$password){
    $password_hashed = sha1($password);
    $req = $db->prepare("SELECT * FROM admin WHERE username=? AND password=? LIMIT 1");
    $req->execute(array($username,$password_hashed));
    $res=$req->fetch();
    if ($res){
        session_start();
        $_SESSION["admin"] = true;
        $_SESSION["username"] = $res["username"];
        return true;
    }
    else {
        return false;
    }
}

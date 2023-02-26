<?php
$months_array = ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"];

function testLoginClient($db,$email,$password){
    $password_hashed = sha1($password);
    $req = $db->prepare("SELECT * FROM clients WHERE email=? AND password=? LIMIT 1");
    $req->execute(array($email,$password_hashed));
    $res=$req->fetch();
    if ($res){
        session_start();
        $_SESSION["id_client"] = $res["id_client"];
        $_SESSION["first_name"] = $res["first_name"];
        $_SESSION["last_name"] = $res["last_name"];
        $_SESSION["address"] = $res["first_name"];
        $_SESSION["id_zone"] = $res["id_zone"];
        $_SESSION["email"] = $res["email"];
        return true;
    }
    else {
        return false;
    }
}
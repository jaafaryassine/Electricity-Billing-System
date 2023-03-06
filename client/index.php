<?php
session_start();
if (isset($_SESSION["id_client"])){
    header("location:my-bills.php");
}
else{
    header("location:login.php");
}

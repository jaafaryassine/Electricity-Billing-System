<?php
session_start();
if ($_SESSION){
    header("location:my-bills.php");
}
else{
    header("location:login.php");
}

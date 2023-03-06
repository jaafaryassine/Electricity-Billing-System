<?php
session_start();
if (isset($_SESSION["id_agent"])){
    header("location:add-year-consommation.php");
}
else{
    header("location:login.php");
}

<?php
require ("../backend/connect_db.php");
require("../backend/client/functions.php");
require ("../backend/client/Client.php");
session_start();

if($_SESSION["email"]){
    $client = new Client($_SESSION);
    if (isset($_POST["send"])){
        $objet = trim(htmlspecialchars($_POST["objet"]));
        $msg = trim(htmlspecialchars($_POST["message"]));
        if($client->send_reclamation($db,$objet,$msg)){
            echo "sent succesfully";
        }
        else{
            echo "Failed";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <body>
    <?php require ("./layouts/navbar.html") ?>
    <section class="py-4 py-xl-5">
        <div class="container">
            <div class="row d-flex justify-content-center">

            </div>
        </div>
    </section>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    </body>

    </html>
    </body>
    </html>

    <?php
}
else {
    header("location: login.php");
}
?>
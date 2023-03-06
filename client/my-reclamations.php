<?php
require ("../backend/connect_db.php");
require("../backend/client/functions.php");
require ("../backend/client/Client.php");
session_start();

if($_SESSION["email"]){
    if (isset($_GET["type"])){
    $client = new Client($_SESSION);
    $not_answered_reclamations = $client->get_not_answered_reclamations($db);
    $answered_reclamations = $client->get_answered_reclamations($db);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <body>
    <?php require ("./layouts/navbar.html") ?>
    <section class="py-4 py-xl-5">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="d-flex flex-column" id="content-wrapper">
                    <div id="content">
                        <div class="container-fluid">
                            <div class="d-sm-flex justify-content-between align-items-center mb-4">
                                <h3 class="text-dark mb-0">Mes réclamations</h3>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" onchange="changeReclamations(this)" name="type-reclamation" id="flexRadioDefault1" value="0" <?php if ($_GET["type"]=='not_answered') echo "checked"?> >
                                        <label class="form-check-label" for="flexRadioDefault1" <?php if ($_GET["type"]=='not_answered') echo "checked"?> >
                                            Non Traitées
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" onchange="changeReclamations(this)" value="1" name="type-reclamation" id="flexRadioDefault2" <?php if ($_GET["type"]=='answered') echo "checked"?> >
                                        <label class="form-check-label" for="flexRadioDefault2" <?php if ($_GET["type"]=='answered') echo "checked"?> >
                                            Traitées
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="not_answered">
                                <?php
                                if ($_GET["type"]=='not_answered'){
                                    foreach ($not_answered_reclamations as $rec){ ?>
                                        <div class="card shadow mb-2">
                                            <div class="card-header py-3">
                                                <p class="text-primary m-0 fw-bold">Réclamation</p>
                                            </div>
                                            <div class="card-body">
                                                <p><?=$rec["message"]?></p>
                                            </div>
                                            <div class="card-footer">
                                                <strong class="text-info">Objet : </strong><strong><small><?=$rec["objet"]?></small></strong>
                                            </div>
                                        </div>
                                    <?php }
                                    if (count($not_answered_reclamations) == 0) { ?>
                                        <div class="alert alert-info">
                                            <strong>Vous n'avez aucune réclamation non traitée</strong>
                                        </div>
                                    <?php }
                                } ?>


                            </div>
                            <div id="answered">
                                <?php
                                if ($_GET["type"]=='answered'){
                                    foreach ($answered_reclamations as $rec){ ?>
                                        <div class="card shadow mb-2">
                                            <div class="card-header py-3">
                                                <p class="text-primary m-0 fw-bold">Réclamation</p>
                                            </div>
                                            <div class="card-body">
                                                <p><?=$rec["client_msg"]?></p>
                                                <p><strong>Réponse : <?=$rec["answer"]?></strong></p>
                                            </div>
                                            <div class="card-footer">
                                                <strong class="text-info">Objet : </strong><strong><small><?=$rec["objet"]?></small></strong>
                                            </div>
                                        </div>
                                    <?php }
                                    if (count($answered_reclamations) == 0) { ?>
                                        <div class="alert alert-info">
                                            <strong>Vous n'avez aucune réclamation déjà traitée</strong>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script>
        function changeReclamations(el) {
            console.log(el.value);
            let not_answered_element = document.getElementById("not_answered");
            let answered_element = document.getElementById("answered");
            if (el.value == "0"){
                window.location.replace("my-reclamations.php?type=not_answered");
                console.log("not");
            }
            else {
                window.location.replace("my-reclamations.php?type=answered");
                console.log("answered");
            }
        }
    </script>
    </body>

    </html>
    </body>
    </html>

    <?php
    }
}
else {
    header("location: login.php");
}
?>
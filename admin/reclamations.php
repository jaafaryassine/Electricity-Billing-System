<?php
require("../backend/connect_db.php");
require("../backend/admin/Admin.php");
session_start();
if (isset($_SESSION["admin"])) {
    if ($_SESSION["admin"]) {
        if (isset($_GET["type"])){
        $admin = new Admin();
        $not_answered_reclamations = $admin->get_not_answered_reclamations($db);
        $answered_reclamations = $admin->get_answered_reclamations($db,'answered');
        if (isset($_POST["answer"])){
            $id_rec = trim(htmlspecialchars($_POST["answer"]));
            $msg = trim(htmlspecialchars($_POST["msg-response"]));
            if ($admin->answer_recmlamation($db,$id_rec,$msg))
                header("Refresh::0");
            else echo "FAILED";
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
            <title>Table - Elec-Bill</title>
            <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet"
                  href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
            <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
        </head>

        <body id="page-top">
        <div id="wrapper">
            <?php require("./layouts/sidebar.html") ?>
            <div class="d-flex flex-column" id="content-wrapper">
                <div id="content">
                    <?php require("./layouts/navbar.html") ?>
                    <div class="container-fluid">
                        <div class="d-sm-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-dark mb-0">Réclamations</h3>
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
                                        <p class="text-primary m-0 fw-bold"><a href="profile.php?id_client=<?=$rec["id_client"]?>"><?=$rec["first_name"]." ".$rec["last_name"] ?></a></p>
                                    </div>
                                    <div class="card-body">
                                        <p><?=$rec["message"]?></p>
                                    </div>
                                    <div class="card-footer">
                                        <strong class="text-info">Objet : </strong><strong><small><?=$rec["objet"]?></small></strong>
                                        <button class="btn btn-outline-info float-end" data-bs-toggle="modal" data-bs-target="#rec<?=$rec["id_reclamation"]?>">Répondre</button>
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
                                        <p class="text-primary m-0 fw-bold"><a href="profile.php?id_client=<?=$rec["id_client"]?>"><?=$rec["first_name"]." ".$rec["last_name"] ?></a></p>
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
                <?php foreach ($not_answered_reclamations as $rec){ ?>
                    <!-- Modal -->
                    <div class="modal fade" id="rec<?=$rec["id_reclamation"]?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form class="modal-content" method="post">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Votre réponse pour la réclamation</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Réponse</span>
                                        <input type="text" class="form-control" name="msg-response"
                                               aria-describedby="basic-addon1" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary" name="answer" value="<?=$rec["id_reclamation"]?>">Répondre</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Modal -->
                <?php } ?>

                <footer class="bg-white sticky-footer">
                    <div class="container my-auto">
                        <div class="text-center my-auto copyright"><span>Copyright © Elec-Bill 2023</span></div>
                    </div>
                </footer>
            </div>
            <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
        </div>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/bs-init.js"></script>
        <script src="assets/js/theme.js"></script>
        <script>
            function changeReclamations(el) {
                console.log(el.value);
                let not_answered_element = document.getElementById("not_answered");
                let answered_element = document.getElementById("answered");
                if (el.value == "0"){
                    window.location.replace("reclamations.php?type=not_answered");
                    console.log("not");
                }
                else {
                    window.location.replace("reclamations.php?type=answered");
                    console.log("answered");
                }
            }
        </script>
        </body>

        </html>
        <?php
        }
    } else {
        header("location: login.php");
    }
} else {
    header("location: login.php");
}
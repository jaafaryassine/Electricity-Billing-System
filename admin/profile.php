<?php
require("../backend/connect_db.php");
require("../backend/admin/functions.php");
require("../backend/admin/Admin.php");
session_start();
if (isset($_SESSION["admin"])) {
    if ($_SESSION["admin"]) {
        if (isset($_GET["id_client"])){
            $admin = new Admin();
            $client_infos = $admin->get_client_by_id($db,$_GET["id_client"]);
            $all_bills = $admin->get_bills_by_client($db,$_GET["id_client"]);
            $all_consommations = $admin->get_consommations_by_client($db,$_GET["id_client"]);

            if (isset($_POST["update_client"])){
                $new_infos = [];
                $new_infos["first_name"] = trim(htmlspecialchars($_POST["first_name"]));
                $new_infos["last_name"] = trim(htmlspecialchars($_POST["last_name"]));
                $new_infos["address"] = trim(htmlspecialchars($_POST["address"]));
                $new_infos["id_zone"] = trim(htmlspecialchars($_POST["id_zone"]));
                if ($admin->update_client_information($db,$client_infos["id_client"],$new_infos))
                    header("Refresh:0");
                else
                    echo "FAILED";
            }

            if (isset($_POST["save_change"])){
                $id_consommation = trim(htmlspecialchars($_POST["save_change"]));
                $new_consommation = trim(htmlspecialchars($_POST["qt_consommation"]));
                if ($admin->update_cons($db,$new_consommation,$id_consommation)){
                    header("location:all-consommations.php");
                }
                else {
                    echo "Failed";
                }
            }
            if (isset($_POST["generate_bill"])){
                $id_cons = trim(htmlspecialchars($_POST["generate_bill"]));
                $info_cons = $admin->get_info_consommation_by_id($db,$id_cons);
                if (!$admin->generateBill($db,$info_cons)){
                    echo "Failed";
                }
            }
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
            <title>Profile - Elec-Bill</title>
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
                        <h3 class="text-dark mb-4">Profile</h3>
                                <div class="row">
                                    <div class="col">
                                        <div class="card shadow mb-3">
                                            <div class="card-header py-3">
                                                <p class="text-primary m-0 fw-bold">Informations du client</p>
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="Nom"><strong>Nom</strong></label>
                                                                <input class="form-control" type="text" id="Nom" placeholder="Nom" name="last_name" value="<?=$client_infos["last_name"]?>"></div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3"><label class="form-label" for="first_name"><strong>Prénom</strong></label>
                                                                <input class="form-control" type="text" id="first_name" placeholder="Prénom" name="first_name" value="<?=$client_infos["first_name"]?>"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3"><label class="form-label"for="Adresse"><strong>Adresse</strong></label>
                                                                <input class="form-control" type="text" id="Adresse" placeholder="Adresse" name="address" value="<?=$client_infos["address"]?>"></div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3"><label class="form-label" for="id_zone"><strong>Zone Géographique</strong></label>
                                                                <select class="form-select" aria-label="Default select example" id="id_zone" name="id_zone">
                                                                <?php foreach ($zones as $id => $zone) { ?>
                                                                    <option value="<?=$id?>" <?php  if ($id == $client_infos["id_zone"]) echo "selected" ?>> <?=$zone?></option>
                                                                <?php } ?>
                                                            </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <button class="btn btn-primary btn-sm float-end" type="submit" name="update_client">
                                                            Enregistrer les changements
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <div class="card shadow clients-bills">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 fw-bold">Factures</p>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th scope="col">N°</th>
                                        <th scope="col">Période</th>
                                        <th scope="col">Prix</th>
                                        <th scope="col">Statut</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($all_bills as $bill) {
                                        if($bill["statut"] == "paid"){
                                            $bg_statut = "bg-success";
                                        }
                                        else{
                                            $bg_statut = "bg-danger";
                                        }
                                        ?>
                                        <tr>
                                            <th scope="row"><?=$bill["id_facture"]?></th>
                                            <td><?=$months_array[$bill["month"]-1]." ".$bill["year"]?></td>
                                            <td><?=$bill["prix"]?></td>
                                            <td><span class="bill-status badge <?=$bg_statut?>"><?=$bill["statut"]?></span></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card shadow clients-consommations mt-5">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 fw-bold">Consommations saisies par le client non encore validés</p>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th scope="col">N°</th>
                                        <th scope="col">Période</th>
                                        <th scope="col">Consommation saisie</th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($all_consommations as $consommation) { ?>
                                        <tr>
                                            <th scope="row"><?=$consommation["id_consommation"]?></th>
                                            <td><?=$months_array[$consommation["month"]]." ".$consommation["year"]?></td>
                                            <td><?=$consommation["qt_consommation"]?> KWH</td>
                                            <td><a href="#" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight<?=$consommation["id_consommation"]?>" aria-controls="offcanvasRight">
                                                    <img class="img-thumbnail img-compteur" width="50" src="../client/compteurs-img/<?=$consommation["id_consommation"]?>.png" />
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#cons<?=$consommation["id_consommation"]?>"> Intervenir </a>
                                            </td>
                                            <td>
                                                <form method="post"> <button class="btn btn-sm btn-outline-success" name="generate_bill" value="<?=$consommation["id_consommation"]?>"> Valider et Génerer facture</button></form>
                                            </td>
                                            </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <?php foreach ($all_consommations as $consommation){ ?>
                    <!-- Modal -->
                    <div class="modal fade" id="cons<?=$consommation["id_consommation"]?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form class="modal-content" method="post">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Consommation en KWH</span>
                                        <input type="text" class="form-control" name="qt_consommation"
                                               aria-describedby="basic-addon1" required value="<?=$consommation["qt_consommation"]?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary" name="save_change" value="<?=$consommation["id_consommation"]?>">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Modal -->
                    <!-- Offcanvas -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight<?=$consommation["id_consommation"]?>" aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasRightLabel">Image du compteur<?= $consommation["id_consommation"] ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <img src="../client/compteurs-img/<?=$consommation["id_consommation"]?>.png" width="300">
                        </div>
                    </div>
                    <!-- End Off canvas -->
                <?php } ?>
                <footer class="bg-white sticky-footer">

                </footer>
            </div>
            <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
        </div>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/bs-init.js"></script>
        <script src="assets/js/theme.js"></script>
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
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
                        <div class="row mb-3">
                            <div class="col-lg-8 client-infos">
                                <div class="row">
                                    <div class="col">
                                        <div class="card shadow mb-3">
                                            <div class="card-header py-3">
                                                <p class="text-primary m-0 fw-bold">Informations du client</p>
                                            </div>
                                            <div class="card-body">
                                                <form>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="Nom"><strong>Nom</strong></label>
                                                                <input class="form-control" type="text" id="Nom" placeholder="Nom" name="last_name" value="<?=$client_infos["last_name"]?>"></div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3"><label class="form-label" for="first_name"><strong>Prénom</strong></label>
                                                                <input class="form-control" type="email" id="first_name" placeholder="Prénom" name="first_name" value="<?=$client_infos["first_name"]?>"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="mb-3"><label class="form-label"for="Adresse"><strong>Adresse</strong></label>
                                                                <input class="form-control" type="text" id="Adresse" placeholder="Adresse" name="address" value="<?=$client_infos["address"]?>"></div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="mb-3"><label class="form-label"for="zone"><strong>Zone géographique</strong></label>
                                                                <input class="form-control" type="text" id="zone" placeholder="Zone Géographiqe" name="zone" value="<?=$client_infos["name_zone"]?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <button class="btn btn-primary btn-sm float-end" type="submit">
                                                            Enregistrer les changements
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 clients-stats">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="text-primary fw-bold m-0">Statistiques de Paiement de factures</h6>
                                    </div>
                                    <div class="card-body">
                                        <h4 class="small fw-bold">Factures Payées<span class="float-end">80%</span></h4>
                                        <div class="progress progress-sm mb-3">
                                            <div class="progress-bar bg-success" aria-valuenow="80" aria-valuemin="0"
                                                 aria-valuemax="100" style="width: 80%;"><span class="visually-hidden">20%</span>
                                            </div>
                                        </div>
                                        <h4 class="small fw-bold">Factures Non Payées<span class="float-end">20%</span>
                                        </h4>
                                        <div class="progress progress-sm mb-3">
                                            <div class="progress-bar bg-danger" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100" style="width: 20%;"><span class="visually-hidden">40%</span>
                                            </div>
                                        </div>
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
                                            <td>
                                                <button class="btn btn-sm btn-outline-info"> Intervenir</button>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-success"> Génerer facture</button>
                                            </td>
                                            </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
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
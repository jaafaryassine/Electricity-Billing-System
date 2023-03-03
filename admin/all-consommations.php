<?php
require("../backend/connect_db.php");
require ("../backend/admin/functions.php");
require("../backend/admin/Admin.php");
session_start();
if (isset($_SESSION["admin"])) {
    if ($_SESSION["admin"]) {
        $admin = new Admin();
        $all_consommations = $admin->get_all_consommations($db);
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
            <title>Table - Elec-Bill</title>
            <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet"
                  href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
            <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
            <link rel="stylesheet" href="assets/css/main.css">
        </head>

        <body id="page-top">
        <div id="wrapper">
            <?php require("./layouts/sidebar.html") ?>
            <div class="d-flex flex-column" id="content-wrapper">
                <div id="content">
                    <?php require("./layouts/navbar.html") ?>
                    <div class="container-fluid">
                        <div class="d-sm-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-dark mb-0">Consommations non encore validés</h3>
                        </div>
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 fw-bold">Informations des consommations</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 text-nowrap">
                                        <div id="dataTable_length" class="dataTables_length" aria-controls="dataTable">
                                            <label
                                                class="form-label">Show&nbsp;<select
                                                    class="d-inline-block form-select form-select-sm">
                                                    <option value="10" selected="">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>&nbsp;</label></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-md-end dataTables_filter" id="dataTable_filter"><label
                                                class="form-label"><input type="search"
                                                                          class="form-control form-control-sm"
                                                                          aria-controls="dataTable"
                                                                          placeholder="Search"></label></div>
                                    </div>
                                </div>
                                <div class="table-responsive table mt-2" id="dataTable" role="grid"
                                     aria-describedby="dataTable_info">
                                    <table class="table my-0" id="all-consommations">
                                        <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Période</th>
                                            <th>Consommations saisie</th>
                                            <th>Photo du compteur</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($all_consommations as $consommation){ ?>
                                            <tr>
                                                <td>
                                                    <?=$consommation["last_name"]." ".$consommation["first_name"]?>
                                                </td>
                                                <td> <?=$months_array[$consommation["month"]]." ".$consommation["year"]?></td>
                                                <td><?=$consommation["qt_consommation"]?> Kwh</td>
                                                <td><a href="#" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight<?=$consommation["id_consommation"]?>" aria-controls="offcanvasRight">
                                                        <img class="img-thumbnail img-compteur" width="50" src="../client/compteurs-img/<?=$consommation["id_consommation"]?>.png" />
                                                    </a>
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#cons<?=$consommation["id_consommation"]?>"> Intervenir</a>
                                                </td>
                                                <td>
                                                    <form method="post"> <button class="btn btn-sm btn-outline-success" name="generate_bill" value="<?=$consommation["id_consommation"]?>"> Génerer facture</button></form>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 align-self-center">
                                    </div>
                                    <div class="col-md-6">
                                        <nav class="d-lg-flex justify-content-lg-end dataTables_paginate paging_simple_numbers">
                                            <ul class="pagination">
                                                <li class="page-item disabled"><a class="page-link"
                                                                                  aria-label="Previous"
                                                                                  href="#"><span
                                                            aria-hidden="true">«</span></a>
                                                </li>
                                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item"><a class="page-link" aria-label="Next"
                                                                         href="#"><span
                                                            aria-hidden="true">»</span></a></li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        </body>

        </html>

        <?php
    } else {
        header("location: login.php");
    }
} else {
    header("location: login.php");
}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php
require("../backend/connect_db.php");
require("../backend/admin/Admin.php");
require("../backend/admin/functions.php");
session_start();
if (isset($_SESSION["admin"])) {
    if ($_SESSION["admin"]) {
        $admin = new Admin();

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
                    <div class="container">
                        <div class="d-sm-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-dark mb-0">Vérification</h3>
                        </div>
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 fw-bold">Informations</p>
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
                                    <table class="table my-0" id="dataTable">
                                        <thead>
                                        <tr>
                                            <th>Nom complet</th>
                                            <th>Consommation saisie par le client</th>
                                            <th>Consommation saisie par l'agent</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($all_clients as $client){ ?>
                                            <tr>
                                                <td>
                                                    <?=$client["last_name"]." ".$client["first_name"]?>
                                                </td>
                                                <td> <?=$client["address"]?></td>
                                                <td><?=$client["name_zone"]?></td>
                                                <td>
                                                    <a href="profile.php?id_client=<?=$client["id_client"]?>">
                                                        <button class="btn btn-outline-info">Infos clients</button>
                                                    </a>
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
        </body>

        </html>
        <?php
    }
    else {
        header("location: login.php");
    }
} else {
    header("location: login.php");
}
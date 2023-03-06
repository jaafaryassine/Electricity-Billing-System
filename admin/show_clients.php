<?php
require("../backend/connect_db.php");
require("../backend/admin/Admin.php");
session_start();
if (isset($_SESSION["admin"])) {
    if ($_SESSION["admin"]) {
        $admin = new Admin();
        $all_clients = $admin->get_all_clients($db);
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
        <style>
            .dataTables_length{
                display: none;
            }
            #clients-table_filter input{
                margin-left: 15px;
                border-radius: 18px;
                outline: 2px solid #0dcaf0;
            }

        </style>

        <body id="page-top">
        <div id="wrapper">
            <?php require("./layouts/sidebar.html") ?>
            <div class="d-flex flex-column" id="content-wrapper">
                <div id="content">
                    <?php require("./layouts/navbar.html") ?>
                    <div class="container-fluid">
                        <div class="d-sm-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-dark mb-0">Clients</h3><a
                                    class="btn btn-primary btn-sm d-none d-sm-inline-block"
                                    role="button" href="add-client.php"><i
                                        class="fas fa-download fa-sm text-white-50"></i>&nbsp;Ajouter Client</a>
                        </div>
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 fw-bold">Informations des clients</p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table mt-2" id="dataTable" role="grid"
                                     aria-describedby="dataTable_info">
                                    <table class="table my-0" id="clients-table">
                                        <thead>
                                        <tr>
                                            <th>Nom complet</th>
                                            <th>Adresse</th>
                                            <th>Zone Geographique</th>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function () {
                $('#clients-table').DataTable();
            });
        </script>
        </body>

        </html>
        <?php
    } else {
        header("location: login.php");
    }
} else {
    header("location: login.php");
}
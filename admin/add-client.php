<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php
require("../backend/connect_db.php");
require("../backend/admin/Admin.php");
require("../backend/admin/functions.php");
session_start();
if (isset($_SESSION["admin"])) {
    if ($_SESSION["admin"]) {
            $admin = new Admin();
            if (isset($_POST["add_client"])){
                $last_name = trim(htmlspecialchars($_POST["last_name"]));
                $first_name = trim(htmlspecialchars($_POST["first_name"]));
                $email = trim(htmlspecialchars($_POST["email"]));
                $password = trim(htmlspecialchars($_POST["password"]));
                $address = trim(htmlspecialchars($_POST["address"]));
                $id_zone = trim(htmlspecialchars($_POST["zone"]));
                if ($admin->add_client($db,$last_name,$first_name,$email,$password,$address,$id_zone)) {
                    echo "<span></span>"
                    ?>
                    <script>
                        swal("Félicitations ! ", "Client bien ajouté", "success");
                    </script>
                <?php }
                else {
                    echo "<span></span>"
                    ?>
                    <script type="text/javascript">
                        swal("Erreur", "Email déjà existant", "error");
                    </script>
                <?php }
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
                        <div class="container">
                            <div class="d-sm-flex justify-content-between align-items-center mb-4">
                                <h3 class="text-dark mb-0">Ajouter Client</h3>
                            </div>
                            <form class="row g-3" method="post">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputEmail4" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="inputEmail4" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputPassword4" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="inputPassword4" name="password" required>
                                </div>
                                <div class="col-12">
                                    <label for="inputAddress" class="form-label">Adresse</label>
                                    <input type="text" class="form-control" id="inputAddress" placeholder="Ex : Casablanca Maarif" name="address" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="inputState" class="form-label">Zone Géographique</label>
                                    <select id="inputState" class="form-select" name="zone">
                                        <?php foreach ($zones as $id => $zone) { ?>
                                            <option value="<?=$id?>"> <?=$zone?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary float-end" name="add_client">Ajouter</button>
                                </div>
                            </form>
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
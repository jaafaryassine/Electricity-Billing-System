<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php
require("../backend/connect_db.php");
require("../backend/client/functions.php");
require("../backend/client/Client.php");
session_start();

if ($_SESSION["email"]) {
    $client = new Client($_SESSION);
    $all_bills = $client->get_all_bills($db);
    if (isset($_POST["update"])){
        echo "<span></span>";
        if ($client->update_password($db,htmlspecialchars($_POST["old_password"]),htmlspecialchars($_POST["new_password"]))){
        ?>
        <script>
            swal("Félicitations ! ", "Mot de passe a été mis à jour", "success");
        </script>
    <?php }
        else{ ?>
            <script>
                swal("Erreur! ", "L'ancien mot de passe est invalid", "error");
            </script>
        <?php }

    } ?>
    <!DOCTYPE html>
    <html lang="en">
    <body>
    <?php require("./layouts/navbar.html") ?>
    <section id="main" class="py-4 py-xl-5">
        <div class="container">
            <div class="card shadow mb-3">
                <div class="card-header py-3">
                    <p class="text-primary m-0 fw-bold">Modifier mot de passe</p>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label" for="old-password"><strong>Ancien mot de passe</strong></label>
                                    <input class="form-control" type="password" id="old-password" placeholder="Ancien mot de passe" name="old_password""></div>
                            </div>
                            <div class="col">
                                <div class="mb-3"><label class="form-label" for="new_password"><strong>Nouveau mot de passe</strong></label>
                                    <input class="form-control" type="password" id="new-password" placeholder="Nouveau mot de passe" name="new_password"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary btn-sm float-end" type="submit" name="update">
                                Enregistrer les changements
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    </body>

    </html>
    <?php
}
else {
    header("location: login.php");
}
?>
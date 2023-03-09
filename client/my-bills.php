<?php
require("../backend/connect_db.php");
require("../backend/client/functions.php");
require("../backend/client/Client.php");
session_start();

if ($_SESSION["email"]) {
    $client = new Client($_SESSION);
    $all_bills = $client->get_all_bills($db);
    if (isset($_POST["print_bill"])){
        $id_bill = trim(htmlspecialchars($_POST["print_bill"]));
        $info_bill = $client->get_info_bill_by_id($db,$id_bill);
        $client->print_pdf_bill($db,$info_bill);
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
<body>
    <?php require("./layouts/navbar.html") ?>
<section id="main" class="py-4 py-xl-5">
    <div class="container">
    <table class="table table-striped">
    <thead>
    <tr>
        <th scope="col">N°</th>
        <th scope="col">Période</th>
        <th scope="col">Prix</th>
        <th scope="col">Statut</th>
        <th scope="col">Action</th>
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
            <td><?=$months_array[$bill["month"] - 1]." ".$bill["year"]?></td>
            <td><?=$bill["prix"]?> MAD</td>
            <td><span class="bill-status badge <?=$bg_statut?>"><?=$bill["statut"]?></span></td>
            <td>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#bill<?=$bill["id_facture"]?>">
                    Détails
                </button>
            </td>
        </tr>
        <?php } ?>

        </tbody>
        </table>
        </div>
        <!-- Button trigger modal -->


        <!-- Modal -->
    <?php foreach ($all_bills as $bill) { ?>
        <div class="modal fade" id="bill<?=$bill["id_facture"]?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Détails de facture</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Mois : <?=$months_array[$bill["month"]]?></p>
                        <p>Année : <?=$bill["year"]?></p>
                        <p>Montant : <?=$bill["prix"]?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <form method="post"> <button type="submit" class="btn btn-primary" name="print_bill" value="<?=$bill["id_facture"]?>">Imprimer facture</button></form>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>



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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php
require ("../backend/connect_db.php");
require("../backend/client/functions.php");
require ("../backend/client/Client.php");

session_start();

if($_SESSION["email"]){
    $client = new Client($_SESSION);
    if (isset($_POST["add"])){
        $qt_consommation = trim(htmlspecialchars($_POST["qt_consommation"]));
        $month = trim(htmlspecialchars($_POST["month"]));
        $year = trim(htmlspecialchars($_POST["year"]));
        $img_compteur = $_FILES["img-compteur"]["name"];
        $client->add_consommation($db,$qt_consommation,$month,$year);
            $last_id = $db->lastInsertId();
            $upload = "compteurs-img/".$last_id.".png";
            move_uploaded_file($_FILES["img-compteur"]["tmp_name"],$upload);
                echo "<span></span>" ?>
                <script>
                    swal("Félicitations ! ", "Consommation bien ajouté", "success");
                </script>
            <?php }
    ?>
<!DOCTYPE html>
<html lang="en">
<body>
    <?php require ("./layouts/navbar.html") ?>
    <section id="main" class="py-4 py-xl-5">
        <div class="container">
            <div class="form-consommation">
                <form method="post" enctype="multipart/form-data">
                    <label for="input-consommation">Consommation du mois : </label>
                    <input id="input-consommation" name="qt_consommation" type="number" placeholder="consommation" required>
                    <label for="select_month">Quel mois ?</label>
                    <select id="select_month" name="month" required>
                        <?php foreach ($months_array as $month) { ?>
                            <option value="<?=array_search($month,$months_array) +1?>"><?=$month?></option>
                        <?php  } ?>
                    </select>
                    <label for="select_year">Quel Année ?</label>
                    <select id="select_year" name="year" required>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                    </select>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="inputGroupFile01">Image du compteur</label>
                        <input type="file" name="img-compteur" class="form-control" id="inputGroupFile01">
                    </div>
                    <div class="mt-4" style="text-align: center;">
                        <button name="add" class="btn btn-outline-info">Enregistrer</button>
                    </div>
                </form>
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
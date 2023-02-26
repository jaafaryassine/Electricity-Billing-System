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
        if($client->add_consommation($db,$qt_consommation,$month,$year)){
            echo "added succesfully";
        }
        else{
            echo "Failed";
        }
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<body>
    <?php require ("./layouts/navbar.html") ?>
    <section id="main" class="py-4 py-xl-5">
        <div class="container">
            <div class="form-consommation">
                <form method="post">
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
<?php
require("../backend/connect_db.php");
require ("../backend/agent/functions.php");
require ("../backend/agent/Agent.php");
session_start();
if (isset($_SESSION["agent"])){
    $agent = new Agent($_SESSION["id_agent"]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Espace Agent</title>
</head>
<body>
    <?php require("./layouts/navbar.html"); ?>
    <div class="container">
        <div class="card-agent mt-5">
            <form method="post" enctype="multipart/form-data">
                <label for="input-file">Insérer le fichier de la consommation annuelle</label>
                <input type="file" id="input-file" name="file_cons" required>
                <button class="btn btn-primary float-end" name="add_file" type="submit">Enregistrer</button> <br/>
            </form>
        </div>
    </div>
    <?php if (isset($_POST["add_file"])){
        $f = $_FILES["file_cons"]["name"];
        $upload = "../backend/agent/txt-files/".$agent->id.".txt";
        move_uploaded_file($_FILES["file_cons"]["tmp_name"],$upload);
        $infos = $agent->add_txt_file($db,$upload);
    ?>
    <table class="table mt-5">
        <thead>
        <tr>
            <th scope="col">ID Client</th>
            <th scope="col">Année</th>
            <th scope="col">Quantité consommation</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?=$infos[0]?></td>
            <td><?=$infos[2]?></td>
            <td><?=$infos[1]?> KWH</td>
        </tr>

        </tbody>
    </table>
        <?php } ?>
</body>
</html>

    <?php
}
else {
    header("location:login.php");
}
?>
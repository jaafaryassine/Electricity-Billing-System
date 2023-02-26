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
            <form method="post">
                <label for="input-file">Insérer le fichier de la consommation annuelle</label>
                <input type="file" id="input-file">
                <button class="btn btn-primary float-end">Enregistrer</button> <br/>
            </form>
        </div>
    </div>
</body>
</html>

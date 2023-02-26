<?php
require ("../backend/connect_db.php");
require("../backend/client/functions.php");
require ("../backend/client/Client.php");
session_start();

if($_SESSION["email"]){
    $client = new Client($_SESSION);
    if (isset($_POST["send"])){
        $objet = trim(htmlspecialchars($_POST["objet"]));
        $msg = trim(htmlspecialchars($_POST["message"]));
        if($client->send_reclamation($db,$objet,$msg)){
            echo "sent succesfully";
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
  <section class="py-4 py-xl-5">
    <div class="container">
      <div class="row d-flex justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5 col-xxl-4">
          <div class="card mb-5">
            <div class="card-body p-sm-5">
              <h2 class="text-center mb-4">Réclamation</h2>
              <form method="post">
                <div class="mb-3">
                    <label for="objet" class="mb-2">Type de la réclammation :</label>
                    <select name="objet" id="objet" class="form-select" required>
                        <option value="Fuite Interne/Externe"> Fuite Interne/Externe </option>
                        <option value="Facture"> Facture </option>
                        <option value="Autre" > Autre </option>
                    </select>
                </div>
                <div class="mb-3"><textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea></div>
                <div><button class="btn btn-primary d-block w-100" type="submit" name="send">Envoyer</button></div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
</body>
</html>

    <?php
}
else {
    header("location: login.php");
}
?>
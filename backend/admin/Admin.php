<?php
require ("PDF.php");

class Admin
{
    public function get_all_clients($db){
        $req=$db->query("SELECT * FROM clients,zones_geo WHERE clients.id_zone=zones_geo.id_zone");
        $res=$req->fetchAll();
        return $res;
    }

    public function get_client_by_id($db,$id_client)
    {
        $req=$db->prepare("SELECT * FROM clients,zones_geo WHERE clients.id_zone=zones_geo.id_zone AND id_client=? LIMIT 1");
        $req->execute(array($id_client));
        $res=$req->fetch();
        return $res;
    }

    public function get_bills_by_client($db,$id_client)
    {
        $req=$db->prepare("SELECT * FROM factures WHERE id_client=?");
        $req->execute(array($id_client));
        $res=$req->fetchAll();
        return $res;
    }

    public function get_consommations_by_client($db,$id_client)
    {
        $req=$db->prepare("SELECT * FROM consommations WHERE id_client=? AND statut=?");
        $req->execute(array($id_client,"not valid"));
        $res=$req->fetchAll();
        return $res;
    }

    public function get_all_consommations($db)
    {
        $req=$db->prepare("SELECT * FROM consommations,clients WHERE clients.id_client=consommations.id_client AND statut=?");
        $req->execute(array("not valid"));
        $res=$req->fetchAll();
        return $res;
    }

    public function update_cons($db,$new_consommation,$id_consommation)
    {
        $update = $db->prepare("UPDATE consommations SET qt_consommation=? WHERE id_consommation=?");
        $update->execute(array($new_consommation,$id_consommation));
        if ($update){
            return true;
        }
        else {
            return false;
        }
    }

    public function get_info_consommation_by_id($db,$id_consommation)
    {
        $req=$db->prepare("SELECT * FROM consommations,clients WHERE consommations.id_client=clients.id_client AND id_consommation=? LIMIT 1");
        $req->execute(array($id_consommation));
        $res=$req->fetch();
        return $res;
    }

    public function generateBill($db,$infos_cons)
    {
        // Calculate Price
        if ($infos_cons["qt_consommation"] <= 100){
            $unit_price = 0.91;
        }
        elseif ($infos_cons["qt_consommation"] >= 101 && $infos_cons["qt_consommation"] <= 200){
            $unit_price = 1.01;
        }
        else{
            $unit_price = 1.12;
        }
        $tva = 0.14;
        $price = $infos_cons["qt_consommation"]* $unit_price * (1 + $tva);
        // Adding Bill to Database
        $insert = $db->prepare("INSERT INTO factures (prix,month,year,id_client,id_consommation)
                        VALUES (?,?,?,?,?)");
        $insert->execute(array($price,$infos_cons["month"],$infos_cons["year"],$infos_cons["id_client"],$infos_cons["id_consommation"]));
        if ($insert){
            $update = $db->prepare("UPDATE consommations SET statut='valid' WHERE id_consommation=?");
            $update->execute(array($infos_cons["id_consommation"]));
            $this->generate_pdf_bill($infos_cons,$price);
            return true;
        }
        else {
            return false;
        }
    }

    public function generate_pdf_bill($infos,$price)
    {
        $months_array = ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"];
        $pdf = new PDF();
        // Define alias for number of pages
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',14);

        $pdf->Cell(0, 10, 'Client : '.$infos["first_name"]." ".$infos["last_name"], 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Quantité Consommée : '.$infos["qt_consommation"])." KWH", 0, 1);
        $pdf->Cell(0, 10, 'Montant TTC : '.number_format($price,2,".","")." MAD", 0, 1);
        $pdf->Cell(0, 10, 'Mois : '.$months_array[$infos["month"] - 1], 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Année : '.$infos["year"]), 0, 1);


        $pdf->Output();
    }

    public function getStatistic($db){
        $statistics = [];
        $req=$db->query("SELECT SUM(prix) as money_not_paid FROM factures WHERE statut='not paid'");
        $res=$req->fetch();
        $statistics["money_not_paid"] = $res["money_not_paid"];

        $req=$db->query("SELECT AVG(qt_consommation) as avg_cons FROM consommations");
        $res=$req->fetch();
        $statistics["avg_cons"] = $res["avg_cons"];

        $req=$db->query("SELECT count(*) as nb_bills FROM factures");
        $res=$req->fetch();
        $nb_bills = $res["nb_bills"];
        $req=$db->query("SELECT count(*) as nb_bills_paid FROM factures WHERE statut='paid'");
        $res=$req->fetch();
        if ($nb_bills != 0)
        $statistics["bills_paid_percentage"] = $res["nb_bills_paid"]/$nb_bills * 100;
        else $statistics["bills_paid_percentage"] = 0;

        $req=$db->query("SELECT count(*) as nb_reclamations FROM reclamations");
        $res=$req->fetch();
        $statistics["nb_reclamations"] = $res["nb_reclamations"];
        return $statistics;
    }

    public function update_client_information($db,$id_client,$new_infos)
    {
        $update = $db->prepare("UPDATE clients SET first_name=?,last_name=?,address=?,id_zone=? WHERE id_client=?");
        $update->execute(array($new_infos["first_name"],$new_infos["last_name"],$new_infos["address"],$new_infos["id_zone"],$id_client));
        if ($update) {
            return true;
        }
        else {
            return false;
        }
    }

    public function get_all_reclamations($db,$statut)
    {
        $req = $db->prepare("SELECT * FROM reclamations, clients WHERE reclamations.id_client=clients.id_client AND statut = ?");
        $req->execute(array($statut));
        $res=$req->fetchAll();
        return $res;
    }

    public function answer_recmlamation($db,$id_reclamation,$message)
    {
        $insert = $db->prepare("INSERT INTO responses (id_reclamation,message) VALUES (?,?)");
        $insert->execute(array($id_reclamation,$message));
        if ($insert) return true;
        else return false;
    }

}

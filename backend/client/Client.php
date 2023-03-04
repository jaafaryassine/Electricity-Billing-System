<?php
require ("PDF.php");

class Client
{
    public $id;
    public $first_name;
    public $last_name;
    public $id_zone;
    public $email;
    public function __construct($arr_session)
    {
        $this->id = $arr_session["id_client"];
        $this->first_name = $arr_session["first_name"];
        $this->last_name= $arr_session["last_name"];
        $this->id_zone = $arr_session["id_zone"];
        $this->email = $arr_session["email"];
    }

    public function add_consommation($db,$qt_consommation,$month,$year)
    {
        if ($qt_consommation >= 50 && $qt_consommation<=400)
            $statut = "valid";

        else $statut = "not valid";

        $insert = $db->prepare("INSERT INTO consommations (qt_consommation,month,year,id_client,statut) VALUES (?,?,?,?,?)");
        $insert->execute(array($qt_consommation,$month,$year,$this->id,$statut));
        $id_consommation = $db->lastInsertId();

        // Calculate Price
        if ($qt_consommation <= 100){
            $unit_price = 0.91;
        }
        elseif ($qt_consommation >= 101 && $qt_consommation<= 200){
            $unit_price = 1.01;
        }
        else{
            $unit_price = 1.12;
        }
        $tva = 0.14;
        $price = $qt_consommation* $unit_price * (1 + $tva);

        // Adding to database
        if ($qt_consommation >= 50 && $qt_consommation<=400)
            $insert = $db->prepare("INSERT INTO factures (prix,month,year,id_client,id_consommation) VALUES (?,?,?,?,?)");
            $insert->execute(array($price,$month,$year,$this->id,$id_consommation));

        if($insert){
            return true;
        }
        else{
            return false;
        }
    }

    public function get_all_bills($db)
    {
        $req = $db->prepare("SELECT * FROM factures WHERE id_client=?");
        $req->execute(array($this->id));
        $res=$req->fetchAll();
        return $res;
    }

    public function get_info_bill_by_id($db,$id_bill)
    {
        $req = $db->prepare("SELECT * FROM factures,consommations WHERE factures.id_consommation=consommations.id_consommation AND factures.id_client=? AND id_facture=?");
        $req->execute(array($this->id,$id_bill));
        $res=$req->fetch();
        return $res;
    }

    public function send_reclamation($db,$objet,$msg)
    {
        $insert = $db->prepare("INSERT INTO reclamations (objet,message,id_client) VALUES (?,?,?)");
        $insert->execute(array($objet,$msg,$this->id));
        if ($insert){
            return true;
        }
        else{
            return false;
        }
    }

    public function print_pdf_bill($infos)
    {
        $months_array = ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"];
        $pdf = new PDF();
        // Define alias for number of pages
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',14);

        $pdf->Cell(0, 10, 'Client : '.$this->first_name." ".$this->last_name, 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Quantité Consommée : '.$infos["qt_consommation"])." KWH", 0, 1);
        $pdf->Cell(0, 10, 'Montant TTC : '.number_format($infos["prix"],2,".","")." MAD", 0, 1);
        $pdf->Cell(0, 10, 'Mois : '.$months_array[$infos["month"] - 1], 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Année : '.$infos["year"]), 0, 1);

        $pdf->Output();
    }

}
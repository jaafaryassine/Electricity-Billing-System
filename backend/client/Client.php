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

    public function get_difference_consommation($db,$year,$month,$qt)
    {
        $req=$db->prepare("SELECT qt_consommation FROM consommations WHERE year=? AND month=? AND id_client=? LIMIT 1");
        $req->execute(array($year,$month - 1,$this->id));
        $res=$req->fetch();
        if ($res)
            return $qt - $res["qt_consommation"];
        else return $qt;
    }
    public function add_consommation($db,$qt_consommation,$month,$year)
    {
        $difference_cons = $this->get_difference_consommation($db,$year,$month,$qt_consommation);
        if ($difference_cons>= 50 && $difference_cons<=400)
            $statut = "valid";

        else $statut = "not valid";

        $insert = $db->prepare("INSERT INTO consommations (qt_consommation,month,year,id_client,statut) VALUES (?,?,?,?,?)");
        $insert->execute(array($qt_consommation,$month,$year,$this->id,$statut));
        $id_consommation = $db->lastInsertId();

        // Calculate Price
        if ($difference_cons <= 100){
            $unit_price = 0.91;
        }
        elseif ($difference_cons > 100 && $difference_cons<= 200){
            $unit_price = 1.01;
        }
        else{
            $unit_price = 1.12;
        }
        $tva = 0.14;
        $price = $difference_cons* $unit_price * (1 + $tva);

        // Adding to database
        if ($difference_cons >= 50 && $difference_cons<=400) {
            $insert = $db->prepare("INSERT INTO factures (prix,month,year,id_client,id_consommation) VALUES (?,?,?,?,?)");
            $insert->execute(array($price, $month, $year, $this->id, $id_consommation));
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

    public function print_pdf_bill($db,$infos)
    {
        // Get difference consommation
        $difference_cons = $this->get_difference_consommation($db,$infos["year"],$infos["month"],$infos["qt_consommation"]);
        $months_array = ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"];
        $pdf = new Fpdf();

        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // Ajout de la couleur et des styles
        $pdf->SetFillColor(226, 188, 156);
        $pdf->SetFont('Helvetica', 'B', 16);

        // En-tête de la facture
        $pdf->Cell(0, 30, utf8_decode("FACTURE D'ELECTRICITÉ"), 0, 1, 'C', true);
        $pdf->Ln(20);

        // Informations sur le client
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(0, 10, utf8_decode("Informations du client"), 0, 1, 'L');
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(52, 73, 94);

        $pdf->Cell(0, 10, 'Client : '.$this->first_name." ".$this->last_name, 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Quantité Consommée : '.$difference_cons)." KWH", 0, 1);
        $pdf->Cell(0, 10, 'Montant TTC : '.number_format($infos["prix"],2,".","")." MAD", 0, 1);
        $pdf->Cell(0, 10, 'Mois : '.$months_array[$infos["month"] - 1], 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Année : '.$infos["year"]), 0, 1);

        $pdf->Output();
    }

    public function get_answered_reclamations($db)
    {
        $req = $db->prepare("SELECT *,reclamations.message as client_msg,responses.message as answer FROM reclamations, responses WHERE reclamations.id_reclamation=responses.id_reclamation AND statut = 'answered' AND id_client=?");
        $req->execute(array($this->id));
        $res=$req->fetchAll();
        return $res;
    }
    public function get_not_answered_reclamations($db)
    {
        $req = $db->prepare("SELECT * FROM reclamations WHERE statut = 'not answered' AND id_client=?");
        $req->execute(array($this->id));
        $res=$req->fetchAll();
        return $res;
    }

    public function update_password($db,$old_password,$new_password)
    {
        $req= $db->prepare("SELECT password FROM clients WHERE id_client=?");
        $req->execute(array($this->id));
        $res = $req->fetch();
        if (sha1($old_password)==$res["password"]){
            $update = $db->prepare("UPDATE clients SET password=? WHERE id_client=?");
            $update->execute(array(sha1($new_password),$this->id));
            if ($update) return true;
            else return false;
        }
        else return false;
    }

}
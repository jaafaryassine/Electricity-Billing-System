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

    public function get_all_consommations_valid($db)
    {
        $req=$db->prepare("SELECT * FROM consommations,clients WHERE clients.id_client=consommations.id_client AND statut=?");
        $req->execute(array("valid"));
        $res=$req->fetchAll();
        return $res;
    }

    public function get_all_bills($db)
    {
        $req=$db->prepare("SELECT * FROM factures,clients WHERE clients.id_client=factures.id_client");
        $req->execute();
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
        $difference_cons = $this->get_difference_consommation($db,$res["year"],$res["month"],$res["qt_consommation"],$res["id_client"]);
        $res["difference_cons"] = $difference_cons;
        return $res;
    }

    public function get_difference_consommation($db,$year,$month,$qt,$id_client)
    {
        $req=$db->prepare("SELECT qt_consommation FROM consommations WHERE year=? AND month=? AND id_client=? LIMIT 1");
        $req->execute(array($year,$month - 1,$id_client));
        $res=$req->fetch();
        if ($res)
            return $qt - $res["qt_consommation"];
        else return $qt;
    }

    public function get_info_bill_by_id($db,$id_facture)
    {
        $req=$db->prepare("SELECT * FROM factures,clients WHERE factures.id_client=clients.id_client AND id_facture=? LIMIT 1");
        $req->execute(array($id_facture));
        $res=$req->fetch();
        return $res;
    }

    public function generateBill($db,$infos_cons)
    {
        // Calculate Price
        if ($infos_cons["difference_cons"] <= 100){
            $unit_price = 0.91;
        }
        elseif ($infos_cons["difference_cons"] >= 101 && $infos_cons["difference_cons"] <= 200){
            $unit_price = 1.01;
        }
        else{
            $unit_price = 1.12;
        }
        $tva = 0.14;
        $price = $infos_cons["difference_cons"]* $unit_price * (1 + $tva);
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
        $pdf->Cell(0, 10, 'Client : '.$infos["first_name"]." ".$infos["last_name"], 0, 1);
        $pdf->Cell(0, 10, utf8_decode('Quantité Consommée : '.$infos["difference_cons"])." KWH", 0, 1);
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
        $tot_reclamations = $res["nb_reclamations"];
        $statistics["nb_reclamations"] = $res["nb_reclamations"];

        $req=$db->query("SELECT name_zone,AVG(qt_consommation) as qt FROM consommations,zones_geo,clients
        WHERE zones_geo.id_zone=clients.id_zone AND consommations.id_client=clients.id_client group by name_zone");
        $res = $req->fetchAll();
        $statistics["avg_by_zone"] = $res;

        $req=$db->query("SELECT count(*) as nb_answered FROM reclamations WHERE statut='answered'");
        $res=$req->fetch();
        if ($tot_reclamations!=0)
            $statistics["nb_reclamations_answered"] = $res["nb_answered"]/$tot_reclamations * 100;
        else $statistics["nb_reclamations_answered"] = 0;

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

    public function get_not_answered_reclamations($db)
    {
        $req = $db->prepare("SELECT * FROM reclamations, clients WHERE reclamations.id_client=clients.id_client AND statut = 'not answered'");
        $req->execute();
        $res=$req->fetchAll();
        return $res;
    }

    public function get_answered_reclamations($db)
    {
        $req = $db->prepare("SELECT *,reclamations.message as client_msg,responses.message as answer FROM reclamations, clients, responses WHERE reclamations.id_client=clients.id_client AND reclamations.id_reclamation=responses.id_reclamation AND statut = 'answered'");
        $req->execute();
        $res=$req->fetchAll();
        return $res;
    }
    public function answer_recmlamation($db,$id_reclamation,$message)
    {
        $insert = $db->prepare("INSERT INTO responses (id_reclamation,message) VALUES (?,?)");
        $insert->execute(array($id_reclamation,$message));
        $update = $db->prepare("UPDATE reclamations SET statut='answered' WHERE id_reclamation=?");
        $update->execute(array($id_reclamation));
        if ($insert) return true;
        else return false;
    }

    public function add_client($db,$last_name,$first_name,$email,$password,$address,$id_zone)
    {
        $req = $db->prepare("SELECT * FROM clients WHERE email=? LIMIT 1");
        $req->execute(array($email));
        $res=$req->fetch();
        if ($res){
            return false;
        }
        else {
            $insert = $db->prepare("INSERT INTO clients (last_name,first_name,email,password,address,id_zone) VALUES (?,?,?,?,?,?)");
            $insert->execute(array($last_name,$first_name,$email,sha1($password),$address,$id_zone));
            return true;
        }
    }

    public function get_annual_verification($db,$year)
    {
        $req = $db->prepare("SELECT id_yearly_cons,clients.id_client, first_name, last_name, SUM(consommations.qt_consommation) as qt_client,
        yearly_cons.qt_consommation as qt_agent FROM clients,consommations,yearly_cons 
        WHERE yearly_cons.statut='untreated' AND consommations.year=? AND yearly_cons.year=? AND clients.id_client=yearly_cons.id_client 
        AND clients.id_client=consommations.id_client group by clients.id_client");
        $req->execute(array($year,$year));
        $res=$req->fetchAll();
        return $res;
    }

    public function tolerate_verification($db,$id_yearly_cons)
    {
        $update = $db->prepare("UPDATE yearly_cons SET statut=? WHERE id_yearly_cons=?");
        $update->execute(array('tolerated',$id_yearly_cons));
        if ($update) return true;
        else return false;
    }

    public function consider_verification($db,$info_cons,$year)
    {
        $data_cons = explode(",",$info_cons);
        $update = $db->prepare("UPDATE yearly_cons SET statut=? WHERE id_yearly_cons=?");
        $update->execute(array('considered',$data_cons[0]));
        if ($update) {
            $insert = $db->prepare("INSERT INTO annual_difference (id_client,year,difference) VALUES (?,?,?)");
            $insert->execute(array($data_cons[1],$year,$data_cons[2]));
            return true;
        }
        else return false;
    }

    public function get_info_yearly_cons_by_id($db,$id_yearly_cons)
    {
        $req = $db->prepare("SELECT * FROM yearly_cons WHERE id_yearly_cons=?");
        $req->execute(array($id_yearly_cons));
        $res=$req->fetch();
        return $res;
    }



    public function search_by_name($db,$full_name)
    {
        $full_name_arr = explode(" ",$full_name);
        if (count($full_name_arr)>=2){
            $first_name = $full_name_arr[0];
            $last_name = $full_name_arr[1];
        }
        $req=$db->prepare("SELECT * FROM clients WHERE first_name=? AND last_name=?");
        $req->execute(array(explode(" ",$full_name)[0],explode(" ",$full_name)[1]));
    }



}

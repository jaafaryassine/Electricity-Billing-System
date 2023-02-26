<?php
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
        $insert = $db->prepare("INSERT INTO consommations (qt_consommation,month,year,id_client) VALUES (?,?,?,?)");
        $insert->execute(array($qt_consommation,$month,$year,$this->id));
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

}
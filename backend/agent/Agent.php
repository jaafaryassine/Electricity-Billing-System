<?php

class Agent
{
    public $id;
    public function __construct($id_agent)
    {
        $this->id=$id_agent;
    }

    public function add_txt_file($db,$path)
    {
        $f = fopen($path,"r");
        while (!feof($f)) {
            $infos = explode(",",fgets($f));
            $insert = $db->prepare("INSERT INTO yearly_cons (qt_consommation,year,id_agent,id_client) VALUES (?,?,?,?)");
            $insert->execute(array($infos[1],$infos[2],$this->id,$infos[0]));
        }
        $req=$db->prepare("SELECT * FROM yearly_cons WHERE year=?");
        $req->execute(array(2022));
        $res=$req->fetchAll();
        return $res;
    }

}
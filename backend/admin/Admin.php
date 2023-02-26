<?php

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
}
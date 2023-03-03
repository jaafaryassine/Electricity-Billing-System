<?php

class Consommation
{
    public $id_consommation;
    public $qt_consommation;
    public $month;
    public $year;
    public $id_client;

    public function __construct($infos)
    {
        $this->id_consommation = $infos["id_consommation"];
        $this->qt_consommation = $infos["qt_consommation"];
        $this->month = $infos["month"];
        $this->year = $infos["year"];
        $this->id_client = $infos["id_client"];
    }
}
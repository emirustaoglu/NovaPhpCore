<?php

namespace NovaPhp\Core;

class JsonData
{
    private $data;
    private $hataKodu;
    private $hataMesaji;
    private $statusCode = 200;

    public function __construct()
    {
        $this->data = null;
        $this->hataKodu = null;
        $this->hataMesaji = null;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setHataKodu($hataKodu)
    {
        $this->hataKodu = $hataKodu;
        return $this;
    }

    public function setHataMesaji($hataMesaji)
    {
        $this->hataMesaji = $hataMesaji;
        return $this;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getJson()
    {
        if ($this->hataKodu != "" && $this->hataMesaji == "") {
            $this->statusCode = 400;
            $this->hataMesaji = "Geçersiz istek. Zorunlu parametrelerden biri veya daha fazlası sağlanmadı.";
        }
        $jsonArray = array(
            "data" => $this->data,
            "hataKodu" => $this->hataKodu,
            "hataMesaji" => $this->hataMesaji
        );
        return json_encode($jsonArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
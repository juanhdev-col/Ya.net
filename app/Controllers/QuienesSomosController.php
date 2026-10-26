<?php

namespace App\Controllers;

use App\Models\CompanyInfo;

class QuienesSomosController
{
    public function index()
    {
        $sections = CompanyInfo::getSections();
        $media    = CompanyInfo::getMedia();
        $valores  = CompanyInfo::getValues();

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'quienes-somos' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista pública no encontrada.";
        }
    }
}
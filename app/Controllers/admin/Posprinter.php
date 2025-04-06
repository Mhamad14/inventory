<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class Posprinter extends BaseController
{

    public function index()
    {
        
        $connector = new NetworkPrintConnector("150.129.54.153", 9100);
        $printer = new Printer($connector);
        try {
            $printer->text("print invoice here");
            $printer->cut();
        } finally {
            $printer->close();
        }
        
    }
}

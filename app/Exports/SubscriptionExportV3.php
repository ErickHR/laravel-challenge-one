<?php

namespace App\Exports;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Options;

class SubscriptionExportV3
{

    public static function writeHeaders($writer)
    {
        $headers = [
            'ID',
            'Nombre Completo',
            'DNI',
            'Email',
            'Teléfono',
            'Compañía',
            'Tipo de deuda',
            'Situación',
            'Atraso',
            'Entidad',
            'Monto total',
            'Línea total',
            'Línea usada',
            'Reporte subido el',
            'Estado',
        ];

        $row = Row::fromValues($headers);
        $writer->addRow($row);
    }
}

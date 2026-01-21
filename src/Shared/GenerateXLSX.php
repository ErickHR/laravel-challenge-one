<?php

namespace Src\Shared;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class GenerateXLSX
{
    public const HEADERS = [
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

    public $writer;

    public function writeHeaders()
    {
        $row = Row::fromValues(self::HEADERS);
        $this->writer->addRow($row);
    }

    public function init()
    {
        $options = new Options();
        $this->writer = new Writer($options);
    }

    public function openToFile($filePath)
    {
        $this->writer->openToFile($filePath);
    }

    public function addRow($data)
    {
        $row = Row::fromValues($data);
        $this->writer->addRow($row);
    }

    public function close()
    {
        $this->writer->close();
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Services\SubscriptionReportServices;

class SubscriptionExportV2 implements FromGenerator, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function generator(): \Generator
    {
        $builder = new SubscriptionReportServices();

        foreach ($builder->generateReportDataLazy($this->filters, 500) as $row) {
            yield [
                $row['id'],
                $row['full_name'],
                $row['document'],
                $row['email'],
                $row['phone'],
                $row['company'],
                $row['debt_type'],
                $row['loan_status'],
                $row['expiration_days'],
                $row['entity'],
                $row['amount_with_currency'],
                $row['line_total'],
                $row['line_used'],
                $row['created_at'],
                $row['status'],
            ];
        }
    }

    public function headings(): array
    {
        return [
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
    }
}

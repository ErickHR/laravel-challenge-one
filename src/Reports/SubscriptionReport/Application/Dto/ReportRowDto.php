<?php

namespace Src\Reports\SubscriptionReport\Application\Dto;

class ReportRowDto
{
    public function __construct(
        public int $subscriptionReportId,
        public string $fullName,
        public string $document,
        public string $email,
        public string $phone,
        public string $company,
        public string $debtType,
        public string $loanStatus,
        public string $expirationDays,
        public string $entityType,
        public string $totalAmount,
        public string $totalLine,
        public string $usedLine,
        public string $reportDate,
        public string $status
    ) {}

    public function toArray(): array
    {
        return [
            $this->subscriptionReportId,
            $this->fullName,
            $this->document,
            $this->email,
            $this->phone,
            $this->company,
            $this->debtType,
            $this->loanStatus,
            $this->expirationDays,
            $this->entityType,
            $this->totalAmount,
            $this->totalLine,
            $this->usedLine,
            $this->reportDate,
            $this->status,
        ];
    }

    public static function headers(): array
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

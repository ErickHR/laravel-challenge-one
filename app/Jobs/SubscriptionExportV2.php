<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

use App\Enums\StatusProcessReport;

use App\Models\RegisterDownloand;
use App\Exports\SubscriptionExportV2 as ExportsSubscriptionExportV2;

class SubscriptionExportV2 implements ShouldQueue
{
    use Queueable;

    private $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $start = microtime(true);

        $filters = [
            'from' => $this->data['filters']['from'],
            'to' => $this->data['filters']['to']
        ];


        $register = RegisterDownloand::where('file_name', $this->data['nameFile'])->first();
        if ($register) {
            $register->update(['status' => StatusProcessReport::PROCESSING->value]);
        }

        $endSetup = microtime(true);
        Log::info('V2 - Tiempo de setup inicial: ' . round($endSetup - $start, 4) . ' segundos');

        $result = Excel::store(
            new ExportsSubscriptionExportV2($filters),
            $this->data['nameFile'],
            'public'
        );

        $endExcel = microtime(true);
        Log::info('V2 - Tiempo de generación Excel: ' . round($endExcel - $endSetup, 4) . ' segundos');

        if (!$result) {
            throw new \Exception('Error al guardar el archivo Excel');
        }

        if ($register) {
            $register->update(['status' => StatusProcessReport::COMPLETED->value]);
        }

        $endTotal = microtime(true);
        Log::info('V2 - Tiempo TOTAL del Job: ' . round($endTotal - $start, 4) . ' segundos');
    }

    public function failed(\Throwable $exception)
    {

        $fileName = $this->data['nameFile'];
        $register = RegisterDownloand::where('file_name', $fileName)->first();

        if ($register) {
            $register->update([
                'status' => StatusProcessReport::FAILED->value,
                'error_message' => $exception->getMessage()
            ]);
        }

        Log::error("=== V2: JOB FAILED DEFINITIVAMENTE: " . $exception->getMessage() . " ===");
    }
}

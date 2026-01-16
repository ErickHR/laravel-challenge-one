<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\StoreSubscriptionReportRequest;
use App\Http\Requests\UpdateSubscriptionReportRequest;

use App\Models\SubscriptionReport;

use App\Services\SubscriptionReportServices;

class SubscriptionReportController extends Controller
{
    protected $subscriptionReportServices;

    public function __construct(SubscriptionReportServices $subscriptionReportServices)
    {
        $this->subscriptionReportServices = $subscriptionReportServices;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
    }

    public function generateReportV1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'date',
            'to' => 'date|after_or_equal:from',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $filters = [
                'from' => Carbon::parse($request->input('from'))->startOfDay()->toDateTimeString(),
                'to' => Carbon::parse($request->input('to'))->endOfDay()->toDateTimeString(),
            ];

            $dataReport = $this->subscriptionReportServices->generateReportV1($filters);
            return $dataReport;
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el reporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function generateReportV2(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'from' => 'date',
            'to' => 'date|after_or_equal:from',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $filters = [
                'from' => Carbon::parse($request->input('from'))->startOfDay()->toDateTimeString(),
                'to' => Carbon::parse($request->input('to'))->endOfDay()->toDateTimeString(),
            ];

            $dataReport = $this->subscriptionReportServices->generateReportV2($filters);
            return response()->json($dataReport);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el reporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function generateReportV3(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'from' => 'date',
            'to' => 'date|after_or_equal:from',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $filters = [
                'from' => Carbon::parse($request->input('from'))->startOfDay()->toDateTimeString(),
                'to' => Carbon::parse($request->input('to'))->endOfDay()->toDateTimeString(),
            ];

            $dataReport = $this->subscriptionReportServices->generateReportV3($filters);
            return response()->json($dataReport);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el reporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadStoredExcel(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'filename' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $filename = $request->input('filename');

            return $this->subscriptionReportServices->downloadStoredExcel($filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al descargar el reporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionReport $subscriptionReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscriptionReport $subscriptionReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionReportRequest $request, SubscriptionReport $subscriptionReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionReport $subscriptionReport)
    {
        //
    }
}

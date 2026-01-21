<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreSubscriptionReportRequest;
use App\Http\Requests\UpdateSubscriptionReportRequest;

use App\Models\SubscriptionReport;

class SubscriptionReportController extends Controller
{

    public function __construct() {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
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
    public function store(StoreSubscriptionReportRequest $request) {}

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

    public function downloadStoredExcel(Request $request) {}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportLoan extends Model
{
    /** @use HasFactory<\Database\Factories\ReportLoanFactory> */
    use HasFactory;

    protected $fillable = [
        'subscription_report_id',
        'bank',
        'status',
        'currency',
        'amount',
        'expiration_days',
    ];

    public function subscriptionReport()
    {
        return $this->belongsTo(SubscriptionReport::class);
    }
}

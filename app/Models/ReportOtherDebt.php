<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportOtherDebt extends Model
{
    /** @use HasFactory<\Database\Factories\ReportOtherDebtFactory> */
    use HasFactory;

    protected $fillable = [
        'subscription_report_id',
        'entity',
        'currency',
        'amount',
        'expiration_days',
    ];

    public function subscriptionReport()
    {
        return $this->belongsTo(SubscriptionReport::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCreditCard extends Model
{
    /** @use HasFactory<\Database\Factories\ReportCreditCardFactory> */
    use HasFactory;

    protected $fillable = [
        'subscription_report_id',
        'bank',
        'currency',
        'line',
        'used',
    ];

    public function subscriptionReport()
    {
        return $this->belongsTo(SubscriptionReport::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionReport extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionReportFactory> */
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'period',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function reportLoans()
    {
        return $this->hasMany(ReportLoan::class);
    }

    public function reportOtherDebts()
    {
        return $this->hasMany(ReportOtherDebt::class);
    }

    public function reportCreditCards()
    {
        return $this->hasMany(ReportCreditCard::class);
    }
}

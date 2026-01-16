<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'full_name',
        'document',
        'email',
        'phone',
    ];

    public function subscriptionReports()
    {
        return $this->hasMany(SubscriptionReport::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterDownloand extends Model
{
    /** @use HasFactory<\Database\Factories\RegisterDownloandFactory> */
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'status',
    ];
}

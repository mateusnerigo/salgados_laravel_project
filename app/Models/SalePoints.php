<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePoints extends Model {
    use HasFactory;

    protected $hidden = [
        'idSalePoints',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'salePointName',
        'description',
        'isActive'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItems extends Model {
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'idSaleItems',
        'idSales',
        'idProducts',
        'quantity',
        'selledPrice',
        'discountApplied'
    ];
}

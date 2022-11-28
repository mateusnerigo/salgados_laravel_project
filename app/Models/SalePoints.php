<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePoints extends Model {
    use HasFactory;

    protected $primaryKey = 'idSalePoints';
    public $incrementing  = true;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'salePointName',
        'description',
        'isActive'
    ];
}

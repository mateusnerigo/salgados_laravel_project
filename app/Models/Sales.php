<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model {
    use HasFactory;

    protected $primaryKey = 'idSales';
    public $incrementing  = true;

    protected $hidden = [];

    protected $fillable = [
        'idClients',
        'idSalePoints',
        'deliverDateTime',
        'status',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model {
    use HasFactory;

    protected $primaryKey = 'idClients';
    public $incrementing  = true;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'clientName',
        'idSalePoints',
        'isActive'
    ];
}

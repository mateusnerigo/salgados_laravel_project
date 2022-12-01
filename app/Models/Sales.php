<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model;

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

    /**
     * Auxiliary function to return a sale by id
     * @param Builder $query
     * @param int $idSale
     */
    public function scopeGetById(Builder $query, $idSale) {
        return $query->where('idSale', $idSale);
    }
}

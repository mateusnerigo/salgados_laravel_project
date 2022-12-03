<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model;

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
        'soldPrice',
        'discountApplied'
    ];

    /**
     * Auxiliary function to return a sale by id
     * @param Builder $query
     * @param int $idSales
     */
    public function scopeWhereIdSales(Builder $query, $idSales) {
        return $query->where('idSales', $idSales);
    }

    /**
     * Auxiliary function to order returned items
     * @param Builder $query
     * @param int $idSales
     */
    public function scopeOrdered(Builder $query) {
        return $query->orderBy('idSaleItems');
    }
}

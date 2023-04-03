<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model,
    Illuminate\Support\Facades\DB;

class SaleItems extends Model {
    use HasFactory;

    protected $hidden = [];

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

    /**
     * Auxiliary builder to join with relational tables
     * @param Builder $query
     */
    public function scopeJoinWithRelations(Builder $query) {
        return $query
            ->join('products AS products', 'products.idProducts', '=', 'sale_items.idProducts');
    }

    /**
     * Auxiliary builder to select fields relationated for use in views
     * @param Builder $query
     */
    public function scopeSelectReturnWithRelationFields(Builder $query) {
        return $query->select(
            'sale_items.idSaleItems AS idSaleItems',
            'sale_items.idProducts AS idProducts',
            'products.productName AS productName',
            'sale_items.quantity AS quantity',
            'sale_items.soldPrice AS soldPrice',
            'sale_items.discountApplied AS discountApplied',
            'sale_items.created_at AS created_at',
            'sale_items.updated_at AS updated_at'
        );
    }
}

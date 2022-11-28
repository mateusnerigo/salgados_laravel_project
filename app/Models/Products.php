<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model;

class Products extends Model {
    use HasFactory;

    protected $primaryKey = 'idProducts';
    public $incrementing  = true;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'productName',
        'standardValue',
        'isActive'
    ];

    /**
     * Auxiliary function to return a product by id
     * @param Builder $query
     * @param int $idProducts
     */
    public function scopeGetById(Builder $query, $idProducts) {
        return $query
            ->where('idProducts', $idProducts)
            ->first();
    }

    /**
     * Auxiliary function to return a product other than sended
     * @param Builder $query
     * @param int $idProducts
     */
    public function scopeWhereDiffId(Builder $query, $idProducts) {
        return $query->where('idProducts', '!=', $idProducts);
    }

    /**
     * Auxiliary function to return a product by name
     * @param Builder $query
     * @param string $productName
     */
    public function scopeWhereName(Builder $query, string $productName) {
        return $query->where('productName', $productName);
    }

    /**
     * Auxiliary function to updates a product to a defined status
     * @param Builder $query
     * @param string $activeStatus (0/1)
     */
    public function scopeSetActiveStatus(Builder $query, $activeStatus) {
        return $query->update(
            ['isActive' => $activeStatus]
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model;

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

    /**
     * Auxiliary function to return a sale point by id
     * @param Builder $query
     * @param int $idSalePoints
     */
    public function scopeGetById(Builder $query, $idSalePoints) {
        return $query->where('idSalePoints', $idSalePoints);
    }

    /**
     * Auxiliary function to return a sale point other than sended
     * @param Builder $query
     * @param int $idSalePoints
     */
    public function scopeWhereDiffId(Builder $query, $idSalePoints) {
        return $query->where('idSalePoints', '!=', $idSalePoints);
    }

    /**
     * Auxiliary function to return a sale point by name
     * @param Builder $query
     * @param string $salePointName
     */
    public function scopeWhereName(Builder $query, string $salePointName) {
        return $query->where('salePointName', $salePointName);
    }

    /**
     * Auxiliary function to return if 'active true' status
     * @param Builder $query
     */
    public function scopeIsActive(Builder $query) {
        return $query->where('isActive', 1);
    }

    /**
     * Auxiliary function to updates a sale point to a defined status
     * @param Builder $query
     * @param string $activeStatus (0/1)
     */
    public function scopeSetActiveStatus(Builder $query, $activeStatus) {
        return $query->update(
            ['isActive' => $activeStatus]
        );
    }
}

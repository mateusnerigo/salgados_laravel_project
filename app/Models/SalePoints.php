<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model,
    Illuminate\Support\Facades\DB;

class SalePoints extends Model {
    use HasFactory;

    protected $primaryKey = 'idSalePoints';
    public $incrementing  = true;

    protected $hidden = [];

    protected $fillable = [
        'salePointName',
        'description',
        'isActive',
        'idUsersCreation',
        'idUsersLastUpdate',
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

    /**
     * Auxiliary builder to join with relational tables
     * @param Builder $query
     * @return Builder
     */
    public function scopeJoinWithRelations(Builder $query) {
        return $query
            ->join('users AS users_creation', 'users_creation.idUsers', '=', 'sale_points.idUsersCreation')
            ->join('users AS users_update', 'users_update.idUsers', '=', 'sale_points.idUsersLastUpdate');
    }

    /**
     * Auxiliary builder to select fields relationated for use in views
     * @param Builder $query
     * @return Builder
     */
    public function scopeSelectReturnWithRelationFields(Builder $query) {
        return $query->select(
            'sale_points.isActive',
            'sale_points.idSalePoints',
            'sale_points.salePointName',
            'sale_points.description',
            'sale_points.idUsersCreation',
            DB::raw("CONCAT(users_creation.firstName, ' ', users_creation.lastName) AS userCreationName"),
            'sale_points.idUsersLastUpdate',
            DB::raw("CONCAT(users_update.firstName, ' ', users_update.lastName) AS userUpdateName"),
            'sale_points.created_at AS createdAt',
            'sale_points.updated_at AS updatedAt'
        );
    }
}

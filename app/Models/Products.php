<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model,
    Illuminate\Support\Facades\DB;

class Products extends Model {
    use HasFactory;

    protected $primaryKey = 'idProducts';
    public $incrementing  = true;

    protected $hidden = [];

    protected $fillable = [
        'productName',
        'standardValue',
        'idUsersCreation',
        'idUsersLastUpdate',
        'isActive',
    ];

    /**
     * Auxiliary function to return a product by id
     * @param Builder $query
     * @param int $idProducts
     */
    public function scopeGetById(Builder $query, $idProducts) {
        return $query->where('idProducts', $idProducts);
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
     * Auxiliary function to return if 'active true' status
     * @param Builder $query
     */
    public function scopeIsActive(Builder $query) {
        return $query->where('isActive', 1);
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

    /**
     * Auxiliary builder to join with relational tables
     * @param Builder $query
     * @return Builder
     */
    public function scopeJoinWithRelations(Builder $query) {
        return $query
            ->join('users AS users_creation', 'users_creation.idUsers', '=', 'products.idUsersCreation')
            ->join('users AS users_update', 'users_update.idUsers', '=', 'products.idUsersLastUpdate');
    }

    /**
     * Auxiliary builder to select fields relationated for use in views
     * @param Builder $query
     * @return Builder
     */
    public function scopeSelectReturnWithRelationFields(Builder $query) {
        return $query->select(
            'products.isActive',
            'products.idProducts',
            'products.standardValue',
            'products.idUsersCreation',
            DB::raw("CONCAT(users_creation.firstName, ' ', users_creation.lastName) AS userCreationName"),
            'products.idUsersLastUpdate',
            DB::raw("CONCAT(users_update.firstName, ' ', users_update.lastName) AS userUpdateName"),
            'products.created_at AS createdAt',
            'products.updated_at AS updatedAt'
        );
    }
}

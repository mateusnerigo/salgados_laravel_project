<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model,
    Illuminate\Support\Facades\DB;

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
        'idUsersCreation',
        'idUsersLastUpdate',
    ];

    /**
     * Auxiliary function to return a sale by id
     * @param Builder $query
     * @param int $idSale
     */
    public function scopeGetById(Builder $query, $idSales) {
        return $query->where('idSales', $idSales);
    }

    /**
     * Auxiliary function to return if 'active true' status
     * @param Builder $query
     */
    public function scopeIsActive(Builder $query) {
        return $query->where('status', 'ic');
    }

    /**
     * Auxiliary builder to join with relational tables
     * @param Builder $query
     */
    public function scopeJoinWithRelations(Builder $query) {
        return $query
            ->join('clients AS clients', 'clients.idClients', '=', 'sales.idClients')
            ->join('sale_points AS sale_points', 'sale_points.idSalePoints', '=', 'sales.idSalePoints')
            ->join('users AS users_creation', 'users_creation.idUsers', '=', 'sales.idUsersCreation')
            ->join('users AS users_update', 'users_update.idUsers', '=', 'sales.idUsersLastUpdate');
    }

    /**
     * Auxiliary builder to select fields relationated for use in views
     * @param Builder $query
     */
    public function scopeSelectReturnWithRelationFields(Builder $query) {
        return $query->select(
            'sales.idSales',
            'sales.idClients',
            'clients.clientName',
            'sales.idSalePoints',
            'sale_points.salePointName',
            'sales.status',
            'sales.idUsersCreation',
            DB::raw("CONCAT(users_creation.firstName, ' ', users_creation.lastName) AS userCreationName"),
            'sales.idUsersLastUpdate',
            DB::raw("CONCAT(users_update.firstName, ' ', users_update.lastName) AS userUpdateName"),
            'sales.created_at AS createdAt',
            'sales.updated_at AS updatedAt'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model,
    Illuminate\Support\Facades\DB;

class Clients extends Model {
    use HasFactory;

    protected $primaryKey = 'idClients';
    public $incrementing  = true;

    protected $hidden = [];

    protected $fillable = [
        'clientName',
        'idSalePoints',
        'idUsersCreation',
        'idUsersLastUpdate',
        'isActive'
    ];

    /**
     * Auxiliary function to return a client by id
     * @param Builder $query
     * @param int $idClients
     */
    public function scopeGetById(Builder $query, $idClients) {
        return $query->where('idClients', $idClients);
    }

    /**
     * Auxiliary function to return a client other than sended
     * @param Builder $query
     * @param int $idClients
     */
    public function scopeWhereDiffId(Builder $query, $idClients) {
        return $query->where('idClients', '!=', $idClients);
    }

    /**
     * Auxiliary function to return a client by name
     * @param Builder $query
     * @param string $clientName
     */
    public function scopeWhereName(Builder $query, string $clientName) {
        return $query->where('clientName', $clientName);
    }

    /**
     * Auxiliary function to return a client by sale point id
     * @param Builder $query
     * @param int $idSalePoints
     */
    public function scopeWhereIdSalePoint(Builder $query, int $idSalePoints) {
        return $query->where('idSalePoints', $idSalePoints);
    }

    /**
     * Auxiliary function to return if 'active true' status
     * @param Builder $query
     */
    public function scopeIsActive(Builder $query) {
        return $query->where('isActive', 1);
    }

    /**
     * Auxiliary function to updates a client to a defined status
     * @param Builder $query
     * @param string $activeStatus (0|1)
     */
    public function scopeSetActiveStatus(Builder $query, $activeStatus) {
        return $query->update(
            ['isActive' => $activeStatus]
        );
    }

    /**
     * Auxiliary builder to join with relational tables
     * @param Builder $query
     */
    public function scopeJoinWithRelations(Builder $query) {
        return $query
            ->join('sale_points AS sale_points', 'sale_points.idSalePoints', '=', 'clients.idSalePoints')
            ->join('users AS users_creation', 'users_creation.idUsers', '=', 'clients.idUsersCreation')
            ->join('users AS users_update', 'users_update.idUsers', '=', 'clients.idUsersLastUpdate');
    }

    /**
     * Auxiliary builder to select fields relationated for use in views
     * @param Builder $query
     */
    public function scopeSelectReturnWithRelationFields(Builder $query) {
        return $query->select(
            'clients.isActive AS isActive',
            'clients.idClients AS idClients',
            'clients.clientName AS clientName',
            'clients.idSalePoints AS idSalePoints',
            'sale_points.salePointName AS salePointName',
            'clients.idUsersCreation AS idUsersCreation',
            DB::raw("CONCAT(users_creation.firstName, ' ', users_creation.lastName) AS userCreationName"),
            'clients.idUsersLastUpdate AS idUsersLastUpdate',
            DB::raw("CONCAT(users_update.firstName, ' ', users_update.lastName) AS userUpdateName"),
            'clients.created_at AS createdAt',
            'clients.updated_at AS updatedAt'
        );
    }
}

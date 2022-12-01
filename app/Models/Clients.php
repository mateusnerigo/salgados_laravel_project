<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory,
    Illuminate\Database\Eloquent\Builder,
    Illuminate\Database\Eloquent\Model;

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
}

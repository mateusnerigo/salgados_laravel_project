<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clients;

class ClientsController extends Controller {
    /**
     * Returns all clients created
     */
    public function index() {
        return jsonResponse(data: Clients::all());
    }

    /**
     * Verifies the data sended and inserts a new client in DB if it doesn't exists
     * @param Request $request
     */
    public function store(Request $request) {
        // properly receive the request information
        [
            'clientName'   => $clientName,
            'idSalePoints' => $idSalePoints
        ] = $request;
        unset($request);

        // array to verify if a client already exists
        $clientDataVerification = [
            ['clientName', '=', $clientName]
        ];

        // if idSalePoints is received, we add it to verification
        if (!empty($idSalePoints)) {
            $clientDataVerification[] = ['idSalePoints', '=', $idSalePoints];
        }

        // verifies if the data given matches with a client already created
        if (!empty(Clients::firstWhere($clientDataVerification))) {
            return jsonResponse(
                'Já existe um cliente cadastrado com esse nome para este ponto de venda.',
                'alert'
            );
        }
        unset($clientDataVerification);

        // inserts data for a new client
        $clientData = [
            'clientName' => $clientName
        ];

        // if idSalePoints is received, we add it to creation
        if (!empty($idSalePoints)) {
            $clientData['idSalePoints'] = $idSalePoints;
        }

        // if the data given is valid, creates a new client
        Clients::create($clientData);

        return jsonResponse(
            'Cliente cadastrado com sucesso!',
            'success'
        );
    }
}

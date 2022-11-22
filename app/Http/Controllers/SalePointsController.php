<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalePoints;
use \Illuminate\Http\JsonResponse;
use Throwable;

class SalePointsController extends Controller {
    /**
     * Returns all sale points created
     */
    public function index(): JsonResponse {
        return jsonResponse(data: SalePoints::all());
    }

    /**
     * Returns a sale point created by id
     */
    public function show(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        return jsonResponse(data: SalePoints::firstWhere([
            [ 'idSalePoints', '=', json_decode($request->data, true)['idSalePoints'] ]
        ]));
    }

    /**
     * Updates a sale point to inactive
     */
    public function toggleActive(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // sets the id received
        $idSalePoints = json_decode($request->data, true)['idSalePoints'];

        // get the actual sale point status by id
        $statusSalePoint = SalePoints::firstWhere([
            ['idSalePoints', '=', $idSalePoints]
        ]);

        // verifies the returned data
        if (empty($statusSalePoint)) {
            return jsonAlertResponse(
                'Há algo errado com a atualização deste ponto de venda.',
                "No sale points founded with the id sended ({$idSalePoints})"
            );
        }

        // default values
        $statusToChange = 0;
        $endMessagePart = 'desativado';

        // changes if the actual status is set to 0 (zero)
        if ($statusSalePoint['isActive'] == 0) {
            $statusToChange = 1;
            $endMessagePart = 'ativado';
        }

        try {
            // updates the sale point founded
            SalePoints::where('idSalePoints', $idSalePoints)
               ->update(['isActive' => $statusToChange]);
        } catch (Throwable $e) {
            // returns it if an error occurs
            return jsonAlertResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with successfull message
        return jsonSuccessResponse("Ponto de venda {$endMessagePart} com sucesso!");
    }

    /**
     * Verifies the data sended and inserts a new sale point in DB if it doesn't exists
     * @param Request $request
     */
    public function save(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // receives the data sended in a variable
        $requestData = json_decode($request->data, true);

        // verify necessary data
        if (empty($requestData['salePointName'])) {
            return jsonAlertResponse(
                "O nome do ponto de venda não foi enviado corretamente.",
                "Empty variable: \$requestData['salePointName']."
            );
        }

        // verifies if the data given matches with a sale point already created
        if (!empty($this->getSalePointByNameDiffId($requestData['salePointName'], $requestData['idSalePoints']))) {
            return jsonAlertResponse('Já existe um ponto de venda cadastrado com esse nome.');
        }

        try {
            // insertion array for insert or update
            $arrayCreateOrUpdate = [
                'salePointName' => $requestData['salePointName'],
                'description'   => $requestData['description']
            ];

            // creates a new sale point
            if (empty($requestData['idSalePoints'])) {
                $endMessagePart = 'cadastrado';

                SalePoints::create($arrayCreateOrUpdate);

            // updates a sale point already created
            } else {
                $endMessagePart = 'atualizado';

                SalePoints::where('idSalePoints', $requestData['idSalePoints'])
                    ->update($arrayCreateOrUpdate);
            }
        } catch (Throwable $e) {
            // returns a message if an error occurs
            return jsonAlertResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with a successfull message
        return jsonSuccessResponse("Ponto de venda {$endMessagePart} com sucesso!");
    }

    /**
     * Auxiliary functions to return a sale point with the given information
     * (used to find an sale point with a name used by another one)
     */
    private function getSalePointByNameDiffId(string $salePointName, $idSalePoints = '') {
        return SalePoints::firstWhere(
            [
                ['salePointName', '=', $salePointName],
                ['idSalePoints', '!=', $idSalePoints]
            ]
        );
    }
}

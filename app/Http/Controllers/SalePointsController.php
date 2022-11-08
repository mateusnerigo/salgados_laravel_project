<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalePoints;
use Throwable;

class SalePointsController extends Controller {
    /**
     * Returns all sale points created
     */
    public function index() {
        return jsonResponse(data: SalePoints::all());
    }

    /**
     * Verifies the data sended and inserts a new sale point in DB if it doesn't exists
     * @param Request $request
     */
    public function save(Request $request) {
        // properly receive the request information
        if (!validateDataSended($request)) {
            return dataSendedErrorResponse();
        }

        $requestData = json_decode($request->data, true);

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
            $arrayCreateOrUpdate = [
                'salePointName' => $requestData['salePointName'],
                'description'   => $requestData['description']
            ];

            // creates a new sale point
            if (empty($requestData['idSalePoints'])) {
                $endMessagePart = 'cadastrado';

                SalePoints::create($arrayCreateOrUpdate);
            } else {
                $endMessagePart = 'atualizado';

                SalePoints::where('idSalePoints', $requestData['idSalePoints'])
                    ->update($arrayCreateOrUpdate);
            }
        } catch (Throwable $e) {
            return jsonAlertResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        return jsonSuccessResponse("Ponto de venda {$endMessagePart} com sucesso!");
    }

    private function getSalePointByNameDiffId(string $salePointName, $idSalePoints = '') {
        return SalePoints::firstWhere(
            [
                ['salePointName', '=', $salePointName],
                ['idSalePoints', '!=', $idSalePoints]
            ]
        );
    }
}

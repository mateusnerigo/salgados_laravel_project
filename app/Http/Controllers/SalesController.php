<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse,
    Illuminate\Http\Request,
    App\Models\Sales,
    App\Models\SaleItems,
    App\Models\Clients,
    App\Models\SalePoints,
    App\Models\Products,
    Throwable,
    DateTime;

class SalesController extends Controller {
    /**
     * Returns all sales created
     * @return JsonResponse
     */
    public function index() {
        $sales = Sales::all();

        if (!empty($sales)) {
            foreach($sales AS $index => $sale) {
                $sales[$index]['items'] = $this->getSaleItemsBySaleId($sale->idSales);
            }
        }

        return jsonResponse(data: $sales);
    }

    /**
     * Returns a sale created by id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        $idSales = json_decode($request->data, true)['idSales'];

        // verifies sale id
        if (!empty($idSales) && empty($this->getSaleById($idSales))) {
            return jsonAlertResponse(
                'O código da venda enviada não pertence a nenhuma venda cadastrado.',
                "Sended variable value: {$idSales}"
            );
        }

        // prepares sale information
        $sale = $this->getSaleById($idSales);
        $sale['items'] = $this->getSaleItemsBySaleId($idSales);

        return jsonResponse(data: $sale);
    }

    /**
     * Updates a sale  status
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // receives the data sended in a variable
        $requestData = json_decode($request->data, true);
        $sale = $this->getSaleById($requestData['idSales']);

        // verifies client id
        if (!empty($requestData['idSales']) && empty($sale)) {
            return jsonAlertResponse(
                'O código da venda enviada não pertence a nenhuma venda cadastrada.',
                "Sended variable value: {$requestData['idSales']}"
            );
        }


        // verifies sale point id
        $statusValidationError = $this->validateStatus(
            $sale['status'],
            ($requestData['status'] ?? null)
        );

        if (!empty($statusValidationError)) {
            return $statusValidationError;
        }

        try {
            // updates the sle founded
            Sales::where('idSales', $requestData['idSales'])
                ->update(['status' => $requestData['status']]);
        } catch (Throwable $e) {
            // returns it if an error occurs
            return jsonAlertResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with successfull message
        return jsonSuccessResponse("Venda atualizada com sucesso!");
    }

    /**
     * Verifies the data sended and inserts a new sale in DB if it doesn't exists
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // receives the data sended in a variable
        $requestData = json_decode($request->data, true);

        // verifies client id
        $idClientsValidationError = $this->validateIdClients($requestData['idClients'] ?? null);
        if (!empty($idClientsValidationError)) {
            return $idClientsValidationError;
        }

        // verifies sale point id
        $idSalePointsValidationError = $this->validateIdSalePoints($requestData['idSalePoints'] ?? null);
        if (!empty($idSalePointsValidationError)) {
            return $idSalePointsValidationError;
        }

        // verifies deliver datetime
        $deliverDatetimeValidationError = $this->validateDeliverDatetime($requestData['deliverDatetime'] ?? null);
        if (!empty($deliverDatetimeValidationError)) {
            return $deliverDatetimeValidationError;
        }

        // verifies items sended
        $saleItemsValidationError = $this->validateSaleItems($requestData['items'] ?? null);
        if (!empty($saleItemsValidationError)) {
            return $saleItemsValidationError;
        }

        try {
            // creates a new sale
            Sales::create([
                'idSalePoints'    => $requestData['idSalePoints'],
                'idClients'       => $requestData['idClients'],
                'deliverDatetime' => $requestData['deliverDatetime'],
            ]);
        } catch (Throwable $e) {
            // returns a message if an error occurs
            return jsonAlertResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with a successfull message
        return jsonSuccessResponse("Venda cadastrada com sucesso!");
    }

    /**
     * Auxiliary function to return a sale by id
     * @param int $idSales
     */
    private function getSaleById($idSales = 0) {
        return Sales::firstWhere([['idSales', '=', $idSales]]);
    }

    /**
     * Auxiliary function to return the items from a sale by its id
     * @param int $idSales
     */
    private function getSaleItemsBySaleId($idSales) {
        return SaleItems::where('idSales', $idSales)
            ->orderBy('idSaleItems')
            ->get();
    }
}

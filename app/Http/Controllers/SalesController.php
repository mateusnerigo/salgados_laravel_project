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

    /**
     * Auxiliary function to validate clients sended id
     * @param $idClients
     */
    private function validateIdClients($idClients) {
        // if it is not set
        if (!isset($idClients)) {
            return jsonAlertResponse(
                "A identificação do cliente não foi enviada corretamente.",
                "Empty variable: \$requestData['idClients']."
            );
        }

        // if it is not numeric
        if (!is_numeric($idClients)) {
            return jsonAlertResponse(
                "A identificação do cliente foi enviada de forma equívoca.",
                "Sended variable value: {$idClients}"
            );
        }

        // if the id was sended
        if ($idClients > 0) {
            // if the client isn't registered
            if (empty(Clients::firstWhere([['idClients', '=', $idClients]]))) {
                return jsonAlertResponse(
                    "O cliente enviado não está cadastrado corretamente.",
                    "Sended variable value: {$idClients}"
                );
            }

            // if the client isn't active
            if (empty(Clients::firstWhere([
                ['idClients', '=', $idClients],
                ['isActive', '=', 0]
            ]))) {
                return jsonAlertResponse(
                    "O cliente escolhido não está ativo.",
                    "Sended variable value: {$idClients}"
                );
            }
        }

        return '';
    }

    /**
     * Auxiliary function to validate status sended
     * @param $actualStatus
     * @param $newStatus
     */
    private function validateStatus($actualStatus, $newStatus) {
        // if it is not set
        if (!isset($newStatus)) {
            return jsonAlertResponse(
                "A situação da venda não foi enviada corretamente",
                "Empty variable: \$requestData['status']."
            );
        }

        // if it is not in the array of possible status
        if (!in_array($newStatus, SALES_STATUS)) {
            return jsonAlertResponse(
                "A situação enviada para que a venda seja atualizada não é uma situação possível.",
                "Sended variable value: {$newStatus}"
            );
        }

        // if it is a canceled or finished sale
        if ($actualStatus == 'cl' || $actualStatus == 'fs') {
            return jsonAlertResponse(
                "A venda não pode ser atualizada.",
                "Sale already finished or canceled."
            );
        }

        return '';
    }

    /**
     * Auxiliary function to validate deliver date and time sended
     * @param $deliverDateTime
     */
    private function validateDeliverDatetime($deliverDateTime) {
        // if it is not set
        if (!isset($deliverDateTime)) {
            return jsonAlertResponse(
                "A data e hora de entrega não foram enviadas corretamente.",
                "Empty variable: \$requestData['deliverDateTime']."
            );
        }

        // tries the creation with given information
        $deliverDateTimeValidation = DateTime::createFromFormat('Y-m-d H:i:s', $deliverDateTime);

        // if we find any errors during the creation, returns with a proper message
        if (!empty(DateTime::getLastErrors()['warning_count'])) {
            return jsonAlertResponse(
                "A data e a hora de entrega foram enviadas de forma equívoca.",
                "Sended variable value: {$deliverDateTime}."
            );
        }
        unset($deliverDateTimeValidation);

        return '';
    }

    /**
     * Auxiliary function to validate items sended
     * @param $itemsToValidate
     */
    private function validateSaleItems($itemsToValidate) {
        // if it is not set
        if (!isset($itemsToValidate)) {
            return jsonAlertResponse(
                "Os itens da venda não foram enviados.",
                "Empty variable: \$requestData['items']."
            );
        }

        foreach($itemsToValidate AS $item) {
            $itemValidationError = $this->validateSingleSaleItem($item);

            if (!empty($itemValidationError)) {
                return $itemValidationError;
            }
        }

        return '';
    }

    /**
     * Auxiliary function to validate a single item from a sale
     * @param $itemToValidate
     */
    private function validateSingleSaleItem($itemToValidate) {
        // validar se o id do produto veio


        if (empty(Products::getById($itemToValidate['idProducts']))) {

        }
        return '';
    }
}

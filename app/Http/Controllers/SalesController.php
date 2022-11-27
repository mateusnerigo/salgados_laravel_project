<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse,
    Illuminate\Http\Request,
    App\Models\Sales,
    App\Models\Clients,
    App\Models\SalePoints,
    Throwable,
    DateTime;

class SalesController extends Controller {
    /**
     * Returns all sales created
     * @return JsonResponse
     */
    public function index() {
        return jsonResponse(data: Sales::all());
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

        return jsonResponse(data: Sales::firstWhere([
            ['idSales', '=', $idSales]
        ]));
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

        // verifies client id
        if (!empty($requestData['idSales']) && empty($this->getSaleById($requestData['idSales']))) {
            return jsonAlertResponse(
                'O código da venda enviada não pertence a nenhuma venda cadastrada.',
                "Sended variable value: {$requestData['idSales']}"
            );
        }

        // verifies sale point id
        $statusValidationError = $this->validateStatus($requestData['status'] ?? null);
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
     * Auxiliary functions to return a sale by id
     * @param int $idSales
     */
    private function getSaleById($idSales = 0) {
        return Sales::firstWhere([['idSales', '=', $idSales]]);
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
     * Auxiliary function to validate sale points sended id
     * @param $idSalePoints
     */
    private function validateIdSalePoints($idSalePoints) {
        // if it is not set
        if (!isset($idSalePoints)) {
            return jsonAlertResponse(
                "O ponto de venda deste cliente não foi enviado corretamente.",
                "Empty variable: \$requestData['idSalePoints']."
            );
        }

        // if it is not numeric
        if (!is_numeric($idSalePoints)) {
            return jsonAlertResponse(
                "O ponto de venda deste cliente foi enviado de forma equívoca.",
                "Sended variable value: {$idSalePoints}"
            );
        }

        // if the id was sended
        if ($idSalePoints > 0) {
            // if the sale point isn't registered
            if (empty(SalePoints::firstWhere([['idSalePoints', '=', $idSalePoints]]))) {
                return jsonAlertResponse(
                    "O ponto de venda enviado não está cadastrado corretamente.",
                    "Sended variable value: {$idSalePoints}"
                );
            }

            // if the sale point isn't active
            if (empty(SalePoints::firstWhere([
                ['idSalePoints', '=', $idSalePoints],
                ['isActive', '=', 0]
            ]))) {
                return jsonAlertResponse(
                    "O ponto de venda escolhido não está ativo.",
                    "Sended variable value: {$idSalePoints}"
                );
            }
        }

        return '';
    }

    /**
     * Auxiliary function to validate status sended
     * @param $status
     */
    private function validateStatus($status) {
        // if it is not set
        if (!isset($status)) {
            return jsonAlertResponse(
                "A situação da venda não foi enviada corretamente",
                "Empty variable: \$requestData['status']."
            );
        }

        // if it is not in the array of possible status
        if (!in_array($status, SALES_STATUS)) {
            return jsonAlertResponse(
                "A situação enviada para que a venda seja atualizada é uma situação possível.",
                "Sended variable value: {$status}"
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
}

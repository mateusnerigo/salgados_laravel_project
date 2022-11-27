<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse,
    Illuminate\Http\Request,
    App\Models\Clients,
    App\Models\SalePoints,
    Throwable;

class ClientsController extends Controller {
    /**
     * Returns all clients created
     * @return JsonResponse
     */
    public function index() {
        return jsonResponse(data: Clients::all());
    }

    /**
     * Returns a client created by id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        $idClients = json_decode($request->data, true)['idClients'] ;

        // verifies client id
        if (!empty($idClients) && empty($this->getClientById($idClients))) {
            return jsonAlertResponse(
                'O código do cliente enviado não pertence a nenhum cliente cadastrado.',
                "Sended variable value: {$idClients}"
            );
        }

        return jsonResponse(data: Clients::firstWhere([
            ['idClients', '=', $idClients]
        ]));
    }

    /**
     * Toggles a client  status
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleActive(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // sets the id received
        $idClients = json_decode($request->data, true)['idClients'];

        // verifies client id
        if (!empty($idClients) && empty($this->getClientById($idClients))) {
            return jsonAlertResponse(
                'O código do cliente enviado não pertence a nenhum cliente cadastrado.',
                "Sended variable value: {$idClients}"
            );
        }

        // get the actual client status by id
        $statusClient = Clients::firstWhere([
            ['idClients', '=', $idClients]
        ]);

        // verifies the returned data
        if (empty($statusClient)) {
            return jsonAlertResponse(
                'Há algo errado com a atualização deste cliente.',
                "No client founded with the id sended ({$idClients})"
            );
        }

        // default values
        $statusToChange = 0;
        $endMessagePart = 'desativado';

        // changes if the actual status is set to 0 (zero)
        if ($statusClient['isActive'] == 0) {
            $statusToChange = 1;
            $endMessagePart = 'ativado';
        }

        try {
            // updates the client founded
            Clients::where('idClients', $idClients)
                ->update(['isActive' => $statusToChange]);
        } catch (Throwable $e) {
            // returns it if an error occurs
            return jsonAlertResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with successfull message
        return jsonSuccessResponse("Cliente {$endMessagePart} com sucesso!");
    }

    /**
     * Verifies the data sended and inserts a new client in DB if it doesn't exists
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
        if (!empty($requestData['idClients']) && empty($this->getClientById($requestData['idClients']))) {
            return jsonAlertResponse(
                'O código do cliente enviado não pertence a nenhum cliente cadastrado.',
                "Sended variable value: {$requestData['idClients']}"
            );
        }

        // verifies client name
        $clientNameValidationError = $this->validateClientName($requestData['clientName'] ?? null);
        if (!empty($clientNameValidationError)) {
            return $clientNameValidationError;
        }

        // verifies sale point id
        $idSalePointsValidationError = $this->validateIdSalePoints($requestData['idSalePoints'] ?? null);
        if (!empty($idSalePointsValidationError)) {
            return $idSalePointsValidationError;
        }

        // verifies if the data given matches with a client already created
        $clientAlreadyCreated = $this->getClientByNameDiffIdAndSalePoints(
            $requestData['clientName'],
            $requestData['idClients'],
            $requestData['idSalePoints']
        );

        if (!empty($clientAlreadyCreated)) {
            return jsonAlertResponse('Já existe um cliente cadastrado com esse nome para esse ponto de venda.');
        }

        try {
            // insertion array for insert or update
            $arrayCreateOrUpdate = [
                'clientName' => $requestData['clientName']
            ];

            if (!empty($requestData['idSalePoints'])) {
                $arrayCreateOrUpdate['idSalePoints'] = $requestData['idSalePoints'];
            }

            // creates a new client
            if (empty($requestData['idClients'])) {
                $endMessagePart = 'cadastrado';

                Clients::create($arrayCreateOrUpdate);

            // updates a client already created
            } else {
                $endMessagePart = 'atualizado';

                Clients::where('idClients', $requestData['idClients'])
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
        return jsonSuccessResponse("Cliente {$endMessagePart} com sucesso!");
    }

    /**
     * Auxiliary functions to return a client by id
     * @param int $idClients
     */
    private function getClientById($idClients = 0) {
        return Clients::firstWhere([ ['idClients', '=', $idClients] ]);
    }

    /**
     * Auxiliary functions to return a client with the given information
     * (used to find a client with a name used by another one with the same sale point)
     * @param string $clientName
     * @param int    $idClients
     * @param int    $idSalePoints
     */
    private function getClientByNameDiffIdAndSalePoints(string $clientName, $idClients = 0, $idSalePoints = 0) {
         return Clients::firstWhere(
            [
                ['clientName', '=', $clientName],
                ['idClients', '!=', $idClients],
                ['idSalePoints', '=', $idSalePoints],
            ]
        );
    }

    /**
     * Auxiliary function to validate a new name for a client
     * @param $clientName
     */
    private function validateClientName($clientName) {
        // if it is not set
        if (!isset($clientName)) {
            return jsonAlertResponse(
                "O nome do cliente não foi enviado corretamente.",
                "Empty variable: \$requestData['clientName']."
            );
        }

        // if it is empty
        if (empty($clientName)) {
            return jsonAlertResponse("O nome do cliente deve ser preenchido.");
        }

        // if it is shorter than 3 characters
        if (strlen($clientName) < 3) {
            return jsonAlertResponse("O nome do cliente deve ter pelo menos 3 letras.");
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
}

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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse {
        if (!empty($request->idClients)) {
            return $this->show($request->idClients);
        }

        return jsonResponse(data: Clients::all());
    }

    /**
     * Returns a client created by id
     * @param mixed $idClients
     * @return JsonResponse
     */
    private function show($idClients): JsonResponse {
        // verifies client id
        $idClientsValidationError = $this->validateId(
            new Clients,
            $idClients,
            'cliente',
            '$idClients',
            false
        );

        if (!empty($idClientsValidationError)) {
            return $idClientsValidationError;
        }

        return jsonResponse(data: Clients::getById($idClients)->first());
    }

    /**
     * Toggles a client  status
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleActive(Request $request): JsonResponse {
        // sets the id received
        $idClients = $request->idClients ?? null;

        // verifies client id
        $idClientsValidationError = $this->validateId(
            new Clients,
            $idClients,
            'cliente',
            '$idClients',
            false
        );

        if (!empty($idClientsValidationError)) {
            return $idClientsValidationError;
        }

        // get the actual client status by id
        $clientToToggle = Clients::getById($idClients);

        // default values
        $statusToChange = 0;
        $endMessagePart = 'desativado';

        // changes if the actual status is set to 0 (zero)
        if ($clientToToggle->first()['isActive'] == 0) {
            $statusToChange = 1;
            $endMessagePart = 'ativado';
        }

        try {
            // updates the client founded
            $clientToToggle->setActiveStatus($statusToChange);
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
        $idClientsValidationError = $this->validateId(
            new Clients,
            ($requestData['idClients'] ?? null),
            'cliente',
            "\$requestData['idClients']"
        );

        if (!empty($idClientsValidationError)) {
            return $idClientsValidationError;
        }

        // verifies client name
        $clientNameValidationError = $this->validateText(
            ($requestData['clientName'] ?? ''),
            'Nome do cliente',
            'clientName'
        );

        if (!empty($clientNameValidationError)) {
            return $clientNameValidationError;
        }

        // verifies sale point id
        $idSalePointsValidationError = $this->validateId(
            new SalePoints,
            ($requestData['idSalePoints'] ?? null),
            'ponto de venda',
            "\$requestData['idSalePoints']"
        );

        if (!empty($idSalePointsValidationError)) {
            return $idSalePointsValidationError;
        }

        // verifies if the data given matches with a client already created
        $clientAlreadyCreated = Clients::whereName($requestData['clientName'])
            ->whereDiffId($requestData['idClients'])
            ->whereIdSalePoint($requestData['idSalePoints'])
            ->first();

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

                Clients::getById($requestData['idClients'])
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
}

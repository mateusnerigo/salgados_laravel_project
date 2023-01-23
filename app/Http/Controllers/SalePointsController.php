<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse,
    Illuminate\Http\Request,
    App\Models\SalePoints,
    Throwable;

class SalePointsController extends Controller {
    /**
     * Returns sale points created
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse {
        if (!empty($request->idSalePoints)) {
            return $this->show($request->idSalePoints);
        }

        return rawJsonResponse(
            $this->getAllPaginated(
                new SalePoints,
                $request,
                [
                    'sale_points.idSalePoints',
                    'sale_points.salePointName',
                    'sale_points.description'
                ]
            )
        );
    }

    /**
     * Returns a sale point created by id
     * @param mixed $idSalePoints
     * @return JsonResponse
     */
    private function show($idSalePoints): JsonResponse {
        // verifies sale point id
        $idSalePointsValidationError = $this->validateId(
            new SalePoints,
            $idSalePoints,
            'ponto de venda',
            '$idSalePoints',
            false
        );

        if (!empty($idSalePointsValidationError)) {
            return $idSalePointsValidationError;
        }

        return jsonResponse(data: SalePoints::getById($idSalePoints)->first());
    }

    /**
     * Toggles a sale point status
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleActive(Request $request): JsonResponse {
        // sets the id received
        $idSalePoints = $request->idSalePoints ?? null;

        // verifies sale point id
        $idSalePointsValidationError = $this->validateId(
            new SalePoints,
            $idSalePoints,
            'ponto de venda',
            '$idSalePoints',
            false
        );

        if (!empty($idSalePointsValidationError)) {
            return $idSalePointsValidationError;
        }

        // get the actual sale point status by id
        $salePointToToggle = SalePoints::getById($idSalePoints);

        // default values
        $statusToChange = 0;
        $endMessagePart = 'desativado';

        // changes if the actual status is set to 0 (zero)
        if ($salePointToToggle->first()['isActive'] == 0) {
            $statusToChange = 1;
            $endMessagePart = 'ativado';
        }

        try {
            // updates the sale point founded
            $salePointToToggle->setActiveStatus($statusToChange);
        } catch (Throwable $e) {
            // returns it if an error occurs
            return jsonErrorResponse(
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
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // receives the data sended in a variable
        $requestData = json_decode($request->data, true);

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

        // verifies sale point name
        $salePointNameValidation = $this->validateText(
            $requestData['salePointName'] ?? null,
            'Nome do ponto de venda',
            'salePointName'
        );
        if (!empty($salePointNameValidation)) {
            return $salePointNameValidation;
        }

        $salePointAlreadyCreated = SalePoints::whereName($requestData['salePointName'])
            ->whereDiffId($requestData['idSalePoints'])
            ->first();

        // verifies if the data given matches with a sale point already created
        if (!empty($salePointAlreadyCreated)) {
            return jsonErrorResponse('Já existe um ponto de venda cadastrado com esse nome.');
        }

        try {
            $userId = auth()->user()->idUsers;

            // insertion array for insert or update
            $arrayCreateOrUpdate = [
                'salePointName' => $requestData['salePointName'],
                'description'   => $requestData['description'],
                'idUsersLastUpdate' => $userId
            ];

            // creates a new sale point
            if (empty($requestData['idSalePoints'])) {
                $arrayCreateOrUpdate['idUsersCreation'] = $userId;
                $endMessagePart = 'cadastrado';

                SalePoints::create($arrayCreateOrUpdate);

            // updates a sale point already created
            } else {
                $endMessagePart = 'atualizado';

                SalePoints::getById($requestData['idSalePoints'])
                    ->update($arrayCreateOrUpdate);
            }
        } catch (Throwable $e) {
            // returns a message if an error occurs
            return jsonErrorResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with a successfull message
        return jsonSuccessResponse("Ponto de venda {$endMessagePart} com sucesso!");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse,
    Illuminate\Http\Request,
    App\Models\Products,
    Throwable;

class ProductsController extends Controller {
    /**
     * Returns all products created
     * @return JsonResponse
     */
    public function index() {
        return jsonResponse(data: Products::all());
    }

    /**
     * Returns a product created by id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        $idProducts = json_decode($request->data, true)['idProducts'];

        // verifies product id
        if (!empty($idProducts) && empty(Products::getById($idProducts)->first())) {
            return jsonAlertResponse(
                'O código do produto enviado não pertence a nenhum produto cadastrado.',
                "Sended variable value: {$idProducts}"
            );
        }

        return jsonResponse(data: Products::getById($idProducts)->first());
    }

    /**
     * Toggles a product  status
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleActive(Request $request): JsonResponse {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // sets the id received
        $idProducts = json_decode($request->data, true)['idProducts'];

        // verifies product id
        if (!empty($idProducts) && empty(Products::getById($idProducts)->first())) {
            return jsonAlertResponse(
                'O código do produto enviado não pertence a nenhum produto cadastrado.',
                "Sended variable value: {$idProducts}"
            );
        }

        // get the actual product status by id
        $statusProduct = Products::getById($idProducts)->first();

        // verifies the returned data
        if (empty($statusProduct)) {
            return jsonAlertResponse(
                'Há algo errado com a atualização deste produto.',
                "No product founded with the id sended ({$idProducts})"
            );
        }

        // default values
        $statusToChange = 0;
        $endMessagePart = 'desativado';

        // changes if the actual status is set to 0 (zero)
        if ($statusProduct['isActive'] == 0) {
            $statusToChange = 1;
            $endMessagePart = 'ativado';
        }

        try {
            // updates the product founded
            Products::getById($idProducts)
                ->setActiveStatus($statusToChange);
        } catch (Throwable $e) {
            // returns it if an error occurs
            return jsonAlertResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with successfull message
        return jsonSuccessResponse("Produto {$endMessagePart} com sucesso!");
    }

    /**
     * Verifies the data sended and inserts a new product in DB if it doesn't exists
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

        // verifies product id
        if (!empty($requestData['idProducts']) && empty(Products::getById($requestData['idProducts']))) {
            return jsonAlertResponse(
                'O código do produto enviado não pertence a nenhum produto cadastrado.',
                "Sended variable value: {$requestData['idProducts']}"
            );
        }

        // verifies product name
        $productNameValidationError = $this->validateText(
            ($requestData['productName'] ?? ''),
            'Nome do produto',
            'productName'
        );

        if (!empty($productNameValidationError)) {
            return $productNameValidationError;
        }

        // verifies standard value
        $standardValueValidationError = $this->validateText(
            ($requestData['standardValue'] ?? ''),
            'Valor padrão',
            'standardValue',
            validateNumeric: true
        );

        if (!empty($standardValueValidationError)) {
            return $standardValueValidationError;
        }

        // verifies if the data given matches with a product already created
        $productAlreadyCreated = Products::whereName($requestData['productName'])
            ->whereDiffId($requestData['idProducts'])
            ->first();

        if (!empty($productAlreadyCreated)) {
            return jsonAlertResponse('Já existe um produto cadastrado com esse nome.');
        }

        try {
            // insertion array for insert or update
            $arrayCreateOrUpdate = [
                'productName'   => $requestData['productName'],
                'standardValue' => $requestData['standardValue'],
            ];

            // creates a new product
            if (empty($requestData['idProducts'])) {
                $endMessagePart = 'cadastrado';

                Products::create($arrayCreateOrUpdate);

                // updates a client already created
            } else {
                $endMessagePart = 'atualizado';

                Products::getById($requestData['idProducts'])
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
        return jsonSuccessResponse("Produto {$endMessagePart} com sucesso!");
    }
}

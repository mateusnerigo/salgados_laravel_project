<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests,
    Illuminate\Foundation\Bus\DispatchesJobs,
    Illuminate\Foundation\Validation\ValidatesRequests,
    Illuminate\Routing\Controller as BaseController;
use App\Models\SalePoints,
    App\Models\Clients,
    App\Models\Products;
use DateTime;

class Controller extends BaseController {
    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    /**
     * Generic function to validate sended values
     * @param $value            string  Value to validate
     * @param $valueFieldName   string  Value API fieldname
     * @param $variableName     string  API variable to be sended
     * @param $obrigatory       bool    Sets obrigatory validation
     * @param $validateLength   bool    Sets length validation
     * @param $lengthToValidate int     Sets the length for its validation
     */
    public function validateText(
        string $value = '',
        string $valueFieldName = '[ERROR]',
        string $variableName = '',
        bool   $obrigatory = true,
        bool   $validateLength = true,
        int    $lengthToValidate = 3,
        bool   $validateNumeric = false
    ) {
        $msgTemplate = "O campo '{$valueFieldName}'";

        // if it is not set
        if (!isset($value)) {
            return jsonAlertResponse(
                "{$msgTemplate} não foi enviado corretamente.",
                "Empty variable: \$requestData['{$variableName}']."
            );
        }

        // if it is empty
        if ($obrigatory) {
            if (empty($value)) {
                return jsonAlertResponse("{$msgTemplate} deve ser preenchido.");
            }
        }

        if ($validateNumeric) {
            $validateLength = false;
        }

        // if it is shorter than 3 characters
        if ($validateLength) {
            if (strlen($value) < $lengthToValidate) {
                return jsonAlertResponse("{$msgTemplate} deve ter pelo menos 3 letras.");
            }
        }

        // if it is not numeric
        if ($validateNumeric) {
            if (!is_numeric($value)) {
                return jsonAlertResponse("{$msgTemplate} deve ser um número decimal (Exemplo: 19.60)");
            }
        }

        return '';
    }

    /**
     * Auxiliary function to validate sale points sended id
     * @param $idSalePoints
     */
    public static function validateIdSalePoints($idSalePoints) {
        // if it is not set
        if (!isset($idSalePoints)) {
            return jsonAlertResponse(
                "O ponto de venda deste cliente não foi enviado corretamente.",
                "Empty variable: \$requestData['idSalePoints']."
            );
        }

        // sets value for the next verification
        if (empty($idClients)) {
            $idClients = 0;
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
            if (empty(SalePoints::getById($idSalePoints)->first())) {
                return jsonAlertResponse(
                    "O ponto de venda enviado não está cadastrado corretamente.",
                    "Sended variable value: {$idSalePoints}"
                );
            }

            // if the sale point isn't active
            if (empty(SalePoints::getById($idSalePoints)->isActive()->first())) {
                return jsonAlertResponse(
                    "O ponto de venda escolhido não está ativo.",
                    "Sended variable value: {$idSalePoints}"
                );
            }
        }

        return '';
    }

    /**
     * Auxiliary function to validate sended ids
     * @param $idToValidate
     */
    public static function validateId($model, $idToValidate, string $idFrom = '[ERROR]', string $variableName = '') {
        // if it is not set
        if (!isset($idToValidate)) {
            return jsonAlertResponse(
                "A identificação do {$idFrom} não foi enviada corretamente.",
                "Empty variable: \$requestData['{$variableName}']."
            );
        }

        // sets value for the next verification
        if (empty($idToValidate)) {
            $idToValidate = 0;
        }

        // if it is not numeric
        if (!is_numeric($idToValidate)) {
            return jsonAlertResponse(
                "A identificação do {$idFrom} foi enviada de forma equívoca.",
                "Sended variable value: {$idToValidate}"
            );
        }

        // if the id was sended
        if ($idToValidate > 0) {
            // if the client isn't registered
            if (empty($model::getById($idToValidate)->first())) {
                return jsonAlertResponse(
                    "O {$idFrom} enviado não está cadastrado corretamente.",
                    "Sended variable value: {$idToValidate}"
                );
            }

            // if the client isn't active
            if (empty($model::getById($idToValidate)->isActive()->first())) {
                return jsonAlertResponse(
                    "O {$idFrom} escolhido não está ativo.",
                    "Sended variable value: {$idToValidate}"
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
    public static function validateStatus($actualStatus, $newStatus) {
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
    public static function validateDeliverDatetime($deliverDateTime) {
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
    public function validateSaleItems($itemsToValidate) {
        // if it is not set
        if (!isset($itemsToValidate)) {
            return jsonAlertResponse(
                "Os itens da venda não foram enviados.",
                "Empty variable: \$requestData['items']."
            );
        }

        foreach ($itemsToValidate as $item) {
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
    public static function validateSingleSaleItem($itemToValidate) {
        // validar se o id do produto veio


        if (empty(Products::getById($itemToValidate['idProducts']))) {
        }
        return '';
    }
}

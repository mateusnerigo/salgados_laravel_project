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
     * @param $value            mixed   Value to validate
     * @param $valueFieldName   string  Value API fieldname
     * @param $variableName     string  API variable to be sended
     * @param $obrigatory       bool    Sets obrigatory validation
     * @param $validateLength   bool    Sets length validation
     * @param $lengthToValidate int     Sets the length for its validation
     * @param $validateNumeric int      Sets the numeric validation
     * @param $validateInteger int      Sets the integer validation
     */
    public function validateText(
        $value = '',
        string $valueFieldName = '[ERROR]',
        string $variableName = '',
        bool   $obrigatory = true,
        bool   $validateLength = true,
        int    $lengthToValidate = 3,
        bool   $validateNumeric = false,
        bool   $validateInteger = false
    ) {
        $msgTemplate = "O campo '{$valueFieldName}'";

        // if it is not set
        if (!isset($value)) {
            return jsonAlertResponse(
                "{$msgTemplate} não foi enviado corretamente.",
                "Empty variable: {$variableName}."
            );
        }

        // if it is empty
        if ($obrigatory) {
            if (empty($value) && ($value != 0)) {
                return jsonAlertResponse("{$msgTemplate} deve ser preenchido.");
            }
        }

        // if it is shorter than 3 characters
        if ($validateLength && !$validateNumeric && !$validateInteger) {
            if (strlen($value) < $lengthToValidate) {
                return jsonAlertResponse("{$msgTemplate} deve ter pelo menos 3 letras.");
            }
        }

        // if it is not numeric
        if ($validateNumeric && !$validateInteger) {
            if (!is_numeric($value)) {
                return jsonAlertResponse("{$msgTemplate} deve ser um número decimal. (Exemplo: 19.60)");
            }
        }

        // if it is not integer
        if ($validateInteger) {
            if (!is_int($value)) {
                return jsonAlertResponse("{$msgTemplate} deve ser um número inteiro.");
            }
        }

        return '';
    }

    /**
     * Auxiliary function to validate sended ids
     * @param mixed  $model             Model to operate
     * @param mixed  $idToValidate      Id to validate
     * @param string $idFrom            String for return message
     * @param string $variableName      String for dev return message
     * @param bool   $validateIsActive  Sets active verification
     */
    public static function validateId(
        $model,
        $idToValidate,
        string $idFrom = '[ERROR]',
        string $variableName = '',
        bool $validateIsActive = true
    ) {
        // if it is not set
        if (!isset($idToValidate)) {
            return jsonAlertResponse(
                "A identificação do {$idFrom} não foi enviada corretamente.",
                "Empty variable: {$variableName}."
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
                    "O código do {$idFrom} enviado não pertence a nenhum cadastrado.",
                    "Sended variable value: {$idToValidate}"
                );
            }

            // if validation for 'active' status is true
            if ($validateIsActive) {
                // if the client isn't active
                if (empty($model::getById($idToValidate)->isActive()->first())) {
                    return jsonAlertResponse(
                        "O {$idFrom} escolhido não está ativo.",
                        "Sended variable value: {$idToValidate}"
                    );
                }
            }
        }

        return '';
    }

    /**
     * Auxiliary function to validate status sended
     * @param $actualStatus
     * @param $newStatus
     */
    public static function validateSaleStatus($actualStatus, $newStatus) {
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
                "Empty variable: \$requestData['deliverDatetime']."
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
        if (!isset($itemsToValidate) || empty($itemsToValidate)) {
            return jsonAlertResponse(
                "Os itens da venda não foram enviados.",
                "Empty variable: \$requestData['items']."
            );
        }

        foreach ($itemsToValidate as $itemIndex => $item) {
            $itemValidationError = $this->validateSingleSaleItem($item, ($itemIndex + 1));

            if (!empty($itemValidationError)) {
                return $itemValidationError;
            }
        }

        return '';
    }

    /**
     * Auxiliary function to validate a single item from a sale
     * @param $itemToValidate
     * @param $itemIndex
     */
    public function validateSingleSaleItem($itemToValidate, $itemIndex) {
        // product id validation/verification
        $idProductsValidationError = $this->validateId(
            new Products,
            ($itemToValidate['idProducts'] ?? null),
            "produto (linha {$itemIndex})",
            "\$itemToValidate['idProducts']"
        );
        if (!empty($idProductsValidationError)) {
            return $idProductsValidationError;
        }

        // quantity validation/verification
        $quantityValidationError = $this->validateText(
            ($itemToValidate['quantity'] ?? null),
            "quantidade (linha {$itemIndex})",
            "\$itemToValidate['quantity']",
            validateInteger: true
        );
        if (!empty($quantityValidationError)) {
            return $quantityValidationError;
        }

        // price validation/verification
        $soldPriceValidationError = $this->validateText(
            ($itemToValidate['soldPrice'] ?? null),
            "valor (linha {$itemIndex})",
            "\$itemToValidate['soldPrice']",
            validateNumeric: true
        );
        if (!empty($soldPriceValidationError)) {
            return $soldPriceValidationError;
        }

        // discount validation/verification
        if (isset($itemToValidate['discountApplied']) && !empty($itemToValidate['discountApplied'])) {
            $discountAppliedValidationError = $this->validateText(
                ($itemToValidate['discountApplied'] ?? null),
                "desconto (linha {$itemIndex})",
                "\$itemToValidate['discountApplied']",
                validateNumeric: true
            );
        }
        if (!empty($discountAppliedValidationError)) {
            return $discountAppliedValidationError;
        }

        return '';
    }
}

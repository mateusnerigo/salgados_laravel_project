<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests,
    Illuminate\Foundation\Bus\DispatchesJobs,
    Illuminate\Foundation\Validation\ValidatesRequests,
    Illuminate\Routing\Controller as BaseController,
    App\Models\SalePoints;

class Controller extends BaseController {
    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    /**
     * General function to validate sended values
     * @param $value            string  Value to validate
     * @param $valueFieldName   string  Value API fieldname
     * @param $variableName     string  API variable to be sended
     * @param $obrigatory       bool    Sets obrigatory validation
     * @param $validateLength   bool    Sets length validation
     * @param $lengthToValidate int     Sets the length for its validation
     */
    public static function validateText(
        string $value = '',
        string $valueFieldName = '[ERROR]',
        string $variableName = '',
        bool   $obrigatory = true,
        bool   $validateLength = true,
        int    $lengthToValidate = 3
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

        // if it is shorter than 3 characters
        if ($validateLength) {
            if (strlen($value) < $lengthToValidate) {
                return jsonAlertResponse("{$msgTemplate} deve ter pelo menos 3 letras.");
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

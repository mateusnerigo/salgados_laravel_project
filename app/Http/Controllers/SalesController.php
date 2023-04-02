<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\DB,
    App\Models\Sales,
    App\Models\SaleItems,
    App\Models\Clients,
    App\Models\SalePoints,
    Throwable;

class SalesController extends Controller {
    /**
     * Returns sales created
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse {
        if (!empty($request->idSales)) {
            return $this->show($request->idSales);
        }

        return jsonResponse(data: $this->getAllSales($request));
    }

    /**
     * Returns a sale created by id
     * @param mixed $idSales
     * @return JsonResponse
     */
    private function show($idSales): JsonResponse {
        // verifies sale id
        $idSalesValidationError = $this->validateId(
            new Sales,
            $idSales,
            'venda',
            '$idSales',
            false
        );

        if (!empty($idSalesValidationError)) {
            return $idSalesValidationError;
        }

        return jsonResponse(data: $this->getSaleById($idSales));
    }

    /**
     * Updates a sale  status
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request): JsonResponse {
        // receives the data sended in a variable
        $idSales = $request->idSales ?? null;
        $targetStatus = $request->status ?? null;

        // verifies sale id
        $idSalesValidationError = $this->validateId(
            new Sales,
            $idSales,
            'venda',
            '$idSales',
            false
        );

        if (!empty($idSalesValidationError)) {
            return $idSalesValidationError;
        }

        $sale = $this->getSaleById($idSales);

        // verifies sale point id
        $statusValidationError = $this->validateSaleStatus(
            $sale['status'],
            $targetStatus
        );

        if (!empty($statusValidationError)) {
            return $statusValidationError;
        }

        try {
            // updates the sle founded
            Sales::where('idSales', $idSales)
                ->update([
                    'status'            => $targetStatus,
                    'idUsersLastUpdate' => auth()->user()->idUsers
                ]);
        } catch (Throwable $e) {
            // returns it if an error occurs
            return jsonErrorResponse(
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

        // verifies sale id
        $idSalesValidationError = $this->validateId(
            new Sales,
            ($requestData['idSales'] ?? null),
            'venda',
            "\$requestData['idSales']"
        );

        if (!empty($idSalesValidationError)) {
            return $idSalesValidationError;
        }

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

        // verifies deliver datetime
        $deliverDatetimeValidationError = $this->validateDeliverDatetime($requestData['deliverDatetime'] ?? null);
        if (!empty($deliverDatetimeValidationError)) {
            return $deliverDatetimeValidationError;
        }

        //verifies items sended
        $saleItemsValidationError = $this->validateSaleItems($requestData['items'] ?? null);
        if (!empty($saleItemsValidationError)) {
            return $saleItemsValidationError;
        }

        try {
            // transaction for sale integrity
            DB::beginTransaction();

            $userId = auth()->user()->idUsers;
            $timestampNow = now();

            // array for insert or update
            $arrayCreateOrUpdate = [
                'idSalePoints'      => $requestData['idSalePoints'],
                'idClients'         => $requestData['idClients'],
                'deliverDatetime'   => $requestData['deliverDatetime'],
                'idUsersLastUpdate' => $userId,
                'created_at'        => $timestampNow,
                'updated_at'        => $timestampNow
            ];
            unset($timestampNow);

            // creates a new sale
            if (empty($requestData['idSales'])) {
                $arrayCreateOrUpdate['idUsersCreation'] = $userId;

                $endMessagePart = 'cadastrada';

                $idSales = Sales::insertGetId($arrayCreateOrUpdate);

            // updates a sale already created
            } else {
                $endMessagePart = 'atualizada';

                $idSales = $requestData['idSales'];
                Sales::getById($idSales)
                    ->update($arrayCreateOrUpdate);

                // removes the items from the sale updated
                SaleItems::whereIdSales($idSales)
                    ->delete();
            }

            // saves sale items
            foreach ($requestData['items'] as $itemIndex => $item) {
                SaleItems::create([
                    'idSaleItems'     => $itemIndex,
                    'idSales'         => $idSales,
                    'idProducts'      => $item['idProducts'],
                    'quantity'        => $item['quantity'],
                    'soldPrice'       => $item['soldPrice'],
                    'discountApplied' => $item['discountApplied'] ?? 0
                ]);
            }

            // commit if it runs correctely
            DB::commit();
        } catch (Throwable $e) {
            // rollback if it fails
            DB::rollBack();

            // returns a message if an error occurs
            return jsonErrorResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with a successfull message
        return jsonSuccessResponse("Venda {$endMessagePart} com sucesso!");
    }

    /**
     * Return all sales with its items
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAllSales(Request $request) {
        $sales = $this->getAllPaginated(
            new Sales,
            $request,
            [
                'sales.idSales',
                'clients.clientName',
                'sale_points.salePointName',
                'sales.deliverDateTime',
                'sales.created_at'
            ]
        );

        if (!empty($sales['data'])) {
            foreach ($sales['data'] as $index => $sale) {
                $sales['data'][$index]['items'] = $this->getSaleItemsByIdSales($sale->idSales);
            }
        }

        return $sales;
    }

    /**
     * Return a sale with its items by id
     * @param int $idSales Sale id to search
     * @return mixed
     */
    private function getSaleById(int $idSales) {
        $sale = Sales::getById($idSales)
            ->first();

        if (!empty($sale)) {
            $sale['items'] = $this->getSaleItemsByIdSales($idSales);
        }

        return $sale;
    }

    /**
     * Auxiliary function to return the items from a sale by its id
     * @param int $idSales
     * @return mixed
     */
    private function getSaleItemsByIdSales(int $idSales) {
        return SaleItems::whereIdSales($idSales)
            ->ordered()
            ->get();
    }
}

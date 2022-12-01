<?php

Use Illuminate\Http\Request;
Use Illuminate\Http\JsonResponse;

/**
 * Returns an json response for front-end use
 * @param string $msg  Message text
 * @param string $dev  Dev message text
 * @param string $type Message type
 * @param mixed  $data Data to send
 */
function jsonResponse(string $msg = '', string $type = '', string $dev = '', $data = ''): JsonResponse {
    return response()
        ->json([
            'msg'  => $msg,
            'dev'  => $dev,
            'type' => $type,
            'data' => $data,

        ])
        ->withHeaders([
            'Content-Type' => 'json'
        ]);
}

/**
 * Prepares a json response with the given message for use in fron, a message to be used by dev and 'alert' type
 * @return JsonResponse
 */
function jsonAlertResponse(string $msg, string $devMsg = ''): JsonResponse {
    return jsonResponse($msg, 'alert', $devMsg);
}

/**
 * Prepares a json response with the given message and 'success' type
 * @return JsonResponse
 */
function jsonSuccessResponse(string $msg): JsonResponse {
    return jsonResponse($msg, 'success');
}

/**
 * Verifies if the information sended is not empty
 * @param  Request $request Request sended for verification
 * @return bool             Returns true if the request data sended is empty
 */
function isAnEmptyRequest(Request $request): bool {
    return (empty(json_decode($request->data))) ? true : false;
}

/**
 * Prepares a default json alert response
 * @return JsonResponse
 */
function dataSendedErrorResponse(): JsonResponse {
    return jsonAlertResponse('Os dados não foram enviados corretamente.');
}

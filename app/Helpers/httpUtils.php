<?php

Use Illuminate\Http\Request;

/**
 * Returns an json response for front-end use
 * @param string    $msg    Message text
 * @param string    $dev    Dev nessage text
 * @param string    $type   Message type
 * @param mixed     $data   Data to send
 */
function jsonResponse(string $msg = '', string $type = '', string $dev = '', $data = '') {
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

function jsonAlertResponse(string $msg, string $devMsg = '') {
    return jsonResponse($msg, 'alert', $devMsg);
}

function jsonSuccessResponse(string $msg) {
    return jsonResponse($msg, 'success');
}

function validateDataSended(Request $request) {
    return (empty(json_decode($request->data))) ? false : true;
}

function dataSendedErrorResponse() {
    return jsonAlertResponse('Os dados não foram enviados corretamente.',);
}

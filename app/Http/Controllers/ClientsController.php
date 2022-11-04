<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientsController extends Controller {
    public function getAllClients() {
        echo json_encode(['client1' => 'teste', 'cliente2' => 'teste2']);
    }
}

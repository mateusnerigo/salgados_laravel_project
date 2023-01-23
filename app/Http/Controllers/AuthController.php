<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request,
    Illuminate\Support\Facades\Hash,
    App\Models\User,
    Throwable;

class AuthController extends Controller {
    public function register (Request $request) {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // receives the data sended in a variable
        $requestData = json_decode($request->data, true);

        $validateRequestData = $this->validateRequestData($requestData);
        if (!empty($validateRequestData)) {
            return $validateRequestData;
        }

        try {
            $user = User::create([
                'firstName' => $requestData['firstName'],
                'lastName'  => $requestData['lastName'],
                'userName'  => $requestData['userName'],
                'email'     => $requestData['email'],
                'password'  => Hash::make($requestData['password'])
            ]);
        } catch (Throwable $e) {
            // returns a message if an error occurs
            return jsonWarningResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        $token = auth()->attempt([
            'userName' => $requestData['userName'],
            'password' => $requestData['password']
        ]);

        if ($token) {
            // returns with a successfull message
            return jsonResponse(
                "Usuário cadastrado com sucesso!",
                'success',
                data: [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
                ]
            );
        }

        // returns with a successfull message
        return jsonResponse(
            "Usuário cadastrado com sucesso, mas houve falha ao logar!",
            'warning'
        );
    }

    public function login(Request $request) {
        // properly receive the request information
        if (isAnEmptyRequest($request)) {
            return dataSendedErrorResponse();
        }

        // receives the data sended in a variable
        $requestData = json_decode($request->data, true);

        if (
            !isset($requestData['userName']) ||
            (isset($requestData['userName']) && empty($requestData['userName'])) ||
            empty($requestData['password'])
        ) {
            return jsonWarningResponse(
                'Preencha todos os campos para continuar.',
                'Data sended: ' . json_encode($requestData)
            );
        }

        $authCredentials = [
            'password' => $requestData['password'],
            'userName' => $requestData['userName'],
            'isActive' => 1
        ];

        if($token = auth()->attempt($authCredentials)) {
            // returns with a successfull message
            return jsonResponse(
                "Usuário logado com sucesso!",
                'success',
                data: [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => (auth()->factory()->getTTL() * 60)
                ]
            );
        }

        return jsonWarningResponse('Dados de acesso inválidos.');
    }

    public function verifyUserAccess() {
        return jsonResponse(data: [
            'hasAccess' => $this->hasUserAccess() ? 1 : 0
        ]);
    }

    public function logout() {
        auth()->logout();

        // returns with a successfull message
        return jsonSuccessResponse("Usuário deslogado com sucesso!");
    }

    private function validateRequestData(array $requestData) {
        // verifies name
        $nameValidation = $this->validateText(
            ($requestData['firstName'] ?? ''),
            'Nome',
            'firstName'
        );

        if (!empty($nameValidation)) {
            return $nameValidation;
        }

        // verifies las name
        $lastNameValidation = $this->validateText(
            ($requestData['lastName'] ?? ''),
            'Último nome',
            'lastName'
        );

        if (!empty($lastNameValidation)) {
            return $lastNameValidation;
        }

        // verifies username
        $userNameValidation = $this->validateText(
            ($requestData['userName'] ?? ''),
            'Nome de usuário',
            'userName'
        );

        if (!empty($userNameValidation)) {
            return $userNameValidation;
        }

        if (!empty(User::whereUserName($requestData['userName'])->first())) {
            return jsonWarningResponse('Este nome de usuário já está sendo usado.');
        }

        // verifies email
        $emailValidation = $this->validateText(
            ($requestData['email'] ?? ''),
            'Email',
            'email',
            validateEmail: true
        );

        if (!empty($emailValidation)) {
            return $emailValidation;
        }

        if (!empty(User::whereEmail($requestData['email'])->first())) {
            return jsonWarningResponse('Este email já está sendo usado.');
        }

        // verifies password
        $passwordValidation = $this->validateText(
                ($requestData['password'] ?? ''),
                'Senha',
                'password',
                lengthToValidate: 8
            );

        if (!empty($passwordValidation)) {
            return $passwordValidation;
        }

        //  default return
        return '';
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request,
    Illuminate\Support\Facades\Hash,
    Illuminate\Support\Facades\Auth,
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
            return jsonAlertResponse(
                "Há algo errado com os dados enviados.",
                $e->getMessage()
            );
        }

        // returns with a successfull message
        return jsonResponse(
            "Usuário cadastrado com sucesso!",
            'success',
            data: [
                'access_token' => $user->createToken('authToken')->plainTextToken,
                'token_type' => 'Bearer'
            ]
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
            return jsonAlertResponse(
                'Preencha todos os campos para continuar.',
                'Data sended: ' . json_encode($requestData)
            );
        }

        $authCredentials = [
            'password' => $requestData['password'],
            'userName' => $requestData['userName']
        ];

        if(Auth::attempt($authCredentials)) {
            $request->session()->regenerate();
            $user = User::whereUserName($requestData['userName'])->first();

            // returns with a successfull message
            return jsonResponse(
                "Usuário logado com sucesso!",
                'success',
                data: [
                    'access_token' => $user->createToken('authToken')->plainTextToken,
                    'token_type' => 'Bearer'
                ]
            );
        }

        return jsonAlertResponse('Dados de acesso inválidos.');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

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
            return jsonAlertResponse('Este nome de usuário já está sendo usado.');
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
            return jsonAlertResponse('Este email já está sendo usado.');
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

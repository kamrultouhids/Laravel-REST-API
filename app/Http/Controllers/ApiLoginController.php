<?php

namespace App\Http\Controllers;

use JWTAuth;

use JWTAuthException;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class ApiLoginController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'      => 422,
                'response'  => 'Validation error.',
                'errors'    => apiValidateError($validator->errors())
            ]);
        }

        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'code'      => 404,
                    'response'  => 'error',
                    'errors'    => ['massage' => 'invalid email/phone or password.']
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'code'      => 404,
                'response'  => 'error',
                'errors'    => ['massage' => 'failed to create token.']
            ]);
        }

        $user = JWTAuth::toUser($token);
        $user['token'] = $token;
        return response()->json([
            'code'      => 200,
            'response'  => 'Login success.',
            'data'      =>  $user
        ]);

    }
}

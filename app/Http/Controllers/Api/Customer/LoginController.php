<?php

namespace App\Http\Controllers\Api\Customer;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{    
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->guard('api_customer')->attempt($credentials)) {
            //response login failed
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect'
            ], 401);
        }

        //response login success
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => auth()->guard('api_customer')->user()
        ], 200);

        return response()->json(compact('token'));
    }

    /**
     * getUser
     *
     * @param  mixed $request
     * @return void
     */
    public function getUser(Request $request){
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_customer')->user()
        ], 200);
    }
    
    /**
     * refreshToken
     *
     * @param  mixed $request
     * @return void
     */
    public function refreshToken(Request $request)
    {
        //refresh token
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        //set user dengan token baru
        $user = JWTAuth::setToken($refreshToken)->toUser();

        //set header authorization dengan type bearer token baru
        $request->headers->set('Authorization', 'Bearer ' . $refreshToken);

        //response token baru
        return response()->json([
            'success' => true,
            'token' => $refreshToken,
            'user' => $user
        ], 200);
    }

    /**
     * logout
     *
     * @param  mixed $request
     * @return void
     */
    public function logout(){
        //remove token
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        //response logout success
        return response()->json([
            'success' => true,
        ], 200);
    }
}

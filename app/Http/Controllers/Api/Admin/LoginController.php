<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        //set validasi
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        //jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //mengambil data user
        $credentials = $request->only('email', 'password');

        //jika data user tidak sesuai
        if (!$token = auth()->guard('api_admin')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect'
            ], 401);
        }
        //jika data user sesuai
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => auth()->guard('api_admin')->user()
        ], 200);
    }
    
    /**
     * getUser
     *
     * @return void
     */
    public function getUser()
    {
        //response data user yang sedang login
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_admin')->user()]);
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
        $refreshToken = JWTAuth :: refresh(JWTAuth :: getToken());

        //response token yang baru
        $user = JWTAuth::setToken($refreshToken)->toUser();

        //set header "authorization" dengan type bearer + token yang baru
        $request->headers->set('Authorization', 'Bearer ' . $refreshToken);

        //response data user dengan token yang baru
        return response()->json([
            'success' => true,
            'token' => $refreshToken,
            'user' => $user
        ], 200);
    }

    public function logout()
    {
        //remove "token" JWT
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        //response data user yang logout
        return response()->json([
            'success' => true,
        ], 200);
    }
}

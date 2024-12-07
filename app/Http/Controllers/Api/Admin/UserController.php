<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //get users
        $users = User::when(request()->q, function($users) {
            $users = $users->where('name', 'like', '%'. request()->q .'%');
        })->latest()->paginate(5);
        //return with api resource
        return new UserResource(true, 'List Data Users', $users);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(new UserResource('error', $validator->errors(), null));
        }

        //create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($user) {
            return response()->json(new UserResource('success', 'Data user berhasil disimpan', $user));
        }

        //return failed with api resource
        return new UserResource(false, 'Data user gagal disimpan', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::whereId($id)->first();
        if ($user) {
            //return success with api resource
            return new UserResource(true, 'Detail Data User', $user);
        }

        //return failed with api resource
        return new UserResource(false, 'Data user tidak ditemukan', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($request->password == "") {
            //update user without password
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        //update user with new  password
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($user) {
            //return success with api resource
            return new UserResource(true, 'Data user berhasil Diupdate', $user);
        }

        //return failed with api resource
        return new UserResource(false, 'Data user gagal Diupdate', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user){
        if ($user->delete()) {
            //return success with api resource
            return new UserResource(true, 'Data user berhasil dihapus', null);
        }

        //return failed with api resource
        return new UserResource(false, 'Data user gagal dihapus', null);
    }
}

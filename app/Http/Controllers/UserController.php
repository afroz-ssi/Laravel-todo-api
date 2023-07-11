<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Exception;
use DB;
use Auth;
use Hash;

class UserController extends Controller
{

    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (User::where('email', $req->email)->first()) {
            return response()->json([
                'success'=> true,
                'status' => 200,
                'message' => 'Already you have an account, Please login!.',
            ], 200);
        }
        if ($validator->fails()) {
            return response()->json([
                'status' => 404,
                'errors' => $validator->errors(),
            ], 400);
        }
        try {

            $user =  User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password)
            ]);
            $token = $user->createToken($req->email)->plainTextToken; // accessToken;
            return response()->json([
                'status' => 200,
                'message' => 'User registered successfully',
                'data' => $user,
                'access_token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 404, 'error' => $e->getMessage()], 404);
        }
    }



    public function login(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 404,
                'errors' => $validator->errors(),
            ], 400);
        }

        try {

            $user = User::where('email', $req->email)->first();
            if ($user && Hash::check($req->password, $user->password)) {
                $token = $user->createToken($req->email)->plainTextToken; // accessToken;
                return response()->json([
                    'status' => 200,
                    'message' => 'Login successfully',
                    'data' => $user,
                    'access_token' => $token
                ], 200);
            }
            return response()->json(['status' => 404, 'error' => 'Credentials are invalid'], 404);

        } catch (\Throwable $e) {
            return response()->json(['status' => 404, 'error' => $e->getMessage()], 404);
        }
    }


    public function userList(Request $req)
    {

        try {
            $user = User::get();
            return response()->json([
                    'status' => 200,
                    'message' => 'All user',
                    'data' => $user,
                ], 200);
            
        } catch (\Throwable $e) {
            return response()->json(['status' => 404, 'error' => $e->getMessage()], 404);
        }
    }


    public function logout()
    {        
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logout successfully',           
        ], 200);
    }

}

<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\APIController;

class AuthController extends APIController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->throwValidation($validation->errors(),422);
            // return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->respond('User registered successfully!',201);
        // return response()->json(['message' => 'User registered successfully!'], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return $this->throwValidation($validator->errors(),422);
        }
        // dd($request);

        $user = User::where('email', $request->email)->first();
        $adshPass = Hash::make($request->password);
        // dd($adshPass. " >>>> ". $user->password);

        if (!$user || !Hash::check($request->password, $user->password)) {
            $error = array("" => array("Wrong user name and password"));
            return $this->throwValidation($error,422);
            // return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return $this->respond('Login success',$token);
        // return response()->json(['token' => $token], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $inputs = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        if (!Auth::attempt($inputs)) {
            return mdsJsonResponse('invalid credentials', $inputs, 'success', 200);
        }

        $user = User::where("email", $inputs["email"])->firstOrFail();
        $token = $user->createToken("auth_token")->plainTextToken;

        return mdsJsonResponse('signed in', ["user" => $user, "token" => $token], 'success', 200);
    }

    public function logout(Request $request)
    {
        try {
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
                return mdsJsonResponse('signed out', [], "success", 200);
            }
        } catch (Exception $e) {
            return mdsJsonResponse('not authenticated user', $e->getMessage(), "success", 200);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string"
        ]);

        if ($validator->fails()) {
            return mdsJsonResponse("not valid inputs", $validator->errors(), "success", 200);
        }

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        if (!$user->id) {
            return mdsJsonResponse('registration failed', ["request_inputs" => $request->input()], 'success', 200);
        }

        return mdsJsonResponse('registered successfully', ["user" => $user], 'success', 200);
    }
}

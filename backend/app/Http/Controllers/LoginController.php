<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function handleLogin(Request $request)
    {
        // validate phone number
        $request->validate([
            'phone' => 'required|numeric|min:10',
        ]);

        // find or create a new user
        $user = User::firstOrCreate(
            [
                'phone' => $request->phone,
            ]
        );

        if (!$user) {
            return response()->json([
                'message' => 'Could not process a user with that phone number'
            ], 401);
        }

        // send the user a one-time use code        
        $user->notify(new LoginNeedsVerification());

        // return back a message
        return response()->json([
            'message' => 'Text Message notification sent.'
        ], 200);
    }

    public function verify(Request $request)
    {
        // validate the incoming request
        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);

        // find the user
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();

        // is the code provided the same one saved
        // if so, return back an auth token
        if ($user) {
            $user->update([
                'login_code' => null
            ]);
            return $user->createToken($request->login_code)->plainTextToken;
        }

        // if not, return back an error message
        return response()->json([
            'message' => 'Invalid login code.'
        ], 401);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //invoke
    public function __invoke()
    {
        return response()->json(['message' => 'Welcome to the AuthController'], 200);
    }
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate incoming request by using validator and return error message and also check unique constrains according to migration
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'string',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|integer|digits:10|unique:users',
            'password' => 'required|string',
            'role' => 'required|integer',
            'referral_code' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        // Create a new user instance
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name ?? "";
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password); // Hash the password
        $user->referral_code = $request->referral_code ?? "";
        $user->role = $request->role;
        $user->save();

        // Generate a JWT token
        $token = JWTAuth::fromUser($user);
        return response()->json(['user' => $user, 'token' => $token], 200);

    }

    /**
     * Authenticate a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validator
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the user exists
        $user = User::where('username', $request->username)->first();

        // Check if the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate a JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    /**
     * Logout a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        auth()->logout();
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'User logged out successfully'], 200);
    }

    /**
     * Refresh a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        return response()->json(['token' => $token], 200);
    }

    // forgot password
    public function forgotPassword(Request $request)
    {
        // Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate a password reset token
        $token = bin2hex(random_bytes(32));

        // Save the token
        $user->password_reset_token = $token;
        $user->save();

        // Send the token to the user
        return response()->json(['message' => 'Password reset token sent'], 200);
    }

    // reset password
    public function resetPassword(Request $request)
    {
        // Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'token' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the token is correct
        if ($user->password_reset_token !== $request->token) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->save();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    // return  jwt token
    public function returUserResponse(Request $request)
    {
        $user = User::find($request->id);

       // return user details and token
        return response()->json(['user' => $user], 200);

    }

}

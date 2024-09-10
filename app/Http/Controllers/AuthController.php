<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller {

  public function register(UserRegisterRequest $request) {
    $validatedData = $request->validated();

    $user = User::create([
      'name' => $validatedData['name'],
      'email' => $validatedData['email'],
      'password' => bcrypt($validatedData['password']),
    ]);

    $token = auth('api')->login($user);
    return $this->respondWithToken($token);
  }

  public function login() {
    $credentials = request(['email', 'password']);

    if (! $token = auth('api')->attempt($credentials)) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $this->respondWithToken($token);
  }


  public function me() {
    return response()->json(auth('api')->user());
  }


  public function logout() {
    $token = request()->cookie('jwt');

    if ($token) {
      // Set the token and invalidate it
      JWTAuth::setToken($token)->invalidate();
    }

    // Clear the cookie
    $cookie = cookie()->forget('jwt');

    return response()->json(['message' => 'Successfully logged out'])->withCookie($cookie);
  }



  public function refresh() {
    // Get the token from the request cookie
    $token = request()->cookie('jwt');

    if (!$token) {
      return response()->json(['error' => 'No token provided'], 401);
    }

    // Set the token and attempt to refresh it
    JWTAuth::setToken($token);
    $newToken = JWTAuth::refresh($token);

    // Respond with the new token
    return $this->respondWithToken($newToken);
  }



  protected function respondWithToken($token) {
    // Create a cookie to store the JWT token
    $cookie = cookie(
      'jwt',  // Name of the cookie
      $token,  // The token value
      JWTAuth::factory()->getTTL(), // Expiration time in minutes (as per the JWT TTL)
      '/',  // Path
      null,  // Domain (null for default)
      true,  // Secure (true for HTTPS, false for HTTP; ensure HTTPS in production)
      true,  // HTTP Only
      false,  // Raw (not encoded)
      'Strict'  // SameSite (Strict for CSRF protection)
    );

    // Return the user as JSON and set the token as a cookie
    return response()->json(auth('api')->user())
      ->withCookie($cookie);
  }
}

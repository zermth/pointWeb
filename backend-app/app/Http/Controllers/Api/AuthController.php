<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $secretKey = "mockupkey";

    public function login(Request $request)
{
    $username = $request->input('username');
    $password = $request->input('password');
    $expiryTime = time() + (30 * 24 * 60 * 60); // 30 days in seconds

    // Load users' data from the JSON file
    $json = file_get_contents(storage_path('app/data/users.json'));
    $users = json_decode($json, true);

    // Check if the user exists in the users array
    $user = null;
    foreach ($users as $userData) {
        if ($userData['username'] === $username) {
            $user = $userData;
            break;
        }
    }

    // If user is not found or password is incorrect
    if (!$user || $user['password'] !== $password) {
        return response()->json(['message' => 'Username หรือ Password ไม่ถูกต้อง'], 401);
    }

    // Generate JWT token
    $tokenPayload = [
        'sub' => $user['user_id'], // Using user_id from users.json
        'iat' => time(),
        'exp' => $expiryTime
    ];
    $token = JWT::encode($tokenPayload, $this->secretKey, 'HS256');

    // Return the token and set it in a cookie
    return response()->json(['token' => $token])->cookie('jwt_token', $token, 60 * 24 * 30); // 30 days in minutes
}

    public function logout()
    {
        return response()->json(['message' => 'Logout สำเร็จ'])->withCookie(Cookie::forget('jwt_token'));
    }

    public function getUserData(Request $request)
    {
        // Retrieve the token from Authorization header
        $token = $request->bearerToken();
        log::info($token);

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        try {
            // Decode the token to get user ID
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $userId = $decoded->sub;

            // Load users' data from the JSON file
            $json = file_get_contents(storage_path('app/data/users.json'));
            $users = json_decode($json, true);

            // Find the user in the data
            $user = null;
            foreach ($users as &$userData) {
                if ($userData['user_id'] === $userId) {
                    $user = &$userData; // Reference to modify the user's data
                    break;
                }
            }

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json([
                'name' => $user['name'],
                'username' => $user['username'],
                'points' => $user['points']
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }
    }


}

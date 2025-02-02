<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class ProductController extends Controller
{
    private $secretKey = "mockupkey";
    public function index()
    {
 
        $json = file_get_contents(storage_path('app/data/products.json'));
        $products = json_decode($json, true);

        return response()->json($products);
    }

    // แลกสิทธิพิเศษ
    public function redeem(Request $request)
    {
        // Get the Bearer token from the Authorization header
        $token = $request->bearerToken();
    
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
    
        try {
            // Decode the token to get user ID (assuming 'sub' field contains user ID)
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $userId = $decoded->sub; // Get the user_id from the decoded token
    
            // Load the products and users JSON files
            $jsonProducts = file_get_contents(storage_path('app/data/products.json'));
            $products = json_decode($jsonProducts, true);
        
            $jsonUsers = file_get_contents(storage_path('app/data/users.json'));
            $users = json_decode($jsonUsers, true);
        
            // Get product ID from the request
            $productId = $request->input('product_id');
    
            // Find the user and get the points
            $userPoints = null;
            foreach ($users as $user) {
                if ($user['user_id'] == $userId) {
                    $userPoints = $user['points'];
                    break;
                }
            }
    
            if ($userPoints === null) {
                return response()->json(['message' => 'User not found'], 404); // User not found in the users list
            }
    
            Log::info("User ID: $userId, Product ID: $productId, Points: $userPoints");
    
            // Loop through the products and find the one with the matching ID
            foreach ($products as &$product) {
                if ($product['id'] == $productId) {
                    // Check if the product has already been redeemed
                    if ($product['redeemed']) {
                        return response()->json(['message' => 'This reward has already been redeemed'], 400);
                    }
    
                    // Check if the user has enough points to redeem
                    if ($userPoints < $product['points']) {
                        return response()->json(['message' => 'Insufficient points'], 400);
                    }
    
                    // Calculate the remaining points after redemption
                    $remainingPoints = $userPoints - $product['points'];
    
                    // Deduct points from the user in the users list
                    foreach ($users as &$user) {
                        if ($user['user_id'] == $userId) {
                            $user['points'] = $remainingPoints; // Update user's points
                            break;
                        }
                    }
    
                    // Mark the product as redeemed
                    $product['redeemed'] = true;
    
                    // Save the updated products and users back to the JSON files
                    file_put_contents(storage_path('app/data/products.json'), json_encode($products, JSON_PRETTY_PRINT));
                    file_put_contents(storage_path('app/data/users.json'), json_encode($users, JSON_PRETTY_PRINT));
    
                    // Return the success response with remaining points
                    return response()->json([
                        'message' => 'Redemption successful',
                        'remaining_points' => $remainingPoints,
                    ]);
                }
            }
    
            // If the product was not found
            return response()->json(['message' => 'Product not found'], 404);
        } catch (\Exception $e) {
            // Handle error if the token is invalid or expired
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }
    }
    
    

    public function show($id)
    {
        $json = file_get_contents(storage_path('app/data/products.json'));
        $products = json_decode($json, true);

        // Search for the product by ID
        foreach ($products as $product) {
            if ($product['id'] == $id) {
                return response()->json($product);
            }
        }

        return response()->json(['message' => 'ไม่พบสินค้าที่คุณต้องการ'], 404);
    }

}

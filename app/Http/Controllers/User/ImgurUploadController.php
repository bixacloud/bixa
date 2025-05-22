<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImgurUploadController extends Controller
{
    /**
     * Upload an image to Imgur
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // Check if image upload is enabled
        if (Setting::get('enable_image_upload', '0') !== '1') {
            return response()->json([
                'success' => false,
                'error' => 'Image upload feature is disabled.'
            ], 403);
        }

        try {
            // Get the Imgur client ID from settings
            $clientId = Setting::get('imgur_client_id');
            
            if (empty($clientId)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Imgur API credentials are not configured.'
                ], 500);
            }

            // Handle both direct file uploads and base64 encoded images
            $imageData = null;
            $imageType = 'base64'; // Always use base64 for Imgur

            // Check if request has a file upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                
                // Validate the file
                if (!$file->isValid() || !in_array($file->getMimeType(), [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp'
                ])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid image file. Supported formats: JPG, PNG, GIF, WEBP.'
                    ], 400);
                }
                
                // Read file contents and encode as base64 to avoid UTF-8 issues
                $imageData = base64_encode(file_get_contents($file->getPathname()));
                
                Log::info('Processing image file upload', [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ]);
            } 
            // Check if request has base64 encoded image
            elseif ($request->has('image')) {
                $base64Image = $request->input('image');
                
                // Improved base64 detection and extraction
                if (is_string($base64Image)) {
                    // If it's a data URI (e.g., "data:image/png;base64,...")
                    if (strpos($base64Image, 'data:image') === 0) {
                        // Extract the base64 encoded image data
                        $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                        
                        Log::info('Processing data URI image', [
                            'length' => strlen($imageData)
                        ]);
                    } 
                    // If it's already a base64 string without data URI prefix
                    else {
                        // Clean the string in case there's whitespace or other characters
                        $base64Image = trim($base64Image);
                        
                        // Basic validation: check if it's a valid base64 string
                        if (base64_encode(base64_decode($base64Image, true)) === $base64Image) {
                            $imageData = $base64Image;
                            
                            Log::info('Processing raw base64 string', [
                                'length' => strlen($imageData)
                            ]);
                        } else {
                            return response()->json([
                                'success' => false,
                                'error' => 'Invalid base64 image data.'
                            ], 400);
                        }
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Image data must be a string.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'No image data provided.'
                ], 400);
            }

            // Ensure we have image data to upload
            if (empty($imageData)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Empty image data.'
                ], 400);
            }

            // Prepare the request data
            $requestData = [
                'image' => $imageData,
                'type' => $imageType
            ];

            // Log request preparation (without the actual image data)
            Log::info('Preparing Imgur upload request', [
                'type' => $imageType,
                'has_data' => !empty($imageData)
            ]);

            // Make API request to Imgur with improved error handling
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $clientId
            ])->timeout(15)->post('https://api.imgur.com/3/image', $requestData);
            
            // Log the response (for debugging purposes)
            Log::info('Imgur API response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            
            // Check if the upload was successful
            if ($response->successful() && isset($response['data']['link'])) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'url' => $response['data']['link'],
                        'delete_hash' => $response['data']['deletehash'] ?? null,
                        'width' => $response['data']['width'] ?? null,
                        'height' => $response['data']['height'] ?? null,
                    ]
                ]);
            }
            
            // If we got here, something went wrong with the API response
            $errorMessage = isset($response['data']['error']) ? 
                $response['data']['error'] : 'Unknown error occurred';
            
            return response()->json([
                'success' => false,
                'error' => 'Imgur API error: ' . $errorMessage
            ], $response->status() >= 400 ? $response->status() : 500);
            
        } catch (\Exception $e) {
            Log::error('Imgur upload error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
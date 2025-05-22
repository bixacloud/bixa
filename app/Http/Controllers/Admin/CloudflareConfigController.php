<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CloudflareConfig;
use App\Services\CloudflareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;

class CloudflareConfigController extends Controller
{
    protected $cloudflareService;

    public function __construct(CloudflareService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;
    }

    public function index()
    {
        $config = CloudflareConfig::where('is_active', true)->first();
        return view('admin.cloudflare.index', compact('config'));
    }

   public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'api_key' => 'required|string',
            'proxy_domain' => 'required|string',
        ]);

        try {
            // Deactivate old configs
            CloudflareConfig::query()->update(['is_active' => false]);

            // Create new config
            CloudflareConfig::create([
                'email' => $request->email,
                'api_key' => $request->api_key,
                'proxy_domain' => $request->proxy_domain,
                'is_active' => true,
            ]);

            Log::info('Cloudflare configuration updated', [
                'email' => $request->email,
                'proxy_domain' => $request->proxy_domain
            ]);

            return redirect()
                ->route('admin.cloudflare.index')
                ->with('success', 'Cloudflare configuration updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update Cloudflare config', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->withInput(request()->except(['api_key']))
                ->with('error', 'Failed to save configuration');
        }
    }

    public function test()
    {
        try {
            $result = $this->cloudflareService->testConnection();
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connection test successful!'
                ]);
            }

            throw new \Exception('Connection test failed');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 422);
        }
    }
	
    public function testSDK()
    {
        try {
            $config = CloudflareConfig::where('is_active', true)->first();
            
            if (!$config) {
                throw new \Exception('No active configuration found');
            }

            // Log the attempt
            Log::info('Testing Cloudflare SDK connection', [
                'email' => $config->email
            ]);

            $key = new APIKey($config->email, $config->api_key);
            $adapter = new Guzzle($key);
            $user = new User($adapter);

            // Log before API call
            Log::info('Making API call to get user details');

            // Thử lấy thông tin user
            $response = $user->getUserDetails();
            
            // Log response
            Log::info('API Response received', [
                'response' => $response
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Connection test successful!'
            ]);

        } catch (\Exception $e) {
            // Log error details
            Log::error('Cloudflare SDK test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
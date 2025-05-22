<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AdSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdvertisementApiController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        Log::info('AdvertisementApiController initialized');
        
        // Không dùng middleware auth cho API này
        // $this->middleware('auth')->except(['getAdSlots', 'getAdvertisements']);
    }

    /**
     * Get active advertisements
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdvertisements(Request $request)
    {
        Log::info('getAdvertisements called', [
            'ip' => $request->ip(),
            'userAgent' => $request->userAgent(),
            'headers' => $request->headers->all()
        ]);

        try {
            $advertisements = Advertisement::where('is_active', true)->get();
            Log::info('Advertisements retrieved', ['count' => $advertisements->count()]);
            
            // Check if debug param exists
            if ($request->has('debug')) {
                return response()->json([
                    'success' => true,
                    'data' => $advertisements,
                    'debug' => [
                        'endpoint' => 'getAdvertisements',
                        'count' => $advertisements->count(),
                        'first_record' => $advertisements->first()
                    ]
                ]);
            }
            
            return response()->json($advertisements);
        } catch (\Exception $e) {
            Log::error('Error in getAdvertisements', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'debug_trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get active ad slots
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdSlots(Request $request)
    {
        Log::info('getAdSlots called', [
            'ip' => $request->ip(),
            'userAgent' => $request->userAgent(),
            'headers' => $request->headers->all()
        ]);

        try {
            // LỖI: Không thể sử dụng header() trên ResponseFactory
            // $response = response();
            // $response->header('Content-Type', 'application/json');
            
            $slots = AdSlot::where('is_active', true)->get();
            Log::info('Ad slots retrieved', ['count' => $slots->count()]);
            
            // Hiển thị chi tiết debug nếu có tham số debug
            if ($request->has('debug')) {
                return response()->json([
                    'success' => true,
                    'data' => $slots,
                    'debug' => [
                        'endpoint' => 'getAdSlots',
                        'count' => $slots->count(),
                        'first_record' => $slots->first(),
                        'app_debug' => config('app.debug'),
                        'is_json' => true
                    ]
                ]);
            }
            
            // Sử dụng response()->json() trực tiếp thay vì response()->header()
            return response()->json($slots);
        } catch (\Exception $e) {
            Log::error('Error in getAdSlots', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'debug_trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Debug route to test API
     */
    public function testApi()
    {
        Log::info('API test route called');
        
        return response()->json([
            'status' => 'API is working',
            'time' => now()->toDateTimeString(),
            'environment' => app()->environment(),
            'debug' => config('app.debug')
        ]);
    }
}
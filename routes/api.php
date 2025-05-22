<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdvertisementApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Advertisement Module API Routes
Route::prefix('v1')->group(function () {
    // Public advertisement retrieval endpoints
    Route::get('/advertisements', [AdvertisementApiController::class, 'getAdvertisements']);
    Route::get('/ad-slots', [AdvertisementApiController::class, 'getAdSlots']);
    
    // Tracking endpoints - Rate limited to prevent abuse
    Route::middleware(['throttle:60,1'])->group(function () {
        Route::post('/advertisements/{advertisement}/impression', [AdvertisementApiController::class, 'recordImpression']);
        Route::post('/advertisements/{advertisement}/click', [AdvertisementApiController::class, 'recordClick']);
    });
});
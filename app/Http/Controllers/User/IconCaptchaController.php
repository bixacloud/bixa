<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use IconCaptcha\IconCaptcha;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IconCaptchaController extends Controller
{
    /**
     * Process the IconCaptcha request.
     */
   public function processRequest(Request $request)
{
    try {
        $response = new Response();
        $response->headers->set('X-CSRF-TOKEN', csrf_token());
        
        \Log::debug('IconCaptcha request', [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);
        
        $captcha = new \IconCaptcha\IconCaptcha(config('iconcaptcha'));
        $captcha->handleCors();
        $captcha->request()->process();
        
        return $response;
    } catch (\Throwable $e) {
        \Log::error('IconCaptcha error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Validate the IconCaptcha submission.
     */
    public function validateCaptcha(Request $request)
    {
        // Create an instance of IconCaptcha
        $captcha = new IconCaptcha(config('iconcaptcha'));

        // Validate the captcha
        $validation = $captcha->validate($request->all());

        return $validation->success();
    }
}
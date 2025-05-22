<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Google2FAController extends Controller
{
    /**
     * Google2FA instance
     */
    protected $google2fa;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Display 2FA setup page
     */
    public function setup()
{
    $user = Auth::user();
    
    if (!empty($user->google2fa_secret)) {
        return redirect()->route('profile')->with('info', 'Two-factor authentication is already enabled.');
    }
    
    
    $secret = $this->google2fa->generateSecretKey();
    
    return view('2fa.setup', [
        'secret' => $secret
    ]);
}
    /**
     * Enable 2FA for user
     */
    public function enable(Request $request)
    {
        $request->validate([
            'secret' => 'required',
            'one_time_password' => 'required|numeric|digits:6',
        ]);
        
        $user = Auth::user();
        $secret = $request->input('secret');
        $otp = $request->input('one_time_password');
        
        $valid = $this->google2fa->verifyKey($secret, $otp);
        
        if ($valid) {
            $user->google2fa_secret = $secret;
            $user->save();
            
            return redirect()->route('profile')->with('success', 'Two-factor authentication has been successfully enabled.');
        }
        
        return back()->withErrors(['one_time_password' => 'Invalid verification code.']);
    }
    
    /**
     * Generate QR Code image directly
     */
    
public function qrcode(Request $request)
{
    $user = Auth::user();
    
    if ($request->has('secret')) {
        $secret = $request->input('secret');
        
        $this->google2fa->setQrcodeService(
            new \PragmaRX\Google2FAQRCode\QRCode\Bacon(
                new \BaconQrCode\Renderer\Image\ImagickImageBackEnd()
            )
        );
        
        try {
            // Generate QR code image
            $qrCodeImage = $this->google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $secret
            );
            
            // Trả về response với đúng content type
            return response($qrCodeImage)->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            \Log::error('QR Code generation error: ' . $e->getMessage());
            return abort(500, 'Unable to generate QR code');
        }
    }
    
    return abort(404);
}
    
    /**
     * Disable 2FA for user
     */
    public function disable(Request $request)
{
    $request->validate([
        'password' => 'required',
    ]);
    
    $user = Auth::user();
    
    // Kiểm tra xem user có 2FA không
    if (empty($user->google2fa_secret)) {
        return redirect()->route('profile')->with('info', 'Two-factor authentication is not enabled.');
    }
    
    if (!Hash::check($request->input('password'), $user->password)) {
        return back()->withErrors(['password' => 'Incorrect password.']);
    }
    
    $user->google2fa_secret = null;
    $user->save();
    
    // Clear 2FA session
    $this->logout();
    
    return redirect()->route('profile')->with('success', 'Two-factor authentication has been disabled.');
}
    
    /**
     * Show 2FA verification form
     */
    public function getValidateToken()
    {
        if (session('2fa:authenticated')) {
            return redirect()->intended(route('user.dashboard'));
        }
        
        return view('2fa.validate');
    }
    
    /**
     * Validate 2FA token
     */
    public function postValidateToken(Request $request)
{
    $request->validate([
        'one_time_password' => 'required|numeric|digits:6',
    ]);
    
    $user = Auth::user();
    
    if (empty($user->google2fa_secret)) {
        return redirect()->route('user.dashboard');
    }
    
    $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->input('one_time_password'));
    
    if ($valid) {
        
        $request->session()->put('2fa:authenticated', true);
        
        
        \Log::info('2FA authenticated successfully for user: ' . $user->email);
        
        
        $intended = $request->session()->pull('url.intended', route('user.dashboard'));
        
        return redirect()->to($intended);
    }
    
    return back()->withErrors(['one_time_password' => 'Invalid verification code.']);
}
    
    /**
     * Log user out of 2FA
     */
    public function logout()
    {
        session()->forget('2fa:authenticated');
    }
}
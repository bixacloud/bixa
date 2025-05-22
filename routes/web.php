<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

// Import Controllers
use App\Http\Controllers\User\IconCaptchaController;
use App\Http\Controllers\Admin\IconCaptchaSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\OAuthSettingsController;
use App\Http\Controllers\Admin\AcmeSettingController;
use App\Http\Controllers\Admin\MofhApiSettingController;
use App\Http\Controllers\Admin\WebFtpSettingController;
use App\Http\Controllers\Admin\SiteProSettingController;
use App\Http\Controllers\Admin\CloudflareConfigController;
use App\Http\Controllers\Admin\SmtpSettingController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\DomainController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminHostingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MigrationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\HostingController;
use App\Http\Controllers\User\WebFtpController;
use App\Http\Controllers\Admin\WebFtpController as AdminWebFtpController;
use App\Http\Controllers\User\SSLController;
use App\Http\Controllers\User\ToolsController;
use App\Http\Controllers\User\AnnouncementController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\User\TicketController as UserTicketController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\User\AuthenticationLogController;
use App\Http\Controllers\Admin\AuthLogSettingsController;
use App\Http\Controllers\User\Google2FAController;
use App\Http\Controllers\User\WhoisController;
use App\Http\Controllers\User\ImgurUploadController;
use App\Services\MofhService;
use App\Http\Controllers\Admin\KnowledgeCategoryController;
use App\Http\Controllers\Admin\KnowledgeArticleController;
use App\Http\Controllers\User\KnowledgeBaseController;
use App\Http\Controllers\Admin\AdSlotController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Api\AdvertisementApiController;
use App\Models\PasswordResetToken;

/******************************
 * PUBLIC ROUTES (NO AUTH)
 ******************************/

// Home and Language Routes
Route::get('/', [HomeController::class, 'root'])->name('root');
Route::get('lang/{locale}', [HomeController::class, 'lang'])->name('lang');

// Language switching with auto-detection support
Route::get('language/{locale}', function ($locale) {
    // Store language in session for all users (logged in or not)
    Session::put('lang', $locale);
    
    // Clear auto-detection flag when user manually selects a language
    Session::forget('lang_auto_detected');
    
    // Also set the current request locale
    App::setLocale($locale);
    
    // If user is logged in, save language preference to their profile
    if (Auth::check()) {
        // Set locale with auto_detected = false since user manually selected it
        Auth::user()->setLocale($locale, false);
    }
    
    return redirect()->back();
})->name('language.switch');

// Password Reset Routes
Route::get('password/reset/{token}', function($token) {
    $resetToken = PasswordResetToken::where('short_token', $token)
        ->where('created_at', '>', now()->subHours(24))
        ->first();
        
    if (!$resetToken) {
        return redirect()->route('password.request')
            ->withErrors(['email' => 'Invalid or expired token']);
    }
    
    $resetToken->delete();
    
    return view('auth.passwords.reset', ['token' => $resetToken->token]);
})->name('password.reset');

// IconCaptcha API endpoint
Route::post('/iconcaptcha/request', [IconCaptchaController::class, 'processRequest'])
    ->name('iconcaptcha.request')
    ->middleware('web');

// Authentication Routes
Auth::routes(['verify' => true]);

// Social Login Routes 
Route::get('auth/{provider}', [SocialController::class, 'redirectToProvider'])->name('social.login');
Route::get('auth/{provider}/callback', [SocialController::class, 'handleProviderCallback'])->name('social.callback');

// WHOIS API endpoint (accessible without login)
Route::get('/api/whois/{domain}', [WhoisController::class, 'apiInfo'])->name('api.whois.info');

// Advertisement Routes (no authentication required)
// API routes for advertisements (PUBLIC API)
Route::prefix('api')->group(function () {
    Route::get('/ad-slots', [AdvertisementApiController::class, 'getAdSlots']);
    Route::get('/advertisements', [AdvertisementApiController::class, 'getAdvertisements']);
    Route::post('/advertisements/{advertisement}/impression', [AdvertisementController::class, 'recordImpression']);
    Route::post('/advertisements/{advertisement}/click', [AdvertisementController::class, 'recordClick']);
    Route::get('/test', [AdvertisementApiController::class, 'testApi']);
});

// Advertisement Script Route (publicly accessible)
Route::get('build/js/advertisement.js', function () {
    $response = response()->file(public_path('js/advertisement.js'));
    $response->header('Content-Type', 'application/javascript');
    $response->header('Cache-Control', 'public, max-age=86400');
    return $response;
})->name('advertisement.js');

// API Tester Route (tool for debugging API)
Route::get('/api-tester', function() {
    return view('api-tester');
})->name('api.tester');

// Knowledge Base User Routes - No authentication required
Route::prefix('knowledge')->name('knowledge.')->group(function () {
    // Knowledge Base Home
    Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
    
    // Knowledge Base Search
    Route::get('/search', [KnowledgeBaseController::class, 'search'])->name('search');
    
    // Knowledge Base Category
    Route::get('/category/{category:slug}', [KnowledgeBaseController::class, 'category'])->name('category');
    
    // Knowledge Base Article
    Route::get('/category/{category:slug}/{article:slug}', [KnowledgeBaseController::class, 'show'])->name('article');
});

// MOFH Callback Route
Route::post('callback/mofh', function(Request $request) {
    \Log::channel('hosting')->info('Received MOFH callback', $request->all());
    return app(MofhService::class)->handleCallback($request->all());
})->name('mofh.callback')->withoutMiddleware(['web', 'auth', 'csrf']);

/******************************
 * AUTH ROUTES (NO 2FA)
 ******************************/

// Google2FA Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('2fa')->name('2fa.')->group(function () {
        Route::get('/setup', [Google2FAController::class, 'setup'])->name('setup');
        Route::post('/enable', [Google2FAController::class, 'enable'])->name('enable');
        Route::post('/disable', [Google2FAController::class, 'disable'])->name('disable');
        Route::get('/validate', [Google2FAController::class, 'getValidateToken'])->name('validate');
        Route::post('/validate', [Google2FAController::class, 'postValidateToken'])->name('validate.post');
        Route::get('/qrcode', [Google2FAController::class, 'qrcode'])->name('qrcode');
    });
    
    // Dashboard redirector - no 2FA protection needed here
    Route::get('/dashboard', function() {
        $user = auth()->user();
        
        // If email not verified, redirect to verification page
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // Check role and redirect
        if (in_array($user->role, ['admin', 'support'])) {
            // If admin/support has 2FA enabled but not authenticated yet, redirect to 2FA validation
            if ($user->google2fa_secret && !session('2fa:authenticated')) {
                return redirect()->route('2fa.validate');
            }
            
            // For both admin and support, redirect to admin dashboard
            return redirect()->route('admin.dashboard');
        }
        
        // Regular user flow
        if ($user->google2fa_secret && !session('2fa:authenticated')) {
            return redirect()->route('2fa.validate');
        }
        
        return app(UserDashboardController::class)->index();
    })->name('user.dashboard');
});

// Image Upload Route - Unified for both admin and regular users
Route::post('/upload-image', [ImgurUploadController::class, 'upload'])
    ->name('upload.image')
    ->middleware(['auth', 'verified']);

/******************************
 * USER ROUTES
 ******************************/

// User Routes - Protected by 2FA middleware
Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    // Profile and Account Management
    Route::prefix('account')->group(function () {
        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // User Authentication Log Route
        Route::get('/login-history', [AuthenticationLogController::class, 'index'])
            ->name('user.authentication-logs');
    });
    
    // Knowledge Base AJAX Search Route
    Route::get('/knowledge/ajax-search', [KnowledgeBaseController::class, 'ajaxSearch'])
        ->name('knowledge.ajax-search');
    
    // Rate Knowledge Base Article - Authentication required
    Route::post('/knowledge/article/{article}/rate', [KnowledgeBaseController::class, 'rate'])
        ->name('knowledge.rate');
    
    // WHOIS Domain Tools
    Route::prefix('whois')->name('whois.')->group(function () {
        Route::get('/', [WhoisController::class, 'index'])->name('index');
        Route::post('/', [WhoisController::class, 'lookup'])->name('lookup');
        Route::get('/bulk', [WhoisController::class, 'bulkCheck'])->name('bulk');
        Route::post('/bulk', [WhoisController::class, 'bulkCheckProcess'])->name('bulk.check');
        Route::post('/check', [WhoisController::class, 'checkAvailability'])->name('check');
        Route::post('/popup-details', [WhoisController::class, 'getPopupDetails'])->name('popup.details');
    });

    // SSL Certificate Management
    Route::prefix('ssl')->name('ssl.')->group(function () {
        Route::get('/', [SSLController::class, 'index'])->name('index');
        Route::get('/create', [SSLController::class, 'create'])->name('create');
        Route::post('/store', [SSLController::class, 'store'])->name('store');
        Route::get('/{id}', [SSLController::class, 'show'])->name('show');
        Route::post('/{id}/verify', [SSLController::class, 'verify'])->name('verify');
        Route::post('/{id}/revoke', [SSLController::class, 'revoke'])->name('revoke');
        Route::post('/{id}/check-dns', [SSLController::class, 'checkDns'])->name('check-dns');
        Route::post('/{id}/challenge-validate', [SSLController::class, 'challengeValidate'])->name('challenge-validate');
        Route::post('/{id}/switch-method', [SSLController::class, 'switchValidationMethod'])->name('switch-method');
    });

    // Ticket System
    Route::prefix('tickets')->name('user.tickets.')->group(function () {
        Route::get('/', [UserTicketController::class, 'index'])->name('index');
        Route::get('/create', [UserTicketController::class, 'create'])->name('create');
        Route::post('/', [UserTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [UserTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [UserTicketController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/status', [UserTicketController::class, 'updateStatus'])->name('status.update');
        // Rate admin route
        Route::post('/rate/{message}', [UserTicketController::class, 'rateAdminResponse'])->name('rate');
    });

    // Hosting Management
    Route::prefix('hosting')->name('hosting.')->group(function () {
        // Hosting list and info pages
        Route::get('/', [HostingController::class, 'index'])->name('index');
        Route::get('/view/{username}', [HostingController::class, 'view'])->name('view');
        
        // Hosting creation
        Route::get('/create', [HostingController::class, 'create'])->name('create');
        Route::post('/check-domain', [HostingController::class, 'checkDomain'])->name('check-domain');
        Route::post('/store', [HostingController::class, 'store'])->name('store');
        
        // Session route
        Route::get('/cancel', [HostingController::class, 'cancel'])->name('cancel');
        
        // cPanel verification
        Route::post('/{username}/verify-cpanel', [HostingController::class, 'verifyCpanel'])
            ->name('verify-cpanel');
        
        // Settings routes
        Route::get('/settings/{username}', [HostingController::class, 'settings'])
            ->name('settings');
        Route::post('/settings/{username}', [HostingController::class, 'updateSettings'])
            ->name('settings.update');
        
        // Reactivation route
        Route::get('/reactivate/{username}', [HostingController::class, 'reactivate'])
            ->name('reactivate');
        
        // Tool access routes
        Route::get('/cpanel/{username}', [HostingController::class, 'cpanel'])
            ->name('cpanel');
        Route::get('/filemanager/{username}/{domain?}', [HostingController::class, 'fileManager'])
            ->name('filemanager');
        Route::get('/softaculous/{username}', [HostingController::class, 'softaculous'])
            ->name('softaculous');
        Route::get('/builder/{username}/{domain}', [HostingController::class, 'builder'])
            ->name('builder');
        
        // API/JSON routes for stats and database management
        Route::get('/stats/{id}', [HostingController::class, 'getStats'])
            ->name('stats');
        Route::get('/chart-stats/{id}', [HostingController::class, 'getChartStats'])
            ->name('chart.stats');
    });
    
    // WebFTP File Manager
    Route::prefix('webftp')->name('webftp.')->group(function () {
        // SPECIFIC ROUTES FIRST
        // Edit and Download with query parameter instead of path parameter
        Route::get('edit/{username}', [WebFtpController::class, 'edit'])
            ->name('edit');
        Route::get('download/{username}', [WebFtpController::class, 'download'])
            ->name('download');
        Route::post('chmod/{username}', [WebFtpController::class, 'chmod'])
            ->name('chmod');
        
        // Clipboard operations
        Route::post('clipboard/copy/{username}', [WebFtpController::class, 'copyToClipboard'])
            ->name('clipboard.copy');
        Route::post('clipboard/cut/{username}', [WebFtpController::class, 'cutToClipboard'])
            ->name('clipboard.cut');
        Route::post('clipboard/paste/{username}', [WebFtpController::class, 'paste'])
            ->name('clipboard.paste');
        Route::get('clipboard/status/{username}', [WebFtpController::class, 'getClipboardStatus'])
            ->name('clipboard.status');
        Route::post('clipboard/clear/{username}', [WebFtpController::class, 'clearClipboard'])
            ->name('clipboard.clear');
    
        // Other specific routes
        Route::post('save/{username}', [WebFtpController::class, 'saveFile'])
            ->name('saveFile');
        Route::post('upload/{username}/{path?}', [WebFtpController::class, 'upload'])
            ->name('upload')
            ->where('path', '.*');
        Route::post('create-directory/{username}/{path?}', [WebFtpController::class, 'createDirectory'])
            ->name('createDirectory')
            ->where('path', '.*');
        Route::post('create-file/{username}/{path?}', [WebFtpController::class, 'createFile'])
            ->name('createFile')
            ->where('path', '.*');
        Route::post('rename/{username}', [WebFtpController::class, 'rename'])
            ->name('rename');
        Route::post('delete/{username}', [WebFtpController::class, 'delete'])
            ->name('delete');
        
        // Zip operations
        Route::post('zip/{username}/{path?}', [WebFtpController::class, 'zipFiles'])
            ->name('zipFiles')
            ->where('path', '.*');
        Route::post('unzip/{username}/{path?}', [WebFtpController::class, 'unzipFile'])
            ->name('unzipFile')
            ->where('path', '.*');
        
        // GENERAL ROUTE LAST
        Route::get('{username}/{path?}', [WebFtpController::class, 'index'])
            ->name('index')
            ->where('path', '.*');
    });
    
    // Utility Tools Routes
    Route::prefix('tools')->name('tools.')->group(function () {
        // Web Development Tools
        Route::get('/case-converter', [ToolsController::class, 'caseConverter'])
            ->name('case-converter');
        Route::get('/code-beautifier', [ToolsController::class, 'codeBeautifier'])
            ->name('code-beautifier');
        Route::get('/color-tools', [ToolsController::class, 'colorTools'])
            ->name('color-tools');
        Route::get('/base64', [ToolsController::class, 'base64Tool'])
            ->name('base64');
        Route::get('/sql-formatter', [ToolsController::class, 'sqlFormatter'])
            ->name('sql-formatter');
        Route::get('/css-grid-generator', [ToolsController::class, 'cssGridGenerator'])
            ->name('css-grid-generator');
        
        // CDN Search Tool Group
        Route::prefix('cdn-search')->group(function() {
            Route::get('/', [ToolsController::class, 'cdnjsSearch'])
                ->name('cdn-search');
            Route::get('/ajax', [ToolsController::class, 'cdnjsSearchAjax'])
                ->name('cdn-search.ajax');
Route::post('/edge-servers', [ToolsController::class, 'getCdnEdgeServers']);
Route::post('/run-speed-test', [ToolsController::class, 'runCdnSpeedTest']);
        });
        
        // Website Speed Test - Only server-side implementation
        Route::get('/website-speed-test', [ToolsController::class, 'websiteSpeedTest'])
            ->name('website-speed-test');
        Route::post('/website-speed-test/server-side', [ToolsController::class, 'serverSideSpeedTest'])
            ->name('speedtest.server-side');
            Route::post('/website-speed-test/checkhost', [ToolsController::class, 'checkHostTest']);
		Route::post('website-speed-test/pagespeed', [ToolsController::class, 'pageSpeedTest']);
Route::post('/website-speed-test/combined', [ToolsController::class, 'combinedSpeedTest'])
    ->name('website-speed-test.combined');
Route::post('/website-speed-test/pre-check', [ToolsController::class, 'preCheckDomain'])
    ->name('website-speed-test.pre-check');
	Route::post('/website-speed-test/export', [ToolsController::class, 'exportSpeedTestResults'])->name('website-speed-test.export');




        // Other tools
        Route::post('/cdn-copy', [ToolsController::class, 'processCdnCopy'])
            ->name('cdn-copy');
        Route::get('/hosting-upgrade', [ToolsController::class, 'hostingUpgrade'])
            ->name('hosting.upgrade');
        Route::get('/froala-license', [ToolsController::class, 'froalaLicense'])
            ->name('froala-license');
        Route::post('/froala-license/generate', [ToolsController::class, 'generateFroalaLicense'])
            ->name('froala-license.generate');
    });

    // Notification System
    Route::prefix('notifications')->name('notifications.')->group(function () {
        // User notification listing
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        
        // AJAX endpoints for notification dropdown
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
        
        // Mark notifications as read
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        
        // Check for new notifications
        Route::get('/check-new/{lastId}', [NotificationController::class, 'checkNew'])->name('check-new');
        
        // Refresh notification locales
        Route::post('/refresh-locale', [NotificationController::class, 'refreshLocale'])->name('refresh-locale');
        
        // Health check endpoint (for debugging)
        Route::get('/health-check', [NotificationController::class, 'healthCheck'])->name('health-check');
        
        // Dismiss popup
        Route::post('/dismiss-popup', [NotificationController::class, 'dismissPopup'])->name('dismiss-popup');
    });
    
    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');
});

/******************************
 * ADMIN ROUTES
 ******************************/

// Admin Routes - Middleware 'admin' allows both admin and support roles
Route::prefix('admin')->middleware(['auth', 'admin', '2fa'])->group(function () {
    // Dashboard Route - Both admin and support can access
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Admin-specific upload image endpoint
    Route::post('/upload-image', [ImgurUploadController::class, 'upload'])
        ->name('admin.upload-image');
   
    // Routes only for admin role
    Route::middleware(['admin:admin'])->group(function () {
        // Data Migration Routes
        Route::prefix('migration')->group(function () {
            Route::get('/', [MigrationController::class, 'index'])->name('admin.migration.index');
            Route::post('/connect', [MigrationController::class, 'connect'])->name('admin.migration.connect');
            Route::get('/disconnect', [MigrationController::class, 'disconnect'])->name('admin.migration.disconnect');
            Route::get('/start', [MigrationController::class, 'start'])->name('admin.migration.start');
            Route::get('/migrate/{step}', [MigrationController::class, 'migrate'])->name('admin.migration.migrate');
            
            // Password Management Routes
            Route::post('/send-passwords', [MigrationController::class, 'sendPasswordEmails'])->name('admin.migration.send-passwords');
            Route::get('/export-passwords', [MigrationController::class, 'exportPasswords'])->name('admin.migration.export-passwords');
            Route::get('/export-full-passwords', [MigrationController::class, 'exportFullPasswords'])->name('admin.migration.export-full-passwords');
        });
        
        // Admin Documentation Routes
        Route::get('/documentation', [SettingController::class, 'documentation'])->name('admin.documentation');
        Route::get('/tos', [SettingController::class, 'tos'])->name('admin.tos');
        Route::get('/about', [SettingController::class, 'about'])->name('admin.about');
        Route::get('/license', [SettingController::class, 'license'])->name('admin.license');
        Route::get('/donate', [SettingController::class, 'donate'])->name('admin.donate');
    
        // Admin Authentication Log Routes
        Route::get('/authentication-logs', [AuthenticationLogController::class, 'adminIndex'])
            ->name('admin.authentication-logs');
        Route::get('/authentication-logs/user/{userId}', [AuthenticationLogController::class, 'userLogs'])
            ->name('admin.user-authentication-logs');
        
        // Auth Log Settings Routes
        Route::get('/authentication-logs/settings', [AuthLogSettingsController::class, 'index'])
            ->name('admin.auth-log-settings');
        Route::post('/authentication-logs/settings', [AuthLogSettingsController::class, 'update'])
            ->name('admin.auth-log-settings.update');
        
        // GeoIP Update Routes (integrated with Auth Log Settings)
        Route::post('/authentication-logs/settings/update-geoip', [AuthLogSettingsController::class, 'updateGeoIP'])
            ->name('admin.auth-log-settings.update-geoip');
        Route::post('/authentication-logs/settings/background-update', [AuthLogSettingsController::class, 'backgroundUpdateGeoIP'])
            ->name('admin.auth-log-settings.background-update');
        
        // Auth Log Cleanup
        Route::post('/authentication-logs/cleanup', [AuthLogSettingsController::class, 'cleanup'])
            ->name('admin.auth-log-settings.cleanup');
        
        // User Management Routes
        Route::prefix('users')->name('admin.users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
        
        // IconCaptcha Settings Routes
        Route::get('/captcha', [IconCaptchaSettingController::class, 'index'])->name('admin.captcha.index');
        Route::post('/captcha', [IconCaptchaSettingController::class, 'update'])->name('admin.captcha.update');
        Route::post('/captcha/validate-test', [IconCaptchaSettingController::class, 'validateTestCaptcha'])->name('admin.captcha.validate-test');
    
        // Settings Routes
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('admin.settings.index');
            Route::put('/', [SettingController::class, 'update'])->name('admin.settings.update');
            
            // OAuth Routes
            Route::prefix('oauth')->group(function () {
                Route::get('/', [OAuthSettingsController::class, 'index'])->name('admin.settings.oauth');
                Route::post('/', [OAuthSettingsController::class, 'store'])->name('admin.settings.oauth.store');
                Route::post('/{id}/toggle', [OAuthSettingsController::class, 'toggle'])->name('admin.settings.oauth.toggle');
                Route::put('/{id}', [OAuthSettingsController::class, 'update'])->name('admin.settings.oauth.update');
                Route::delete('/{id}', [OAuthSettingsController::class, 'destroy'])->name('admin.settings.oauth.destroy');
            });
    
            // SMTP Routes
            Route::prefix('smtp')->group(function () {
                Route::get('/', [SmtpSettingController::class, 'index'])->name('admin.settings.smtp');
                Route::post('/', [SmtpSettingController::class, 'update'])->name('admin.settings.smtp.update');
                Route::get('/test', [SmtpSettingController::class, 'test'])->name('admin.settings.smtp.test');
            });
        });
    
        // ACME Routes  
        Route::prefix('acme')->group(function () {
            Route::get('/', [AcmeSettingController::class, 'index'])->name('admin.settings.acme');
            Route::put('/', [AcmeSettingController::class, 'update'])->name('admin.settings.acme.update');
        }); 
       
        // Cloudflare Routes  
        Route::prefix('cloudflare')->group(function () {
            Route::get('/', [CloudflareConfigController::class, 'index'])->name('admin.cloudflare.index');
            Route::post('/', [CloudflareConfigController::class, 'store'])->name('admin.cloudflare.store');
            Route::get('/test-sdk', [CloudflareConfigController::class, 'testSDK'])->name('admin.cloudflare.test-sdk');
        });
    
        // Sitepro Routes
        Route::prefix('sitepro')->group(function () {
            Route::get('/', [SiteProSettingController::class, 'index'])->name('admin.sitepro.index');
            Route::post('/', [SiteProSettingController::class, 'update'])->name('admin.sitepro.update');
        });
    
        // Domain Routes
        Route::prefix('domains')->group(function () {
            Route::get('/', [DomainController::class, 'index'])->name('admin.domains.index');
            Route::post('/', [DomainController::class, 'store'])->name('admin.domains.store');
            Route::delete('/{domain}', [DomainController::class, 'destroy'])->name('admin.domains.destroy');
        });
    
        // Email Template Routes
        Route::prefix('email')->group(function () {
            Route::get('/', [EmailTemplateController::class, 'index'])->name('admin.email.index');
            Route::get('/{id}/edit', [EmailTemplateController::class, 'edit'])->name('admin.email.edit');
            Route::put('/{id}', [EmailTemplateController::class, 'update'])->name('admin.email.update');
        });
    
        // MOFH Settings Routes
        Route::prefix('mofh')->group(function () {
            Route::get('/', [MofhApiSettingController::class, 'index'])->name('admin.mofh.settings');
            Route::post('/', [MofhApiSettingController::class, 'update'])->name('admin.mofh.settings.update');
            Route::get('/test', [MofhApiSettingController::class, 'testConnection'])->name('admin.mofh.settings.test');
        });
        
        // WebFTP Settings Routes
        Route::prefix('webftp')->group(function () {
            Route::get('/settings', [WebFtpSettingController::class, 'index'])->name('admin.webftp.settings');
            Route::post('/settings', [WebFtpSettingController::class, 'update'])->name('admin.webftp.settings.update');
        });
        
        // Knowledge Base Categories - Only admin can manage categories
        Route::prefix('knowledge/categories')->name('admin.knowledge.categories.')->group(function () {
            Route::get('/', [KnowledgeCategoryController::class, 'index'])->name('index');
            Route::get('/create', [KnowledgeCategoryController::class, 'create'])->name('create');
            Route::post('/', [KnowledgeCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [KnowledgeCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [KnowledgeCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [KnowledgeCategoryController::class, 'destroy'])->name('destroy');
        });
    });
    
    // Routes for both admin and support roles
    
    // Admin Hosting Routes
    Route::prefix('hosting')->name('admin.hosting.')->group(function () {
        Route::get('/', [AdminHostingController::class, 'index'])->name('index');
        Route::get('/view/{identifier}', [AdminHostingController::class, 'view'])->name('view');
        Route::get('/settings/{identifier}', [AdminHostingController::class, 'settings'])->name('settings');
        Route::post('/settings/{identifier}', [AdminHostingController::class, 'updateSettings'])->name('settings.update');
        Route::get('/cpanel/{identifier}', [AdminHostingController::class, 'cpanel'])
            ->name('cpanel');
        Route::get('/filemanager/{identifier}/{domain?}', [AdminHostingController::class, 'fileManager'])
            ->name('filemanager');
        Route::post('/{identifier}/verify-cpanel', [AdminHostingController::class, 'verifyCpanel'])
            ->name('verify-cpanel');
    });
    
    // Admin WebFTP Routes
    Route::prefix('webftp')->name('admin.webftp.')->group(function () {
        // Specific routes first
        Route::get('edit/{identifier}/{path}', [AdminWebFtpController::class, 'edit'])
            ->name('edit')
            ->where('path', '.*');
        Route::post('save/{identifier}', [AdminWebFtpController::class, 'saveFile'])
            ->name('saveFile');
        Route::post('upload/{identifier}/{path?}', [AdminWebFtpController::class, 'upload'])
            ->name('upload')
            ->where('path', '.*');
        Route::post('create-directory/{identifier}/{path?}', [AdminWebFtpController::class, 'createDirectory'])
            ->name('createDirectory')
            ->where('path', '.*');
        Route::post('create-file/{identifier}/{path?}', [AdminWebFtpController::class, 'createFile'])
            ->name('createFile')
            ->where('path', '.*');
        Route::post('rename/{identifier}', [AdminWebFtpController::class, 'rename'])
            ->name('rename');
        Route::post('delete/{identifier}', [AdminWebFtpController::class, 'delete'])
            ->name('delete');
        Route::get('download/{identifier}/{path}', [AdminWebFtpController::class, 'download'])
            ->name('download')
            ->where('path', '.*');
        Route::post('zip/{identifier}/{path?}', [AdminWebFtpController::class, 'zipFiles'])
            ->name('zipFiles')
            ->where('path', '.*');
        Route::post('unzip/{identifier}/{path?}', [AdminWebFtpController::class, 'unzipFile'])
            ->name('unzipFile')
            ->where('path', '.*');
        
        // General route last
        Route::get('{identifier}/{path?}', [AdminWebFtpController::class, 'index'])
            ->name('index')
            ->where('path', '.*');
    });
    
    // Admin Tickets Routes
    Route::prefix('tickets')->group(function () {
        // Category Management - Only for full admin
        Route::middleware(['admin:admin'])->group(function() {
            Route::get('/categories', [CategoryController::class, 'index'])->name('admin.tickets.categories.index');
            Route::post('/categories', [CategoryController::class, 'store'])->name('admin.tickets.categories.store');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.tickets.categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.tickets.categories.destroy');
        });

        // Ticket Management - For both admin and support
        Route::get('/', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
        Route::patch('/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('admin.tickets.status.update');
        Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('admin.tickets.reply');
        
        // Staff ratings
        Route::get('/ratings/overview', [AdminTicketController::class, 'ratings'])->name('admin.tickets.ratings');
        Route::get('/ratings/staff/{admin}', [AdminTicketController::class, 'adminRatings'])->name('admin.tickets.admin-ratings');
    });
    
    // Knowledge Base Articles
    Route::prefix('knowledge/articles')->name('admin.knowledge.articles.')->group(function () {
        Route::get('/', [KnowledgeArticleController::class, 'index'])->name('index');
        Route::get('/create', [KnowledgeArticleController::class, 'create'])->name('create');
        Route::post('/', [KnowledgeArticleController::class, 'store'])->name('store');
        Route::get('/{article}/edit', [KnowledgeArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [KnowledgeArticleController::class, 'update'])->name('update');
        Route::delete('/{article}', [KnowledgeArticleController::class, 'destroy'])->name('destroy');
    });

    // Ad Slot Management
    Route::prefix('ad-slots')->name('admin.ad-slots.')->group(function () {
        Route::get('/', [AdSlotController::class, 'index'])->name('index');
        Route::get('/create', [AdSlotController::class, 'create'])->name('create');
        Route::post('/', [AdSlotController::class, 'store'])->name('store');
        Route::get('/{adSlot}/edit', [AdSlotController::class, 'edit'])->name('edit');
        Route::put('/{adSlot}', [AdSlotController::class, 'update'])->name('update');
        Route::delete('/{adSlot}', [AdSlotController::class, 'destroy'])->name('destroy');
    });

    // Advertisement Management
    Route::prefix('advertisements')->name('admin.advertisements.')->group(function () {
        Route::get('/', [AdvertisementController::class, 'index'])->name('index');
        Route::get('/create', [AdvertisementController::class, 'create'])->name('create');
        Route::post('/', [AdvertisementController::class, 'store'])->name('store');
        Route::get('/{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('edit');
        Route::put('/{advertisement}', [AdvertisementController::class, 'update'])->name('update');
        Route::delete('/{advertisement}', [AdvertisementController::class, 'destroy'])->name('destroy');
        
        // Statistics routes
        Route::get('/statistics', [AdvertisementController::class, 'statistics'])->name('statistics');
        Route::get('/statistics/{advertisement}', [AdvertisementController::class, 'advertisementStatistics'])->name('statistics.show');
        Route::get('/export/statistics', [AdvertisementController::class, 'exportStatistics'])->name('statistics.export');
    });
    
    // Admin notification routes
    Route::prefix('notifications')->name('admin.notifications.')->group(function () {
        // Announcements
        Route::get('/announcements', [AdminNotificationController::class, 'announcements'])->name('announcements');
        Route::post('/announcements', [AdminNotificationController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::get('/announcements/{id}/edit', [AdminNotificationController::class, 'editAnnouncement'])->name('announcements.edit');
        Route::put('/announcements/{id}', [AdminNotificationController::class, 'updateAnnouncement'])->name('announcements.update');
        Route::delete('/announcements/{id}', [AdminNotificationController::class, 'deleteAnnouncement'])->name('announcements.delete');
        Route::post('/announcements/{id}/toggle', [AdminNotificationController::class, 'toggleAnnouncement'])->name('announcements.toggle');
        
        // Popup notifications management
        Route::get('/popups', [AdminNotificationController::class, 'popups'])->name('popups');
        Route::post('/popups', [AdminNotificationController::class, 'storePopup'])->name('popups.store');
        Route::get('/popups/{id}/edit', [AdminNotificationController::class, 'editPopup'])->name('popups.edit');
        Route::put('/popups/{id}', [AdminNotificationController::class, 'updatePopup'])->name('popups.update');
        Route::delete('/popups/{id}', [AdminNotificationController::class, 'deletePopup'])->name('popups.delete');
        Route::post('/popups/{id}/toggle', [AdminNotificationController::class, 'togglePopup'])->name('popups.toggle');
        
        // Email notifications
        Route::get('/bulk-email', [AdminNotificationController::class, 'emailForm'])->name('bulk-email');
        Route::post('/bulk-email', [AdminNotificationController::class, 'sendEmail'])->name('bulk-email.send');
        
        // Notification Settings
        Route::get('/settings', [AdminNotificationController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminNotificationController::class, 'updateSettings'])->name('settings.update');
    });
    
    // Admin notification cleanup
    Route::get('/notifications/cleanup', [NotificationController::class, 'cleanup'])
        ->name('notifications.cleanup');
});
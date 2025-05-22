<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ url('/') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('build/images/logo-sm.svg') }}" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('build/images/logo-sm.svg') }}" alt="" height="24"> <span class="logo-txt"></span>
                    </span>
                </a>

                <a href="{{ url('/') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('build/images/logo-sm.svg') }}" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('build/images/logo-sm.svg') }}" alt="" height="24"> <span class="logo-txt"></span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            <!-- UPGRADE BUTTON - Fixed alignment issues -->
            @if(Auth::check() && !in_array(Auth::user()->role, ['admin', 'support']))
            <div class="d-inline-block me-3 align-self-center">
                <a href="{{ route('tools.hosting.upgrade') }}" class="btn btn-gradient-primary px-3 py-1 rounded fw-bold shadow-sm upgrade-btn">
                    <i class="fas fa-rocket me-1"></i> UPGRADE
                </a>
            </div>
            @endif
           
            <!-- Language Dropdown with Auto-Detection -->
            <div class="dropdown d-inline-block">
                @php
                    $currentLang = Session::get('lang', config('app.locale'));
                    
                    // Define all supported languages and their properties
                    $supportedLanguages = [
                        'en' => ['name' => 'English', 'flag' => 'us.png'],
                        'es' => ['name' => 'Spanish', 'flag' => 'es.png'],
                        'de' => ['name' => 'German', 'flag' => 'de.png'],
                        'it' => ['name' => 'Italian', 'flag' => 'it.png'],
                        'ru' => ['name' => 'Russian', 'flag' => 'ru.png'],
                        'fr' => ['name' => 'French', 'flag' => 'fr.png'],
                        'pt' => ['name' => 'Portuguese', 'flag' => 'pt.png'],
                        'nl' => ['name' => 'Dutch', 'flag' => 'nl.png'],
                        'ja' => ['name' => 'Japanese', 'flag' => 'ja.png'],
                        'zh' => ['name' => 'Chinese', 'flag' => 'zh.png'],
                        'ar' => ['name' => 'Arabic', 'flag' => 'ar.png'],
                        'hi' => ['name' => 'Hindi', 'flag' => 'in.png'],
                        'ko' => ['name' => 'Korean', 'flag' => 'kr.png'],
                        'vi' => ['name' => 'Vietnamese', 'flag' => 'vn.png'],
                        'th' => ['name' => 'Thai', 'flag' => 'th.png'],
                        'ph' => ['name' => 'Filipino', 'flag' => 'ph.png'],
                        // Add more languages as needed
                    ];
                    
                    // Check if language was auto-detected
                    $isAutoDetected = Session::has('lang_auto_detected') && Session::get('lang_auto_detected');

                    // Get current language properties with fallback to English
                    $currentLangData = $supportedLanguages[$currentLang] ?? $supportedLanguages['en'];
                    
                    // Get available languages from the lang directory
                    $availableLanguages = [];
                    
                    try {
                        // Check if using Laravel 9+ or earlier
                        $langPath = function_exists('lang_path') ? lang_path() : base_path('resources/lang');
                        
                        if (is_dir($langPath)) {
                            // Get directories and JSON files
                            $directories = array_filter(glob($langPath . '/*'), 'is_dir');
                            $jsonFiles = glob($langPath . '/*.json');
                            
                            // Process directories
                            foreach ($directories as $dir) {
                                $langCode = basename($dir);
                                if ($langCode != $currentLang && isset($supportedLanguages[$langCode])) {
                                    $availableLanguages[$langCode] = $supportedLanguages[$langCode];
                                }
                            }
                            
                            // Process JSON files
                            foreach ($jsonFiles as $file) {
                                $langCode = pathinfo($file, PATHINFO_FILENAME);
                                if ($langCode != $currentLang && isset($supportedLanguages[$langCode]) && !isset($availableLanguages[$langCode])) {
                                    $availableLanguages[$langCode] = $supportedLanguages[$langCode];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // In case of error, don't crash the page
                    }
                    
                    // If no languages found other than current, use defaults but exclude current
                    if (empty($availableLanguages)) {
                        foreach ($supportedLanguages as $code => $data) {
                            if ($code != $currentLang) {
                                $availableLanguages[$code] = $data;
                            }
                        }
                    }
                    
                    // Check if flag file exists, use default image path
                    $flagPath = 'build/images/flags/' . $currentLangData['flag'];
                    $defaultFlag = 'build/images/flags/us.png'; // Fallback to English flag
                @endphp
                
                <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <img src="{{ URL::asset($flagPath) }}" alt="{{ $currentLangData['name'] }}" height="16" 
                        onerror="this.onerror=null; this.src='{{ URL::asset($defaultFlag) }}';">
                    <span class="d-none d-sm-inline-block ms-1">{{ $currentLangData['name'] }}
                        @if($isAutoDetected)
                            <i class="mdi mdi-wifi-arrow-down text-success" title="Auto-detected"></i>
                        @endif
                    </span>
                    <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                </button>
                
                <div class="dropdown-menu dropdown-menu-end">
                    @foreach($availableLanguages as $langCode => $langData)
                        <a href="{{ route('language.switch', $langCode) }}" class="dropdown-item notify-item language" data-lang="{{ $langCode }}">
                            <img src="{{ URL::asset('build/images/flags/' . $langData['flag']) }}" alt="{{ $langData['name'] }}" class="me-1" height="12"
                                onerror="this.onerror=null; this.src='{{ URL::asset($defaultFlag) }}';">
                            <span class="align-middle">{{ $langData['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Dark/Light Mode Toggle -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item" id="mode-setting-btn">
                    <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                    <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                </button>
            </div>

            <!-- User Profile Dropdown -->
            @if(Auth::check())
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item topbar-light bg-light-subtle border-start border-end" 
                id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="rounded-circle header-profile-user"
                    src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(Auth::user()->email))) }}?s=80&d=mp"
                    alt="Header Avatar">
                <span class="d-none d-xl-inline-block ms-1 fw-medium">
                    {{ Auth::user()->name }}
                </span>
                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
            </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('profile') }}">
                        <i data-feather="user" class="icon-sm me-2"></i> Profile
                    </a>
                    
                    @if(Auth::check())
                        <a class="dropdown-item" href="{{ Auth::user()->isAdmin() ? route('admin.authentication-logs') : route('user.authentication-logs') }}">
                            <i data-feather="shield" class="icon-sm me-2"></i> Login History
                        </a>
                    @endif
                    
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i data-feather="log-out" class="icon-sm me-2"></i> <span key="t-logout">Log Out</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Custom CSS for the Upgrade button -->
    <style>
        .btn-gradient-primary {
            background: linear-gradient(135deg, #6e57ff, #4e73df);
            border: none;
            color: white;
            transition: all 0.3s ease;
            font-size: 13px;
            letter-spacing: 0.5px;
            line-height: 1.5;
        }
        
        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #5240e3, #3a5ecc);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.4) !important;
            color: white !important; 
        }
        
        .upgrade-btn {
            position: relative;
            overflow: hidden;
            height: 32px;
            display: flex;
            align-items: center;
        }
        
        .upgrade-btn:after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            bottom: -50%;
            left: -50%;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.4) 100%);
            transform: rotateZ(60deg) translate(-5em, 7.5em);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% {
                transform: rotateZ(60deg) translate(-5em, 7.5em);
            }
            100% {
                transform: rotateZ(60deg) translate(1em, -9em);
            }
        }
        
        /* Make sure the button is vertically centered in the navbar header */
        .navbar-header .d-flex {
            align-items: center;
            height: 100%;
        }
        
        @media (max-width: 576px) {
            .btn-gradient-primary {
                padding: 0.25rem 0.5rem !important;
                font-size: 12px;
            }
        }
    </style>
</header>
@yield('css')
 {{-- <link rel="stylesheet" href="{{ URL::asset('build/css/preloader.min.css') }}" type="text/css" /> --}}
 <!-- Bootstrap Css -->
 <link href="{{ URL::asset('build/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet"
     type="text/css" />
 <!-- Icons Css -->
 <link href="{{ URL::asset('build/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
 

 <link href="{{ URL::asset('vendor/iconcaptcha/css/iconcaptcha.min.css') }}" rel="stylesheet" type="text/css" />
 <!-- App Css-->
 <link href="{{ URL::asset('build/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<style>
    /* Custom styles for flag icons in language dropdown */
    .dropdown-item .flag-icon-squared {
        width: 20px;
        height: 15px;
        margin-right: 7px;
        vertical-align: -1px;
        border-radius: 2px;
        box-shadow: 0 0 1px rgba(0,0,0,0.2);
    }

    .header-item .flag-icon-squared {
        width: 20px;
        height: 15px;
        vertical-align: -1px;
        border-radius: 2px;
        box-shadow: 0 0 1px rgba(0,0,0,0.2);
    }

    /* Active language in dropdown */
    .dropdown-item.active .flag-icon-squared {
        box-shadow: 0 0 2px rgba(0,0,0,0.4);
    }

    /* Hover effect */
    .dropdown-item:hover .flag-icon-squared {
        transform: scale(1.05);
        transition: transform 0.2s ease;
    }
</style>
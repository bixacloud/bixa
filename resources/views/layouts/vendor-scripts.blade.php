<!-- JAVASCRIPT -->
<script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/node-waves/waves.min.js') }}"></script>

<script src="{{ URL::asset('build/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ URL::asset('vendor/iconcaptcha/js/iconcaptcha.min.js') }}"></script>
<script src="{{ asset('build/js/pages/dashboard.init.js') }}"></script>
<!-- pace js -->
<script src="{{ URL::asset('build/libs/pace-js/pace.min.js') }}"></script>
<script src="{{ URL::asset('build/js/advertisement.js ') }}"></script>
<script src="build/libs/flatpickr/flatpickr.min.js"></script>
@yield('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>



<script>
  document.addEventListener('DOMContentLoaded', function() {
      if (typeof feather !== 'undefined') {
          feather.replace();
      }

      var modeBtn = document.getElementById('mode-setting-btn');
      if (modeBtn) {
          modeBtn.addEventListener('click', function() {
              var body = document.body;
              
              if (body.hasAttribute("data-bs-theme") && body.getAttribute("data-bs-theme") === "dark") {
                  body.setAttribute('data-bs-theme', 'light');
                  body.setAttribute('data-topbar', 'light');
                  body.setAttribute('data-sidebar', 'light');
                  
                  localStorage.setItem('theme', 'light');
              } else {
                  body.setAttribute('data-bs-theme', 'dark');
                  body.setAttribute('data-topbar', 'dark');
                  body.setAttribute('data-sidebar', 'dark');
                  
                  // Lưu trạng thái
                  localStorage.setItem('theme', 'dark');
              }
              
              console.log('Theme switched to:', body.getAttribute('data-bs-theme'));
          });
          
          var savedTheme = localStorage.getItem('theme');
          if (savedTheme === 'dark') {
              document.body.setAttribute('data-bs-theme', 'dark');
              document.body.setAttribute('data-topbar', 'dark');
              document.body.setAttribute('data-sidebar', 'dark');
          }
      }
  });
</script>
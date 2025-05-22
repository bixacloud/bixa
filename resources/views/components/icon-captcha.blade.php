<div>
    <!-- IconCaptcha widget -->
    <div class="iconcaptcha-widget" data-theme="{{ $theme ?? 'light' }}"></div>
    
    <!-- Security token -->
    @php echo \IconCaptcha\Token\IconCaptchaToken::render(); @endphp
    
    @error('iconcaptcha')
        <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
    @enderror

    <!-- Initialize IconCaptcha -->
    @once
        @push('styles')
            <link href="{{ asset('vendor/iconcaptcha/css/iconcaptcha.min.css') }}" rel="stylesheet" type="text/css">
        @endpush

        @push('scripts')
            <script src="{{ asset('vendor/iconcaptcha/js/iconcaptcha.min.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    IconCaptcha.init('.iconcaptcha-widget', {
                        general: {
                            endpoint: '{{ route('iconcaptcha.request') }}',
                            fontFamily: 'inherit',
                            showCredits: true,
                        },
                        security: {
                            interactionDelay: 1500,
                            hoverProtection: true,
                            displayInitialMessage: true,
                            initializationDelay: 500,
                            incorrectSelectionResetDelay: 3000,
                            loadingAnimationDuration: 1000,
                        },
                        locale: {
                            initialization: {
                                verify: 'Verify that you are human.',
                                loading: 'Loading challenge...',
                            },
                            header: 'Select the image displayed the <u>least</u> amount of times',
                            correct: 'Verification complete.',
                            incorrect: {
                                title: 'Uh oh.',
                                subtitle: "You've selected the wrong image.",
                            },
                            timeout: {
                                title: 'Please wait.',
                                subtitle: 'You made too many incorrect selections.'
                            }
                        }
                    });
                });
            </script>
        @endpush
    @endonce
</div>
@if($enabled)
<div class="icon-captcha-container mb-3">
    <!-- IconCaptcha widget -->
    <div class="iconcaptcha-widget" data-theme="{{ $theme }}" data-unique-id="{{ $uniqueId }}"></div>
    
    <!-- Security token -->
    @php echo \IconCaptcha\Token\IconCaptchaToken::render(); @endphp
</div>
@endif

@once
@push('styles')
<link href="{{ asset('vendor/iconcaptcha/css/iconcaptcha.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .iconcaptcha-widget {
        margin-bottom: 15px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/iconcaptcha/js/iconcaptcha.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof IconCaptcha !== 'undefined') {
        try {
            IconCaptcha.init('.iconcaptcha-widget', {
                general: {
                    endpoint: '{{ route('iconcaptcha.request') }}',
                    fontFamily: 'inherit',
                    showCredits: true,
                },
                security: {
                    interactionDelay: 500,
                    hoverProtection: true,
                    displayInitialMessage: true,
                    initializationDelay: 300,
                    incorrectSelectionResetDelay: 1000,
                    loadingAnimationDuration: 500,
                },
                locale: {
                    initialization: {
                        verify: 'Xác minh bạn là người.',
                        loading: 'Đang tải...',
                    },
                    header: 'Chọn hình ảnh xuất hiện <u>ít nhất</u>',
                    correct: 'Xác minh thành công.',
                    incorrect: {
                        title: 'Rất tiếc.',
                        subtitle: "Bạn đã chọn sai hình ảnh.",
                    },
                    timeout: {
                        title: 'Vui lòng đợi.',
                        subtitle: 'Bạn đã chọn sai quá nhiều lần.'
                    }
                }
            });
            console.log('IconCaptcha initialized successfully');
        } catch (e) {
            console.error('Error initializing IconCaptcha:', e);
        }
    }
});
</script>
@endpush
@endonce
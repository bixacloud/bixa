@extends('layouts.master')

@section('title') Softaculous Installer @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') <a href="{{ route('hosting.index') }}">Hosting</a> @endslot
        @slot('title') Softaculous Installer @endslot
    @endcomponent

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="mb-4">Accessing Softaculous Installer</h4>
                    
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    
                    <p class="mb-4">Please wait while we redirect you to Softaculous...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Redirect ngay lập tức đến URL đã có sẵn credential
    const url = "{{ $url }}";
    
    setTimeout(() => {
        // Mở URL trong tab mới
        const newWindow = window.open(url, '_blank');
        
        if (newWindow) {
            // Nếu mở tab thành công
            Swal.fire({
                title: 'Softaculous Opened',
                html: `
                    <div class="text-center">
                        <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                        <p>Softaculous installer has been opened in a new tab.</p>
                    </div>
                `,
                icon: 'success',
                showConfirmButton: true,
                confirmButtonText: 'Return to Dashboard',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Thay vì chuyển hướng, trở về trang trước đó
                    window.location.href = "{{ route('hosting.view', $username) }}";
                }
            });
        } else {
            // Nếu popup bị chặn
            Swal.fire({
                title: 'Popup Blocked',
                html: `
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                        <p>Please allow popups for this site to access Softaculous.</p>
                        <a href="${url}" class="btn btn-primary mt-3" target="_blank">
                            Open Softaculous Manually
                        </a>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Close',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Trở về trang chi tiết hosting
                    window.location.href = "{{ route('hosting.view', $username) }}";
                }
            });
        }
    }, 1000);
});
</script>
@endsection
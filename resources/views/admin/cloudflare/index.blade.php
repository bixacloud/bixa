@extends('layouts.master')

@section('title') Cloudflare Configuration @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('title') Cloudflare Configuration @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i data-feather="cloud" class="me-2"></i> 
                        Cloudflare Settings
                    </h4>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-feather="check-circle" class="me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i data-feather="alert-circle" class="me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.cloudflare.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <label for="email" class="col-sm-3 col-form-label">
                                <i data-feather="mail" class="me-2"></i> Cloudflare Email
                            </label>
                            <div class="col-sm-9">
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $config->email ?? '') }}"
                                       required>
                                <div class="form-text">
                                    <i data-feather="info" class="me-1"></i>
                                    Your Cloudflare account email address
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="api_key" class="col-sm-3 col-form-label">
                                <i data-feather="key" class="me-2"></i> Global API Key
                            </label>
                            <div class="col-sm-9">
                                <input type="password" 
                                       class="form-control @error('api_key') is-invalid @enderror" 
                                       id="api_key" 
                                       name="api_key" 
                                       value="{{ old('api_key', $config->api_key ?? '') }}"
                                       required>
                                <div class="form-text">
                                    <i data-feather="info" class="me-1"></i>
                                    Your Cloudflare Global API Key (found in Profile > API Tokens > Global API Key)
                                </div>
                                @error('api_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="proxy_domain" class="col-sm-3 col-form-label">
                                <i data-feather="globe" class="me-2"></i> Proxy Domain
                            </label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control @error('proxy_domain') is-invalid @enderror" 
                                       id="proxy_domain" 
                                       name="proxy_domain" 
                                       value="{{ old('proxy_domain', $config->proxy_domain ?? '') }}"
                                       required>
                                <div class="form-text">
                                    <i data-feather="info" class="me-1"></i>
                                    The domain that will host the TXT records (e.g., proxy.example.com)
                                </div>
                                @error('proxy_domain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save" class="me-1"></i> Save Settings
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($config)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <h5 class="alert-heading mb-3">
                                        <i data-feather="info" class="me-2"></i>
                                        Current Configuration
                                    </h5>
                                    <p class="mb-2">
                                        <i data-feather="check-circle" class="me-1"></i>
                                        Status: <span class="badge bg-success">Active</span>
                                    </p>
                                    <p class="mb-2">
                                        <i data-feather="mail" class="me-1"></i>
                                        Email: {{ $config->email }}
                                    </p>
                                    <p class="mb-2">
                                        <i data-feather="globe" class="me-1"></i>
                                        Proxy Domain: {{ $config->proxy_domain }}
                                    </p>
                                    <p class="mb-0">
                                        <i data-feather="clock" class="me-1"></i>
                                        Last Updated: {{ $config->updated_at->format('Y-m-d H:i:s') }}
                                    </p>
                                </div>
                            </div>
                        </div>
						<div class="mt-3">
                    <button type="button" class="btn btn-info" onclick="testSDKConnection()">
                        <i data-feather="check-circle" class="me-1"></i> Test SDK Connection
                    </button>
                </div>
            </div>
        </div>
    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
@parent
<script>
function testSDKConnection() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> Testing...';

    fetch('{{ route("admin.cloudflare.test-sdk") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success',
                    text: 'Connection is working!',
                    icon: 'success',
                    confirmButtonColor: '#556ee6'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Connection failed',
                    icon: 'error',
                    confirmButtonColor: '#556ee6'
                });
            }
        })
        .catch(() => {
            Swal.fire({
                title: 'Error',
                text: 'Connection failed',
                icon: 'error',
                confirmButtonColor: '#556ee6'
            });
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalContent;
            feather.replace();
        });
}
</script>
@endsection
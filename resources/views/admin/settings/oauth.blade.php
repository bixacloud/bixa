@extends('layouts.master')

@section('title') OAuth Settings @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Settings @endslot
        @slot('title') OAuth Settings @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <h4 class="card-title">OAuth Providers Configuration</h4>
                        <button type="button" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addProviderModal">
                            <i class="bx bx-plus me-1"></i> Add New Provider
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Client ID</th>
                                    <th>Redirect URI</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($oauthSettings as $setting)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($setting->provider == 'google')
                                                <i class="bx bxl-google text-danger me-2 font-size-20"></i>
                                            @elseif($setting->provider == 'facebook')
                                                <i class="bx bxl-facebook text-primary me-2 font-size-20"></i>
                                            @endif
                                            {{ ucfirst($setting->provider) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group" style="max-width: 300px;">
                                            <input type="text" class="form-control" value="{{ substr($setting->client_id, 0, 25) }}..." readonly>
                                            <button class="btn btn-light" type="button" onclick="copyToClipboard(this, '{{ $setting->client_id }}')">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group" style="max-width: 300px;">
                                            <input type="text" class="form-control" value="{{ url("/auth/{$setting->provider}/callback") }}" readonly>
                                            <button class="btn btn-light" type="button" onclick="copyToClipboard(this, '{{ url("/auth/{$setting->provider}/callback") }}')">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   onchange="updateStatus(this, {{ $setting->id }})"
                                                   {{ $setting->is_enabled ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    
                                        <td>
    <div class="btn-group">
        <button type="button" class="btn btn-primary btn-sm" 
                onclick='editProvider(@json($setting))'>
            <i class="bx bx-edit-alt"></i>
        </button>
        <button type="button" class="btn btn-danger btn-sm"
                onclick="deleteProvider({{ $setting->id }})">
            <i class="bx bx-trash"></i>
        </button>
    </div>
</td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h5 class="alert-heading"><i class="bx bx-info-circle me-2"></i>Important Notes:</h5>
                            <ul class="mb-0">
                                <li>Redirect URI should be configured in your OAuth provider's dashboard exactly as shown above.</li>
                                <li>Make sure to keep Client Secret secure and never share it publicly.</li>
                                <li>Enable the provider only after properly configuring both Client ID and Client Secret.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addProviderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.settings.oauth.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add OAuth Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Provider</label>
                        <select class="form-select" name="provider" id="provider-select" required>
                            <option value="google">Google</option>
                            <option value="facebook">Facebook</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Redirect URI</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="redirect-uri" readonly>
                            <button class="btn btn-light" type="button" onclick="copyRedirectUri()">
                                <i class="bx bx-copy"></i>
                            </button>
                        </div>
                        <small class="text-muted">Copy this URL to your OAuth provider's dashboard</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client ID</label>
                        <input type="text" class="form-control" name="client_id" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client Secret</label>
                        <input type="password" class="form-control" name="client_secret" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Provider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Provider Modal -->
<div class="modal fade" id="editProviderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editProviderForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit OAuth Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Provider</label>
                        <input type="text" class="form-control" id="edit-provider" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Redirect URI</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="edit-redirect-uri" readonly>
                            <button class="btn btn-light" type="button" onclick="copyEditRedirectUri()">
                                <i class="bx bx-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client ID</label>
                        <input type="text" class="form-control" name="client_id" id="edit-client-id" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client Secret</label>
                        <input type="password" class="form-control" name="client_secret" id="edit-client-secret" 
                               placeholder="Leave blank to keep current secret">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Provider</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Update Redirect URI when provider changes
    document.getElementById('provider-select').addEventListener('change', function() {
        updateRedirectUri(this.value);
    });

    // Initial update for default selected provider
    updateRedirectUri(document.getElementById('provider-select').value);

    function updateRedirectUri(provider) {
        const baseUrl = '{{ url("/") }}';
        const redirectUri = `${baseUrl}/auth/${provider}/callback`;
        document.getElementById('redirect-uri').value = redirectUri;
    }

    function copyRedirectUri() {
        const redirectUri = document.getElementById('redirect-uri');
        navigator.clipboard.writeText(redirectUri.value);
        showToast('Redirect URI copied to clipboard!');
    }

    function copyEditRedirectUri() {
        const redirectUri = document.getElementById('edit-redirect-uri');
        navigator.clipboard.writeText(redirectUri.value);
        showToast('Redirect URI copied to clipboard!');
    }

    function showToast(message) {
        // Add your toast notification logic here
        alert(message); // Temporary alert, replace with your toast implementation
    }

   function editProvider(setting) {
    document.getElementById('edit-provider').value = setting.provider;
    document.getElementById('edit-client-id').value = setting.client_id;
    document.getElementById('edit-redirect-uri').value = `${window.location.origin}/auth/${setting.provider}/callback`;
    document.getElementById('editProviderForm').action = `{{ url('admin/settings/oauth') }}/${setting.id}`;
    new bootstrap.Modal(document.getElementById('editProviderModal')).show();
}
</script>
@endsection

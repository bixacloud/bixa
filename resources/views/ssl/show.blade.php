@extends('layouts.master')

@section('title') @lang('translation.Certificate_Details') @endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet" type="text/css" />
<style>
    /* Additional custom styles if needed */
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Certificate Details -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">@lang('translation.Certificate_Details')</h4>
                        <a href="{{ route('ssl.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> @lang('translation.Back_to_List')
                        </a>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <!-- Basic Info -->
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th width="200">@lang('translation.Domain')</th>
                                    <td>{{ $certificate->domain }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.Provider')</th>
                                    <td><span class="badge bg-primary">{{ ucfirst($certificate->type) }}</span></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.Status')</th>
                                    <td>
                                        @if($certificate->status === 'active')
                                            <span class="badge bg-success">@lang('translation.Active')</span>
                                        @elseif($certificate->status === 'pending')
                                            <span class="badge bg-warning">@lang('translation.status_pending')</span>
                                        @elseif($certificate->status === 'expired')
                                            <span class="badge bg-danger">@lang('translation.Expired')</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($certificate->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($certificate->valid_until)
                                    <tr>
                                        <th>@lang('translation.Valid_Until')</th>
                                        <td>{{ $certificate->valid_until->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>@lang('translation.Created_At')</th>
                                    <td>{{ $certificate->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- DNS Validation Section -->
                    @if($certificate->status === 'pending' && !empty($certificate->dns_validation))
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h5 class="alert-heading mb-3">
                                    <i class="mdi mdi-dns me-2"></i>@lang('translation.DNS_Verification_Required')
                                </h5>
                                <p>@lang('translation.DNS_Verification_Instructions', ['record_type' => $certificate->dns_validation['record_type'] ?? 'DNS'])</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0 bg-white">
                                        <tbody>
                                            <tr>
                                                <th width="150">@lang('translation.Record_Name')</th>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text"
                                                                class="form-control font-monospace"
                                                               value="{{ $certificate->dns_validation['record_name'] ?? '' }}"
                                                               id="recordName"
                                                               readonly>
                                                        <button class="btn btn-light" type="button" onclick="copyToClipboard('recordName')">
                                                            <i class="mdi mdi-content-copy"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>@lang('translation.Record_Type')</th>
                                                <td>
                                                    <code>{{ $certificate->dns_validation['record_type'] ?? 'TXT' }}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ isset($certificate->dns_validation['record_type']) && $certificate->dns_validation['record_type'] === 'CNAME' ? __('translation.Points_To') : __('translation.Value') }}</th>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text"
                                                               class="form-control font-monospace"
                                                               value="{{ $certificate->dns_validation['record_content'] ?? '' }}"
                                                               id="recordContent"
                                                               readonly>
                                                        <button class="btn btn-light" type="button" onclick="copyToClipboard('recordContent')">
                                                            <i class="mdi mdi-content-copy"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">@lang('translation.Important_Steps'):</h6>
                                    <ul class="mb-0">
                                        @if(isset($certificate->dns_validation['use_proxy']) && $certificate->dns_validation['use_proxy'])
                                            <li><strong>@lang('translation.Step') 1:</strong> @lang('translation.CNAME_Record_Step_1')</li>
                                            <li><strong>@lang('translation.Step') 2:</strong> @lang('translation.DNS_Step_2')</li>
                                            <li><strong>@lang('translation.Step') 3:</strong> @lang('translation.DNS_Step_3')</li>
                                            <li><strong>@lang('translation.Step') 4:</strong> @lang('translation.DNS_Step_4')</li>
                                        @else
                                            <li><strong>@lang('translation.Step') 1:</strong> @lang('translation.TXT_Record_Step_1')</li>
                                            <li><strong>@lang('translation.Step') 2:</strong> @lang('translation.DNS_Step_2')</li>
                                            <li><strong>@lang('translation.Step') 3:</strong> @lang('translation.DNS_Step_3')</li>
                                            <li><strong>@lang('translation.Step') 4:</strong> @lang('translation.DNS_Step_4')</li>
                                        @endif
                                    </ul>
                                    
                                    <h6 class="alert-heading mt-3">@lang('translation.Note'):</h6>
                                    <ul class="mb-0">
                                        <li>@lang('translation.DNS_Note_1')</li>
                                        <li>@lang('translation.DNS_Note_2')</li>
                                        @if(isset($certificate->dns_validation['use_proxy']) && $certificate->dns_validation['use_proxy'])
                                            <li>@lang('translation.DNS_Note_3')</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <form action="{{ route('ssl.verify', $certificate->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="mdi mdi-refresh me-1"></i>
                                        @lang('translation.Generate_New_DNS_Record')
                                    </button>
                                </form>
                                <button type="button"
                                         class="btn btn-warning"
                                        id="checkDnsBtn"
                                        onclick="checkDns()">
                                    <span class="normal-text">
                                        <i class="mdi mdi-refresh me-1"></i>
                                        @lang('translation.Check_DNS_Record')
                                    </span>
                                    <span class="loading-text d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        @lang('translation.Checking_DNS')...
                                    </span>
                                </button>
                            </div>
                            <div id="dnsCheckResult" class="alert d-none mt-3"></div>
                            <form action="{{ route('ssl.challenge-validate', $certificate->id) }}"
                                  method="POST"
                                  id="validateForm"
                                  class="mt-3">
                                @csrf
                                <button type="submit"
                                        class="btn btn-success"
                                        id="validateBtn">
                                    <span class="normal-text">
                                        <i class="mdi mdi-check me-1"></i>
                                        @lang('translation.Validate_Issue_Certificate')
                                    </span>
                                    <span class="loading-text d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        @lang('translation.Validating')...
                                    </span>
                                </button>
                            </form>
                        </div>
                    @endif
                    <!-- Certificate Files Section -->
                    @if($certificate->status === 'active')
                        <div class="mt-4">
                            <h5 class="mb-3">@lang('translation.Certificate_Files')</h5>
                            <div class="accordion" id="certificateAccordion">
                                <!-- Private Key -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#privateKey">
                                            <i class="mdi mdi-key me-2"></i> @lang('translation.Private_Key')
                                        </button>
                                    </h2>
                                    <div id="privateKey" class="accordion-collapse collapse" data-bs-parent="#certificateAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-2">
                                                <button class="btn btn-sm btn-light"
                                                        onclick="copyToClipboard('privateKeyContent')">
                                                    <i class="mdi mdi-content-copy me-1"></i> @lang('translation.Copy')
                                                </button>
                                            </div>
                                            <textarea id="privateKeyContent"
                                                     class="form-control font-monospace"
                                                     rows="10"
                                                     readonly>{{ $certificate->private_key }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- CSR -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#csr">
                                            <i class="mdi mdi-file-document-outline me-2"></i> @lang('translation.Certificate_Signing_Request')
                                        </button>
                                    </h2>
                                    <div id="csr" class="accordion-collapse collapse" data-bs-parent="#certificateAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-2">
                                                <button class="btn btn-sm btn-light"
                                                        onclick="copyToClipboard('csrContent')">
                                                    <i class="mdi mdi-content-copy me-1"></i> @lang('translation.Copy')
                                                </button>
                                            </div>
                                            <textarea id="csrContent"
                                                     class="form-control font-monospace"
                                                     rows="10"
                                                     readonly>{{ $certificate->csr }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Certificate -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#certificate">
                                            <i class="mdi mdi-certificate me-2"></i> @lang('translation.Certificate')
                                        </button>
                                    </h2>
                                    <div id="certificate" class="accordion-collapse collapse" data-bs-parent="#certificateAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-2">
                                                <button class="btn btn-sm btn-light"
                                                        onclick="copyToClipboard('certificateContent')">
                                                    <i class="mdi mdi-content-copy me-1"></i> @lang('translation.Copy')
                                                </button>
                                            </div>
                                            <textarea id="certificateContent"
                                                     class="form-control font-monospace"
                                                     rows="10"
                                                     readonly>{{ $certificate->certificate }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- CA Certificate -->
                                @if($certificate->ca_certificate)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#caCertificate">
                                                <i class="mdi mdi-certificate-outline me-2"></i> @lang('translation.CA_Certificate')
                                            </button>
                                        </h2>
                                        <div id="caCertificate" class="accordion-collapse collapse" data-bs-parent="#certificateAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <button class="btn btn-sm btn-light"
                                                            onclick="copyToClipboard('caCertificateContent')">
                                                        <i class="mdi mdi-content-copy me-1"></i> @lang('translation.Copy')
                                                    </button>
                                                </div>
                                                <textarea id="caCertificateContent"
                                                         class="form-control font-monospace"
                                                         rows="10"
                                                         readonly>{{ $certificate->ca_certificate }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- Actions -->
                        <div class="mt-4">
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="confirmRevoke()">
                                <i class="mdi mdi-delete me-1"></i>
                                @lang('translation.Revoke_Certificate')
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revoke Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('translation.Revoke_Certificate')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>@lang('translation.Revoke_Certificate_Confirm')</p>
                <p class="text-danger mb-0">
                    <i class="mdi mdi-alert me-1"></i>
                    @lang('translation.Revoke_Certificate_Warning')
                </p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('ssl.revoke', $certificate->id) }}" method="POST">
                    @csrf
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('translation.Cancel')</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-1"></i>
                        @lang('translation.Revoke_Certificate')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
<script>
function checkDns() {
    const btn = document.getElementById('checkDnsBtn');
    const normalText = btn.querySelector('.normal-text');
    const loadingText = btn.querySelector('.loading-text');
    const resultDiv = document.getElementById('dnsCheckResult');
    const validateBtn = document.getElementById('validateBtn');
    
    // Disable button and show loading state
    btn.disabled = true;
    normalText.classList.add('d-none');
    loadingText.classList.remove('d-none');
    resultDiv.classList.add('d-none');
    
    fetch('{{ route('ssl.check-dns', $certificate->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
        resultDiv.classList.add(data.isValid ? 'alert-success' : 'alert-warning');
        
        let html = `<h6 class="mb-2">${data.message}</h6>`;
        html += `<div class="mb-2"><strong>@lang('translation.Expected_Value'):</strong> <code>${data.expected}</code></div>`;
                
        if (data.found && data.found.length > 0) {
            html += `<div><strong>@lang('translation.Found_Values'):</strong><br>`;
            data.found.forEach(value => {
                html += `<code>${value}</code><br>`;
            });
            html += `</div>`;
        } else {
            html += `<div>@lang('translation.No_records_found')</div>`;
        }
        
        resultDiv.innerHTML = html;
    })
    .catch(error => {
        resultDiv.classList.remove('d-none', 'alert-success', 'alert-warning');
        resultDiv.classList.add('alert-danger');
        resultDiv.innerHTML = `<i class="mdi mdi-alert-circle me-1"></i> ${error.message}`;
    })
    .finally(() => {
        btn.disabled = false;
        normalText.classList.remove('d-none');
        loadingText.classList.add('d-none');
    });
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');
    
    // Use SweetAlert2 for notifications
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'success',
        title: '@lang("translation.Copied_to_clipboard")'
    });
}

function confirmRevoke() {
    const modal = new bootstrap.Modal(document.getElementById('revokeModal'));
    modal.show();
}

// Form submission handlers
document.getElementById('validateForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('validateBtn');
    const normalText = btn.querySelector('.normal-text');
    const loadingText = btn.querySelector('.loading-text');
    
    btn.disabled = true;
    normalText.classList.add('d-none');
    loadingText.classList.remove('d-none');
});

// Handle enter key in copy fields
document.querySelectorAll('input[readonly]').forEach(input => {
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            copyToClipboard(this.id);
        }
    });
});
</script>
@endsection
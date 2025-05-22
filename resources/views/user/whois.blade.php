@extends('layouts.master')

@section('title') @lang('translation.WHOIS_Domain_Lookup') @endsection

@section('css')
<style>
    .domain-info {
        margin-top: 20px;
    }
    .whois-result {
        font-family: monospace;
        white-space: pre-wrap;
        word-break: break-word;
        max-height: 600px;
        overflow-y: auto;
    }
    .info-card {
        margin-bottom: 15px;
    }
    .status-badge {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .domain-available {
        background-color: #d4edda;
        color: #155724;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .domain-registered {
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 5px;
        padding: 10px 20px;
        margin-bottom: 20px;
    }
    .domain-name {
        font-weight: bold;
        color: #007bff;
    }
</style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Tools') @endslot
        @slot('title') @lang('translation.WHOIS_Domain') @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Domain_WHOIS_Lookup')</h4>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="whois-lookup-form" method="post" action="{{ route('whois.lookup') }}">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="domain" id="domain-input" 
                                        placeholder="@lang('translation.Enter_domain_placeholder')" 
                                        value="{{ $domain ?? '' }}" required>
                                    <div class="input-group-append">
                                        <button id="lookup-button" class="btn btn-primary" type="submit">@lang('translation.Lookup')</button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    @lang('translation.Enter_domain_without_http')
                                </small>
                            </div>
                            <div class="col-lg-4">
                                <a href="{{ route('whois.bulk') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list"></i> @lang('translation.Check_Multiple_Domains')
                                </a>
                            </div>
                        </div>
                    </form>

                    <div id="whois-results" class="{{ isset($domain) ? '' : 'd-none' }}">
                        @if(isset($available) && $available)
                            <div class="domain-available">
                                <h5><i class="fas fa-check-circle"></i> @lang('translation.Domain') <span class="domain-name">{{ $domain }}</span> @lang('translation.is_available_for_registration')</h5>
                                <p>@lang('translation.Domain_not_registered_yet')</p>
                            </div>
                        @elseif(isset($info) && $info)
                            <div class="domain-registered">
                                <h5><i class="fas fa-times-circle"></i> @lang('translation.Domain') <span class="domain-name">{{ $domain }}</span> @lang('translation.is_already_registered')</h5>
                            </div>
                            
                            <div class="domain-info">
                                <h5>@lang('translation.Domain_Information'):</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card info-card">
                                            <div class="card-body">
                                                <h5 class="card-title">@lang('translation.Basic_Information')</h5>
                                                <ul class="list-group list-group-flush">
                                                    @if($info->creationDate)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            @lang('translation.Created')
                                                            <span>{{ date('d/m/Y', $info->creationDate) }}</span>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($info->expirationDate)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            @lang('translation.Expires')
                                                            <span>{{ date('d/m/Y', $info->expirationDate) }}</span>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($info->updatedDate)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            @lang('translation.Last_Updated')
                                                            <span>{{ date('d/m/Y', $info->updatedDate) }}</span>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($info->registrar)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            @lang('translation.Registrar')
                                                            <span>{{ $info->registrar }}</span>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($info->owner)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            @lang('translation.Owner')
                                                            <span>{{ $info->owner }}</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        @if(!empty($info->nameServers))
                                            <div class="card info-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">@lang('translation.Nameservers')</h5>
                                                    <ul class="list-group list-group-flush">
                                                        @foreach($info->nameServers as $ns)
                                                            <li class="list-group-item">{{ $ns }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(!empty($info->states))
                                            <div class="card info-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">@lang('translation.Status')</h5>
                                                    <div class="p-2">
                                                        @foreach($info->states as $state)
                                                            <span class="badge bg-info status-badge">{{ $state }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if(isset($result))
                                <div class="mt-4">
                                    <h5>@lang('translation.Detailed_WHOIS_Result'):</h5>
                                    <div class="card">
                                        <div class="card-body bg-light">
                                            <pre class="whois-result">{{ $result }}</pre>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
<script>
    $(document).ready(function() {
        // Main Whois lookup form with Ajax
        $('#whois-lookup-form').on('submit', function(e) {
            e.preventDefault();
            
            const domain = $('#domain-input').val();
            if (!domain) return;
            
            // Show loading state
            $('#lookup-button').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> @lang("translation.Looking_up")');
            $('#lookup-button').prop('disabled', true);
            
            // Clear previous results
            $('#whois-results').html('<div class="d-flex justify-content-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">@lang("translation.Loading")</span></div></div>');
            $('#whois-results').removeClass('d-none');
            
            // Get CSRF token
            const token = $('meta[name="csrf-token"]').attr('content');
            
            // Perform Ajax request
            $.ajax({
                url: "{{ route('whois.lookup') }}",
                type: "POST",
                data: {
                    domain: domain,
                    _token: token,
                    ajax: true
                },
                success: function(response) {
                    // Reset button state
                    $('#lookup-button').html('@lang("translation.Lookup")');
                    $('#lookup-button').prop('disabled', false);
                    
                    // Update URL without reloading the page
                    window.history.pushState({}, "", "{{ route('whois.index') }}?domain=" + domain);
                    
                    // Update the results area with the received HTML
                    $('#whois-results').html(response);
                },
                error: function(xhr) {
                    // Reset button state
                    $('#lookup-button').html('@lang("translation.Lookup")');
                    $('#lookup-button').prop('disabled', false);
                    
                    // Show error message
                    let errorMessage = '@lang("translation.Error_processing_request")';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        errorMessage = '@lang("translation.Invalid_domain_format")';
                    }
                    
                    $('#whois-results').html(
                        '<div class="alert alert-danger">' + 
                            '<i class="fas fa-exclamation-circle"></i> ' + 
                            errorMessage + 
                        '</div>'
                    );
                }
            });
        });
        
        // If domain is already in URL, trigger lookup
        const urlParams = new URLSearchParams(window.location.search);
        const domainParam = urlParams.get('domain');
        if (domainParam && $('#whois-results').hasClass('d-none')) {
            $('#domain-input').val(domainParam);
            $('#whois-lookup-form').trigger('submit');
        }
    });
</script>
@endsection
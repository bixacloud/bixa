@extends('layouts.master')

@section('title') @lang('translation.Bulk_Domain_Check') @endsection

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>
    .domain-available {
        color: #28a745;
        font-weight: bold;
    }
    .domain-taken {
        color: #dc3545;
    }
    .domain-error {
        color: #ffc107;
    }
    .domain-invalid {
        color: #6c757d;
        text-decoration: line-through;
    }
    .loading {
        display: none;
    }
    .modal-whois-result {
        max-height: 400px;
        overflow-y: auto;
        font-family: monospace;
        white-space: pre-wrap;
        word-break: break-word;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        font-size: 14px;
    }
    .modal-domain-name {
        color: #007bff;
        font-weight: bold;
    }
    .cooldown-btn {
        cursor: not-allowed;
        opacity: 0.6;
    }
    .domain-count-warning {
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: 5px;
        display: none;
    }
</style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Tools') @endslot
        @slot('title') @lang('translation.Bulk_Domain_Check') @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Check_Multiple_Domains')</h4>
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="post" action="{{ route('whois.bulk.check') }}" id="bulk-form">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="domains">@lang('translation.List_of_domains')</label>
                                    <textarea class="form-control" name="domains" id="domains" rows="10" required>{{ $domains ?? '' }}</textarea>
                                    <small class="form-text text-muted">
                                        @lang('translation.Enter_domain_without_http')
                                    </small>
                                    <div id="domain-count-warning" class="domain-count-warning">
                                        <i class="fas fa-exclamation-triangle"></i> @lang('translation.Maximum_domains_allowed')
                                    </div>
                                </div>
                                <button class="btn btn-primary mt-3" type="submit" id="check-button">
                                    <span class="normal-text">@lang('translation.Check_Availability')</span>
                                    <span class="loading">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        @lang('translation.Checking')...
                                    </span>
                                </button>
                                <a href="{{ route('whois.index') }}" class="btn btn-secondary mt-3 ml-2">@lang('translation.Single_Domain_Lookup')</a>
                            </div>
                        </div>
                    </form>

                    @if(isset($results) && count($results) > 0)
                        <div class="mt-4">
                            <h5>@lang('translation.Check_Results'):</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="40%">@lang('translation.Domain')</th>
                                            <th width="40%">@lang('translation.Status')</th>
                                            <th width="20%">@lang('translation.Actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $result)
                                            <tr>
                                                <td>{{ $result['domain'] }}</td>
                                                <td class="domain-status">
                                                    @if(!$result['valid'])
                                                        <span class="domain-invalid">@lang('translation.Invalid_domain_format')</span>
                                                    @elseif($result['error'])
                                                        <span class="domain-error">@lang('translation.Error'): {{ $result['error'] }}</span>
                                                    @elseif($result['available'])
                                                        <span class="domain-available">
                                                            <i class="fas fa-check-circle"></i> @lang('translation.Available_for_registration')
                                                        </span>
                                                    @else
                                                        <span class="domain-taken">
                                                            <i class="fas fa-times-circle"></i> @lang('translation.Already_registered')
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($result['valid'] && !$result['error'] && !$result['available'])
                                                        <button type="button" class="btn btn-sm btn-info view-whois-btn" data-domain="{{ $result['domain'] }}">
                                                            <i class="fas fa-search"></i> @lang('translation.View_WHOIS')
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- WHOIS Details Modal -->
    <div class="modal fade" id="whoisDetailsModal" tabindex="-1" aria-labelledby="whoisDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="whoisDetailsModalLabel">@lang('translation.WHOIS_Information')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-loading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">@lang('translation.Loading')</span>
                        </div>
                        <p class="mt-2">@lang('translation.Loading_WHOIS_data')</p>
                    </div>
                    
                    <div id="modal-content" style="display: none;">
                        <h4>@lang('translation.Domain'): <span id="modal-domain-name" class="modal-domain-name"></span></h4>
                        
                        <div id="modal-available" style="display: none;" class="alert alert-success">
                            <i class="fas fa-check-circle"></i> @lang('translation.Domain_available_for_registration')
                        </div>
                        
                        <div id="modal-registered" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">@lang('translation.Basic_Information')</h5>
                                            <ul class="list-group list-group-flush" id="modal-basic-info">
                                                <!-- Basic info will be inserted here -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-3" id="modal-nameservers-card" style="display: none;">
                                        <div class="card-body">
                                            <h5 class="card-title">@lang('translation.Nameservers')</h5>
                                            <ul class="list-group list-group-flush" id="modal-nameservers">
                                                <!-- Nameservers will be inserted here -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="modal-raw-data-section" style="display: none;">
                            <h5 class="mt-3">@lang('translation.Raw_WHOIS_Data'):</h5>
                            <pre id="modal-raw-data" class="modal-whois-result"></pre>
                        </div>
                        
                        <div id="modal-error" style="display: none;" class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <span id="modal-error-message"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('translation.Close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        const modal = new bootstrap.Modal(document.getElementById('whoisDetailsModal'));
        const cooldownTime = 5000; // 5 seconds cooldown
        let buttonCooldowns = {};
        
        // Count domains and show warning if exceeds 10
        $('#domains').on('input', function() {
            const domains = $(this).val().split(/\r\n|\r|\n/).filter(line => line.trim() !== '');
            if (domains.length > 10) {
                $('#domain-count-warning').show();
                $('#check-button').prop('disabled', true);
            } else {
                $('#domain-count-warning').hide();
                $('#check-button').prop('disabled', false);
            }
        });
        
        // Submit form with loading state
        $('#bulk-form').on('submit', function() {
            // Check domain count once more before submitting
            const domains = $('#domains').val().split(/\r\n|\r|\n/).filter(line => line.trim() !== '');
            if (domains.length > 10) {
                $('#domain-count-warning').show();
                return false;
            }
            
            $('.normal-text').hide();
            $('.loading').show();
        });
        
        // View WHOIS button click handler
        $(document).on('click', '.view-whois-btn', function() {
            const button = $(this);
            const domain = button.data('domain');
            
            // Check if button is in cooldown
            if (button.hasClass('cooldown-btn')) {
                return;
            }
            
            // Show modal with loading state
            $('#modal-loading').show();
            $('#modal-content').hide();
            $('#modal-domain-name').text(domain);
            modal.show();
            
            // Fetch WHOIS data
            $.ajax({
                url: "{{ route('whois.popup.details') }}",
                type: "POST",
                data: {
                    domain: domain,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#modal-loading').hide();
                    $('#modal-content').show();
                    
                    if (response.success) {
                        const data = response.data;
                        
                        // Reset all sections
                        $('#modal-available').hide();
                        $('#modal-registered').hide();
                        $('#modal-error').hide();
                        $('#modal-raw-data-section').hide();
                        $('#modal-basic-info').empty();
                        $('#modal-nameservers').empty();
                        $('#modal-status').empty();
                        
                        if (data.available) {
                            // Domain is available
                            $('#modal-available').show();
                        } else if (data.error) {
                            // Error occurred
                            $('#modal-error-message').text(data.error);
                            $('#modal-error').show();
                        } else {
                            // Domain is registered
                            $('#modal-registered').show();
                            
                            // Basic info
                            if (data.info) {
                                if (data.info.creationDate) {
                                    $('#modal-basic-info').append(`<li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('translation.Created')
                                        <span>${data.info.creationDate}</span>
                                    </li>`);
                                }
                                
                                if (data.info.expirationDate) {
                                    $('#modal-basic-info').append(`<li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('translation.Expires')
                                        <span>${data.info.expirationDate}</span>
                                    </li>`);
                                }
                                
                                if (data.info.updatedDate) {
                                    $('#modal-basic-info').append(`<li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('translation.Last_Updated')
                                        <span>${data.info.updatedDate}</span>
                                    </li>`);
                                }
                                
                                if (data.info.registrar) {
                                    $('#modal-basic-info').append(`<li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('translation.Registrar')
                                        <span>${data.info.registrar}</span>
                                    </li>`);
                                }
                                
                                if (data.info.owner) {
                                    $('#modal-basic-info').append(`<li class="list-group-item d-flex justify-content-between align-items-center">
                                        @lang('translation.Owner')
                                        <span>${data.info.owner}</span>
                                    </li>`);
                                }
                                
                                // Nameservers
                                if (data.info.nameServers && data.info.nameServers.length > 0) {
                                    $('#modal-nameservers-card').show();
                                    data.info.nameServers.forEach(ns => {
                                        $('#modal-nameservers').append(`<li class="list-group-item">${ns}</li>`);
                                    });
                                } else {
                                    $('#modal-nameservers-card').hide();
                                }
                                
                                // Status codes
                                if (data.info.states && data.info.states.length > 0) {
                                    $('#modal-status-card').show();
                                    data.info.states.forEach(state => {
                                        $('#modal-status').append(`<span class="badge bg-info status-badge">${state}</span>`);
                                    });
                                } else {
                                    $('#modal-status-card').hide();
                                }
                            }
                        }
                        
                        // Raw data
                        if (data.rawData) {
                            $('#modal-raw-data').text(data.rawData);
                            $('#modal-raw-data-section').show();
                        }
                    } else {
                        // Error in the response
                        $('#modal-error-message').text(response.message || '@lang("translation.Unknown_error")');
                        $('#modal-error').show();
                    }
                },
                error: function(xhr) {
                    $('#modal-loading').hide();
                    $('#modal-content').show();
                    let errorMessage = '@lang("translation.Error_retrieving_WHOIS")';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    $('#modal-error-message').text(errorMessage);
                    $('#modal-error').show();
                }
            });
        });
        
        // Set cooldown when modal is hidden
        $('#whoisDetailsModal').on('hidden.bs.modal', function() {
            const domain = $('#modal-domain-name').text();
            
            // Find the button for this domain
            const button = $(`.view-whois-btn[data-domain="${domain}"]`);
            
            // Apply cooldown
            button.addClass('cooldown-btn');
            buttonCooldowns[domain] = setTimeout(function() {
                button.removeClass('cooldown-btn');
            }, cooldownTime);
        });
    });
</script>
@endsection
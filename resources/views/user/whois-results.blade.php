@if(isset($error) && $error)
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> 
        @lang('translation.Error'): {{ $error }}
    </div>
@elseif(isset($available) && $available)
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
@else
    <div class="domain-registered">
        <h5><i class="fas fa-times-circle"></i> @lang('translation.Domain') <span class="domain-name">{{ $domain }}</span> @lang('translation.appears_to_be_registered')</h5>
        <p>@lang('translation.Could_not_retrieve_info')</p>
    </div>
    
    @if(isset($result))
        <div class="mt-4">
            <h5>@lang('translation.Raw_WHOIS_Result'):</h5>
            <div class="card">
                <div class="card-body bg-light">
                    <pre class="whois-result">{{ $result }}</pre>
                </div>
            </div>
        </div>
    @endif
@endif
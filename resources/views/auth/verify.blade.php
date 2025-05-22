@extends('layouts.master')

@section('title')
    @lang('translation.Verify_Email_Address')
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">@lang('translation.Verify_Email_Address')</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            @lang('translation.Verification_link_sent')
                        </div>
                    @endif

                    @lang('translation.Before_proceeding_check_email')
                    @lang('translation.If_not_receive_email'),
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">@lang('translation.Click_request_another')</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
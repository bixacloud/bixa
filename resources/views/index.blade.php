@extends('layouts.master')

@section('title')
    @lang('translation.Dashboard')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Dashboard
        @endslot
    @endcomponent


@endsection
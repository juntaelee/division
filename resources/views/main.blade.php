@extends('layouts.master')

@push('css')
    {!! Html::style('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') !!}
    <link rel="stylesheet" href="{{ mix('/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ mix('/css/main.css') }}">
@endpush

@push('scripts')
    <!--/*<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js"></script>*/-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
    <script src="{{ mix('/js/main.js') }}"></script>
@endpush

@section('content')

<div id="app">
    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>

    <my-header name="{{ Auth::user()->name }}"></my-header>

    <div class="container-fluid">
        <div class="row">
            <my-nav class="sidebar"></my-nav>
            <my-main class="main"></my-main>
        </div>
    </div>
</div>
@endsection

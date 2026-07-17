@extends('layouts.app')

@section('content')
<div class="layout-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
        </div>
        <div id="app">
        </div>
        </div>        
    </div>
</div>
@endsection
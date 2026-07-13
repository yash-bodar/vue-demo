@extends('layouts.app')

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center mt-3">
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
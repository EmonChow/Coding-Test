@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Product</h1>
    </div>
    <div id="app">
        @php
        $url = json_encode(['url'=>url('/')]);
        @endphp
        <create-product :base_url="{{$url}}" :variants="{{ $variants }}" :variant_details="{{$variant_details->toJson()}}">Loading</create-product>
    </div>
@endsection

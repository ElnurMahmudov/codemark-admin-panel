@extends('layouts.front')

@section('title', 'Əlaqə')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   
@endsection
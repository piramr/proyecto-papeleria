@extends('adminlte::page')

@section('title', 'Perfil')

@section('content_header')
    @if (isset($header))
        {{ $header }}
    @endif
@stop

@section('content')
    {{ $slot ?? '' }}
@stop

@section('css')
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@stop

@section('js')
    @livewireScripts
@stop

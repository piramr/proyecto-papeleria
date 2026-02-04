@extends('layouts.app')

@section('title', 'Auditoría del Sistema')

@section('content_header')
    <h1>Auditoría del Sistema</h1>
@stop

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            @livewire('audit-monitor')
        </div>
    </div>
@stop

@section('js')
    {{-- Scripts handled by Livewire --}}
@stop


@section('css')
    <style>
        .audit-console {
            max-height: 600px;
            overflow-y: auto;
            font-family: 'Fira Code', 'Courier New', monospace;
            background-color: #0d1117;
            /* border-radius: 0.5rem; */
            padding: 1rem;
            /* border: 1px solid #30363d; */
        }
        .audit-console-container {
            background-color: #0d1117;
            color: #c9d1d9;
        }
        /* Custom scrollbar for webkit */
        .audit-console::-webkit-scrollbar {
            width: 8px;
        }
        .audit-console::-webkit-scrollbar-track {
            background: #0d1117; 
        }
        .audit-console::-webkit-scrollbar-thumb {
            background: #30363d; 
            border-radius: 4px;
        }
        .audit-console::-webkit-scrollbar-thumb:hover {
            background: #58a6ff; 
        }
    </style>
    <!-- Tailwind via CDN if not working in AdminLTE, checking... AdminLTE uses Bootstrap usually. 
         We might need to rely on inline styles or verify tailwind presence. 
         The file view_file package.json showed Tailwind is installed, but AdminLTE might conflict. 
         For safety, I'll keep the tailwind classes but assuming typical AdminLTE config includes app.css which has tailwind.
    -->
@stop

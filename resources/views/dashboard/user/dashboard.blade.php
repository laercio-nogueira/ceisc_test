@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if (Auth::user()->role === 'admin')
        <div class="alert alert-info mt-5 text-center">
            Você está logado como <strong>Administrador</strong>.<br>
            Use o menu para acessar o painel administrativo.
        </div>
    @else
        @if (Auth::user()->hasActivePlan())
            @include('dashboard.user.content')
        @else
            @include('dashboard.user.plans')
        @endif
    @endif
@endsection

@extends('layouts.app')

@section('title', 'Registrar')

@section('content')
<div class="auth-card card shadow">
    <div class="card-body">
        <h2 class="card-title text-center mb-4">Criar Conta</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small class="form-text text-muted">Mínimo de 8 caracteres</small>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>

            @if(config('auth.allow_role_selection'))
            <div class="mb-3">
                <label class="form-label">Tipo de Conta</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="role_user" value="user" checked>
                    <label class="form-check-label" for="role_user">Usuário Comum</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="role_admin" value="admin">
                    <label class="form-check-label" for="role_admin">Administrador</label>
                </div>
            </div>
            @endif

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>

            <div class="mt-3 text-center">
                Já tem uma conta? <a href="{{ route('login') }}">Faça login</a>
            </div>
        </form>
    </div>
</div>
@endsection

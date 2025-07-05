@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-card card shadow">
    <div class="card-body">
        <h2 class="card-title text-center mb-4">Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Lembrar-me</label>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>

            <div class="mt-3 text-center">
                <a href="#">Esqueceu sua senha?</a>
            </div>
        </form>
    </div>
</div>
@endsection

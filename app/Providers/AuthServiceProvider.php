<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        // Defina escopos para as permissões
        Passport::tokensCan([
            'admin' => 'Acesso total ao sistema',
            'user' => 'Acesso básico ao sistema',
        ]);

        // Escopo padrão
        Passport::setDefaultScope([
            'user',
        ]);
    }
}

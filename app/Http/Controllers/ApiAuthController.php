<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth-token')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'active' => $user->active,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();

        $token->revoke();

        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'active' => $user->active,
                'has_active_plan' => $user->hasActivePlan(),
                'current_plan' => $user->currentPlan ? [
                    'plan_name' => $user->currentPlan->plan->name,
                    'status' => $user->currentPlan->status,
                    'expires_at' => $user->currentPlan->expires_at,
                ] : null,
            ]
        ]);
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $refreshToken = $request->refresh_token;

        $user = $request->user();
        $newToken = $user->createToken('auth-token')->accessToken;

        return response()->json([
            'success' => true,
            'access_token' => $newToken,
            'token_type' => 'Bearer',
        ]);
    }
}

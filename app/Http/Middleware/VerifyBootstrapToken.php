<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * admin側からのテナント初期化リクエスト(POST /internal/bootstrap)を、
 * テナントごとに払い出されたトークン(INTERNAL_BOOTSTRAP_SECRET)で認証する
 */
class VerifyBootstrapToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.internal_bootstrap.secret');
        $token = $request->bearerToken();

        if (! $secret || ! $token || ! hash_equals($secret, $token)) {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}

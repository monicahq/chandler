<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Facades\App;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\TransientToken;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Authenticate user with Basic Authentication, with Sanctum token on password field.
 *
 * Examples:
 *   curl -u "email@example.com:$TOKEN" -X PROPFIND https://localhost/dav/
 */
class AuthenticateWithTokenOnBasicAuth
{
    /**
     * Create a new middleware instance.
     *
     * @param  AuthManager  $auth
     * @return void
     */
    public function __construct(private AuthManager $auth)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return (new Pipeline(app()))->send($request)->through($this->auth->guard()->check() ? [
            function ($request, $next) {
                if (App::environment('local')) {
                    $this->auth->guard('sanctum')->setUser($request->user()->withAccessToken(new TransientToken));
                }

                return $next($request);
            },
        ] : [
            function ($request, $next) {
                Sanctum::getAccessTokenFromRequestUsing(fn ($request) => $request->bearerToken() ?? $request->getPassword()
                );

                return $next($request);
            },
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            function ($request) use ($next) {
                if (! $this->basicAuth($request)) {
                    $this->failedBasicResponse();
                }

                return $next($request);
            },
        ])
        ->then(fn ($request) => $next($request));
    }

    /**
     * Try Bearer authentication, with token in 'password' field on basic auth.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    private function basicAuth(Request $request)
    {
        if (($user = $this->sanctumUser($request)) !== null) {
            $this->auth->guard()->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Authenticate user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return User|null
     */
    private function sanctumUser(Request $request): ?User
    {
        /** @var \Illuminate\Auth\RequestGuard */
        $guard = $this->auth->guard('sanctum');

        /** @var ?User */
        $user = $guard->setRequest($request)->user();

        // if there is no bearer token PHP_AUTH_USER header must match user email
        if ($user->currentAccessToken() !== null
            && $request->bearerToken() !== null
            && $request->getUser() !== $user->email) {
            return null;
        }

        return $user;
    }

    /**
     * Get the response for basic authentication.
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    protected function failedBasicResponse()
    {
        throw new UnauthorizedHttpException('Basic', 'Invalid credentials.');
    }
}

<?php

namespace App\Http\Middleware;

use App\Utils\Helper;
use Closure;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Middleware\GetUserFromToken;

class ApiAuthentication extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->header('request-ajax') == null)
        {
            if (! $token = $this->auth->setRequest($request)->getToken()) {
                return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
            }

            try {
                $user = $this->auth->authenticate($token);
            } catch (TokenExpiredException $e) {
                return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
            } catch (JWTException $e) {
                return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
            }

            if (! $user) {
                return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
            }

            $this->events->fire('tymon.jwt.valid', $user);
        }
        else
        {
            $ajax = Helper::validaRequisicaoAjax($request->header('request-ajax'));

            if(!$ajax)
            {
                return ['success' => false, 'log' => ['A requisição ajax não foi permitida']];
            }
        }

        return $next($request);
    }

}

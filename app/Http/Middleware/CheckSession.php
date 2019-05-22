<?php

namespace App\Http\Middleware;

use App\Empresa;
use App\Medico;
use App\Atendente;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $success = true;
        $log     = [];

        if(session('auth.token') !== Auth::user()->auth_token)
        {
            session(['auth.token' => null]);

            $log[] = ['error' => 'Acesso expirado'];

            return \redirect('/')->with('log',$log);
        }

        return $next($request);
    }

}

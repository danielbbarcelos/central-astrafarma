<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CheckUserVend
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
        if(Auth::user()->vxfatvend_id == null)
        {
            $log[] = ['error' => 'O seu usuário não está vinculado a nenhum vendedor. Entre em contato com o administrador do sistema'];

            return \redirect('/dashboard')->with('log',$log);
        }

        return $next($request);

    }

}

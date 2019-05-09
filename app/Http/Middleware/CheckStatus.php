<?php

namespace App\Http\Middleware;

use App\Empresa;
use App\Medico;
use App\Atendente;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStatus
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

        if((int)Auth::user()->status == 0)
        {
            $success = false;
        }

        if(!$success)
        {
            $log[] = ['error' => 'O seu acesso foi desativado. Em caso de dúvidas, entre em contato com o administrador do seu sistema'];

            return \redirect('/')->with('log',$log);
        }

        return $next($request);
    }

}

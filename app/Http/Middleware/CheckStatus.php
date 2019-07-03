<?php

namespace App\Http\Middleware;

use App\Empresa;
use App\Medico;
use App\Atendente;
use App\Utils\Helper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

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
        $route   = substr(strtolower($request->getRequestUri()),0,4) == '/api' ? 'api' : 'web';



        if($request->header('request-ajax') !== null)
        {
            $ajax = Helper::validaRequisicaoAjax($request->header('request-ajax'));

            if(!$ajax)
            {
                return ['success' => false, 'log' => ['A requisição ajax não foi permitida']];
            }
            else
            {
                return $next($request);
            }
        }
        else
        {

            if((int)Auth::user()->status == 0)
            {
                $success = false;
            }


            if($route == 'api')
            {
                if((int)Auth::user()->mobile == 0)
                {
                    $success = false;
                }
            }
            else
            {
                if((int)Auth::user()->web == 0)
                {
                    $success = false;
                }
            }

            if(!$success)
            {
                if($route == 'api')
                {
                    return \Response::make(['error' => true, 'success' => false, 'log' => ['Sessão expirada']], 401);
                }
                else
                {
                    $log[] = ['error' => 'Acesso expirado'];

                    return \redirect('/')->with('log',$log);
                }
            }

            return $next($request);
        }

    }

}

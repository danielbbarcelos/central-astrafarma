<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Redirect;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        /*
         * Realizando o return para teste de desenvolvimento
         *
         * Caso esteja em produção, as exceptions serão tratadas
         *
         */
        if(env('APP_DD') == true)
        {
            dd($exception);
	}
	elseif(env('APP_DEBUG') == true)
	{
	    return parent::render($request, $exception);
	}

        /*
         * Erros HTTP. Exemplo: 404
         *
         */
        if ($this->isHttpException($exception))
        {
            return $this->renderHttpException($exception);
        }
        /*
         * Exceptions na API
         *
         */
        elseif($request->is('api/*') == true)
        {
            return parent::render($request, $exception);
        }



        ///Exceptions web
        $log   = [];
        $log[] = ['error' => 'Não foi possível realizar o procedimento. Tente novamente mais tarde'];

        return Redirect::back()->with('log', $log);

    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('/logout');
    }
}

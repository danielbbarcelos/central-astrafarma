<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CheckPermission
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
        /*
         * Busca permissão que tem o nome da função, para validar a permissão
         *
         */
        $function  = $request->route()->getActionMethod();

        /*
         * Verifica a partir de qual Locator foi instanciado, para consultar no arquivo de permissões correto
         *
         */
        $locator = get_class($request->route()->getController());
        $locator = str_replace('App\Http\Locators\\', '', $locator);
        list($path, $locator) = explode("\\", $locator);
        $object  = str_replace('Locator', '', $locator);

        /*
         * Se não achar a permissão aplicada ao perfil, retornará status 403
         *
         */
        $check = $this->check($function, $object, $path);

        if(!$check)
        {
            abort(403);
        }

        return $next($request);
    }


    public static function check($function, $object, $path)
    {
        $name      = "\App\Http\Permissions\\".$path."\\".$object.'Permission';
        $class     = new $name();
        $functions = $class::$functions;


        /**
         * Se a função não estiver listada no array, a permissão é negada
         *
         */
        if(!array_key_exists($function, $functions))
        {
            return false;
        }

        /**
         * Se a função estiver listada mas o controle for negativo (0) a permissão é concedida
         *
         */
        if($functions[$function]['controle'] == '0')
        {
            return true;
        }


        /**
         * Se a função estiver listada com controle positivo, mas o usuário nao possuir a permissão, o acesso é bloqueado
         *
         */
        $key = base64_encode(DB::connection()->getDatabaseName()).'#'.Auth::user()->id.'-permissions';

        if(!in_array($path."\\".$object."Locator@{$function}", Cache::get($key)))
        {
            return false;
        }


        return true;
    }

}

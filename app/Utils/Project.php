<?php

namespace App\Utils;

use Illuminate\Support\Facades\File;

class Project
{
    public static $privatedFunctions = [
        '__construct',
        'middleware',
        'getMiddleware',
        'callAction',
        '__call',
        'authorize',
        'authorizeForUser',
        'authorizeResource',
        'dispatchNow',
        'validateWith',
        'validate',
        'validateWithBag',
    ];

    public static $pathAliases = [
        'Mobile'  => 'Mobile',
        'Central' => 'Central VEX',
        'Erp'     => 'WebService ERP'
    ];


    public static function getFunctions()
    {
        $functions = [];

        try
        {
            /*
             * Busca todos os Locators da aplicação
             *
             */
            $locators = File::allFiles(base_path('app/Http/Locators'));

            foreach($locators as $locator)
            {
                $file  = "\App\Http\Locators\\". str_replace(['.php','/'],['','\\'],$locator->getRelativePathname());

                $class = new $file();

                //busca os dados do classe para encontrarmos as tags desejadas
                $reflection = new \ReflectionClass($class);

                $classDescription = self::getDocComment($reflection->getDocComment(), '@description');
                $classDescription = isset($classDescription) ? $classDescription : $locator->getRelativePathname();

                $allFunctions = get_class_methods($class);

                foreach($allFunctions as $item)
                {
                    if(!in_array($item, self::$privatedFunctions))
                    {
                        //busca os dados do método para encontrarmos as tags desejadas
                        $method = new \ReflectionMethod($class, $item);

                        $description = self::getDocComment($method->getDocComment(), '@description');
                        $info        = self::getDocComment($method->getDocComment(), '@info');

                        $functions
                            [self::$pathAliases[$locator->getRelativePath()]]
                            [$classDescription]
                            [] = [
                            'function'      => $item,
                            'description'   => isset($description) ? $description : $item,
                            'info'          => isset($info) ? $info : '',
                        ];
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            return null;
        }


        return $functions;
    }


    public static function getDocComment($comment, $tag = '')
    {
        if(!$comment)
        {
            return null;
        }

        $lines = explode("\n", $comment);

        foreach($lines as $line)
        {
            if (strpos($line, $tag) !== false)
            {
                $str = explode($tag, $line);

                return trim($str[1]);
            }
        }

        return null;
    }

}
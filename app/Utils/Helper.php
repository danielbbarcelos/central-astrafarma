<?php

namespace App\Utils;

use App\EmpresaFilial;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use JWTAuth;

class Helper
{

    //Gera log em arquivo personalizado
    public static function validaRequisicaoAjax($header)
    {
        list($token, $md5) = explode(' ',$header);

        return env('AJAX_MD5') == md5($md5);
    }


    //Gera log em arquivo personalizado
    public static function logFile($file, $message = '')
    {
        $content = '';

        $path = storage_path('logs/'.$file);

        if(File::exists($path))
        {
            $content  = file_get_contents($path);
        }

        $content .= "[".Carbon::now()->format('Y-m-d H:i:s')."] ".$message."\n\n";
        $content .= str_repeat("=",200);
        $content .= "\n\n";

        $file = fopen($path,'w+');
        fwrite($file, $content);
        fclose($file);

        try
        {
          chmod($path, 0777);  //changed to add the zero
        } 
        catch(\Exception $e)
        {
          //
        }

        return $path;
    }


    //retira os campos exclusivos da central vex, para enviar para o ERP via vexsync
    public static function formataSyncObject($object, $camposExtras = [])
    {
        unset($object->id);
        unset($object->created_at);
        unset($object->updated_at);
        unset($object->deleted_at);

        foreach($camposExtras as $extra)
        {
            unset($object->{$extra});
        }

        return $object;

    }


    //formata os valores de empresa/filial, para enviar para o ERP via vexsync como tenantId
    public static function formataTenantId($id)
    {
        $empfil = EmpresaFilial::find($id);

        $empresa = str_pad((int) $empfil->erp_id,2,'0',STR_PAD_LEFT);
        $filial  = str_pad((int) $empfil->filial_erp_id,2,'0',STR_PAD_LEFT);

        return $empresa.','.$filial;
    }


    //formata os valores de empresa/filial, para enviar para o ERP via vexsync como tenantId
    public static function converteTenantId($value)
    {
        list($erp, $filial) = explode(',',$value);

        $empfil = EmpresaFilial::where('erp_id',$erp)->where('filial_erp_id',$filial)->first();

        return $empfil;
    }


    //trata as mensagens de retorno do mobile
    public static function retornoMobile($response)
    {
        if(isset($response['log']))
        {
            $log = [];

            $error   = '';
            $info    = '';
            $success = '';
            $warning = '';

            foreach($response['log'] as $item)
            {
                foreach($item as $type => $message)
                {
                    if($$type == '')
                    {
                        $$type = $message;
                    }
                }
            }

            if($error !== '')
            {
                $log['error'] = $error;
            }

            if($info !== '')
            {
                $log['info'] = $info;
            }

            if($success !== '')
            {
                $log['success'] = $success;
            }

            if($warning !== '')
            {
                $log['warning'] = $warning;
            }

            $response['log'] = $log;
        }

        return $response;
    }



    //Converte nome de variáveis de objeto vindo do webservice
    public static function retornoERP($object)
    {
        $original = $object;

        $new = [];

        //retorno em array
        if(gettype($object) == 'array')
        {
            foreach($original as $item)
            {
                $array = [];

                try
                {
                    foreach(get_object_vars($item) as $key => $value)
                    {
                        $array[strtolower($key)] = $value;

                        //converte valor sim/nao caso seja campo status. Os valores referem-se a "bloqueado sim ou nao"
                        if(strtolower($key) == 'status' or strtolower($key) == 'contribuinte' or strtolower($key) == 'envia_boleto')
                        {
                            if(strtolower($value) == 'sim')
                            {
                                $array[strtolower($key)] = '1';
                            }
                            elseif(strtolower($value) == 'nao')
                            {
                                $array[strtolower($key)] = '0';
                            }
                        }
                    }

                    $new[] = $array;

                }
                catch(\Exception $e)
                {
                    foreach($item as $key => $value)
                    {
                        $array[strtolower($key)] = $value;

                        //converte valor sim/nao caso seja campo status. Os valores referem-se a "bloqueado sim ou nao"
                        if(strtolower($key) == 'status' or strtolower($key) == 'contribuinte' or strtolower($key) == 'envia_boleto')
                        {
                            if(strtolower($value) == 'sim')
                            {
                                $array[strtolower($key)] = '1';
                            }
                            elseif(strtolower($value) == 'nao')
                            {
                                $array[strtolower($key)] = '0';
                            }
                        }
                    }

                    $new[] = $array;

                }

            }
        }
        //retorno em json object
        else 
        {
            foreach(get_object_vars($original) as $key => $value)
            {
                $new[strtolower($key)] = $value;

                //converte valor sim/nao caso seja campo status. Os valores referem-se a "bloqueado sim ou nao"
                if(strtolower($key) == 'status' or strtolower($key) == 'contribuinte' or strtolower($key) == 'envia_boleto')
                {
                    if(strtolower($value) == 'sim')
                    {
                        $new[strtolower($key)] = '1';
                    }
                    elseif(strtolower($value) == 'nao')
                    {
                        $new[strtolower($key)] = '0';
                    }
                }
            }
        }
        
        return json_encode($new);
    }


    //conversão de jwt token authorization em usuário
    public static function JWTAuthorization($authorization)
    {
        return JWTAuth::toUser(str_replace('Bearer ','',$authorization));
    }


    //Formata bytes to kb, mb, gb, tb
    public static function formataBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }


    //Insere máscara
    public static function insereMascara($val, $mask)
    {
         $maskared = '';
         $k        = 0;

         for($i = 0; $i<=strlen($mask)-1; $i++)
         {
             if($mask[$i] == '#')
             {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
             }
             else
             {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
             }
         }

         return $maskared;
    }


    //Retira acentuação das string para envio de XML's
    public static function formataString($string)
    {
        $string = str_replace(array("Ç","ç"),array("C","c"), $string);

        $formated = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/",
            "/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/",
            "/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),($string));

        return $formated;
    }


    //Formata string apenas com numeros
    public static function formataNumeral($string)
    {
        $string = str_replace(" ","",$string);


        return preg_replace("/[^0-9]/", "", $string);
    }

    
    //Remove trecho da string " - " reservada para complemento do produto na emissão de NFe
    public static function removeTrechoReservado($string)
    {
        return str_replace([" - "], ["-"], $string);
    }


    //Remove quebra de linha
    public static function removeQuebraLinha($text)
    {
        return str_replace(["\r\n","\n","\r"], [". ",". ",". "], $text);
    }

    
    //Remove espaços - Exemplos de aplicação: chave da nota fiscal
    public static function removeEspaco($string)
    {
        return str_replace(" ", "", $string);
    }

    
    //Remove máscaras de campos como telefone, cpf, cnpj, cep para envio de XML's
    public static function removeMascara($string)
    {
        return str_replace([".","_","/","-","(",")"," ",","], ["","","","","","","",""], $string);
    }


    //Formata moeda com padronização para inserir em banco de dados, de colunas tipo "decimal"
    public static function formataMoeda($money)
    {
        $money = str_replace(" ","",$money);

        if(!is_numeric($money))
        {
            $money      = str_replace(".","",$money);
            $formated   = str_replace(",",".",$money);
        }
        else
        {
            $formated = $money;
        }
        return $formated;
    }


    //Formata moeda com padronização para inserir em banco de dados, de colunas tipo "decimal"
    public static function formataDecimal($money)
    {
        $money = str_replace(" ","",$money);

        if(!is_numeric($money))
        {
            $money      = str_replace(".","",$money);
            $formated   = str_replace(",",".",$money);
        }
        else
        {
            $formated = $money;
        }
        return $formated;
    }

}





<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ObjectDependence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'object:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate object dependences like models, controllers, locators, permissions and routes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $object = $this->argument('name');


        /*
         * Generate model
         *
         */
        $model = true;

        if(File::exists(base_path("app/$object.php")))
        {
            $this->alert('Model has been created previously');

            if(!$this->confirm('Do you really wanna continue?'))
            {
                $model = false;
            }
        }

        if(!$model)
        {
            $this->comment('Model don\'t created ');
        }
        else
        {
            $content  = "<?php \n\n";
            $content .= "namespace App; \n\n";
            $content .= "use Illuminate\Database\Eloquent\Model; \n\n";
            $content .= "class $object extends Model\n";
            $content .= "{\n\n";
            $content .= "    //The attributes that should be not changed \n";
            $content .= '    protected $primaryKey = "id";'."\n\n";
            $content .= "    //Table's name of the model \n";
            $content .= '    protected $table = "'.strtolower($object).'s";'."\n\n";
            $content .= "    //The attributes that are table's timestamps \n";
            $content .= '    public $timestamps = ["created_at", "updated_at"];'."\n\n";
            $content .= "    //The attributes that are mass assignable \n";
            $content .= '    protected $fillable = [];'."\n\n";
            $content .= "    //The attributes that should be hidden for arrays \n";
            $content .= '    protected $hidden = [];'."\n\n";
            $content .= "    //Messages to show when validation fails \n";
            $content .= '    public static $messages = [];'."\n\n";
            $content .= "}";

            $file = fopen(base_path("app/$object.php"),'w+');
            fwrite($file, $content);
            fclose($file);

            $this->info('Model created successfully');

        }


        /*
         * Generate controller
         *
         */
        $controller = true;

        if(File::exists(base_path("app/Http/Controllers/".$object."Controller.php")))
        {
            $this->alert('Controller has been created previously');

            if(!$this->confirm('Do you really wanna continue?'))
            {
                $controller = false;
            }
        }

        if(!$controller)
        {
            $this->comment('Controller don\'t created ');
        }
        else
        {
            $content  = "<?php \n\n";
            $content .= "namespace App\Http\Controllers; \n\n";
            $content .= "use App\\$object; \n";
            $content .= "use Illuminate\Http\Request; \n";
            $content .= "use Validator; \n\n";
            $content .= "class ".$object."Controller extends Controller\n";
            $content .= "{\n\n";
            $content .= "    //construct\n";
            $content .= "    public function __construct()\n    {\n         //\n    }\n\n\n";
            $content .= "    //retorna array do objeto\n";
            $content .= "    public function lista()\n    {\n        \$success = true;\n        \$log     = [];\n\n        //\n\n        \$response['success'] = \$success;\n        \$response['log']     = \$log;\n        return \$response;\n    }\n\n\n";
            $content .= "    //chamada da tela para adicionar um objeto\n";
            $content .= "    public function adiciona()\n    {\n        \$success = true;\n        \$log     = [];\n\n        $".strtolower($object)." = new $object();\n\n        \$response['success'] = \$success;\n        \$response['log']     = \$log;\n        \$response['".strtolower($object)."'] = $".strtolower($object).";\n        return \$response;\n    }\n\n\n";
            $content .= "    //post para adicionar um objeto\n";
            $content .= "    public function adicionaPost(Request \$request)\n    {\n        \$success = true;\n        \$log     = [];\n\n        \$rules = [];\n\n        \$validator = Validator::make(\$request->all(), \$rules, $object::\$messages);\n\n        if (\$validator->fails())\n        {\n            \$success = false;\n\n            foreach(\$validator->messages()->all() as \$message)\n            {\n                \$log[] = ['error' => \$message];\n            }\n        }\n\n        if (\$success)\n        {\n            //\n        }\n\n        \$response['success'] = \$success;\n        \$response['log']     = \$log;\n        return \$response;\n    }\n\n\n";
            $content .= "    //chamada da tela para editar um objeto\n";
            $content .= "    public function edita($".strtolower($object)."_id)\n    {\n        \$success = true;\n        \$log     = [];\n\n        $".strtolower($object)." = $object::find($".strtolower($object)."_id);\n\n        if(!isset($".strtolower($object)."))\n        {\n            \$success = false;\n            \$log[]   = ['error' => 'Item não encontrado'];\n        }\n\n        \$response['success'] = \$success;\n        \$response['log']     = \$log;\n        return \$response;\n    }\n\n\n";
            $content .= "    //post para editar um objeto\n";
            $content .= "    public function editaPost(Request \$request, $".strtolower($object)."_id)\n    {\n        \$success = true;\n        \$log     = [];\n\n        $".strtolower($object)." = $object::find($".strtolower($object)."_id);\n\n        if(!isset($".strtolower($object)."))\n        {\n            \$success = false;\n            \$log[]   = ['error' => 'Item não encontrado'];\n        }\n        else\n        {\n            \$rules = [];\n\n           \$validator = Validator::make(\$request->all(), \$rules, $object::\$messages);\n\n           if (\$validator->fails())\n           {\n               \$success = false;\n\n               foreach(\$validator->messages()->all() as \$message)\n               {\n                   \$log[] = ['error' => \$message];\n               }\n           }\n\n           if (\$success)\n           {\n               //\n           }\n\n        }\n\n        \$response['success'] = \$success;\n        \$response['log']     = \$log;\n        return \$response;\n    }\n\n\n";
            $content .= "    //chamada da tela para visualizar um objeto\n";
            $content .= "    public function visualiza($".strtolower($object)."_id)\n    {\n        \$success = true;\n        \$log     = [];\n\n        $".strtolower($object)." = $object::find($".strtolower($object)."_id);\n\n        if(!isset($".strtolower($object)."))\n        {\n            \$success = false;\n            \$log[]   = ['error' => 'Item não encontrado'];\n        }\n\n        \$response['success'] = \$success;\n        \$response['log']     = \$log;\n        return \$response;\n    }\n\n\n";
            $content .= "    //post para excluir um objeto\n";
            $content .= "    public function excluiPost(Request \$request, $".strtolower($object)."_id)\n    {\n        \$success = true;\n        \$log     = [];\n\n        $".strtolower($object)." = $object::find($".strtolower($object)."_id);\n\n        if(!isset($".strtolower($object)."))\n        {\n            \$success = false;\n            \$log[]   = ['error' => 'Item não encontrado'];\n        }\n        else\n        {\n            //\n        }\n\n        \$response['success'] = \$success;\n        \$response['log']     = \$log;\n        return \$response;\n    }\n\n\n";
            $content .= "}";

            $file = fopen(base_path("app/Http/Controllers/".$object."Controller.php"),'w+');
            fwrite($file, $content);
            fclose($file);

            $this->info('Controller created successfully');

        }



        /*
         * Generate locator
         *
         */
        $locator = true;

        if(File::exists(base_path("app/Http/Locators/".$object."Locator.php")))
        {
            $this->alert('Locator has been created previously');

            if(!$this->confirm('Do you really wanna continue?'))
            {
                $locator = false;
            }
        }

        if(!$locator)
        {
            $this->comment('Locator don\'t created ');
        }
        else
        {
            $content  = "<?php \n\n";
            $content .= "namespace App\Http\Locators; \n\n";
            $content .= "use Illuminate\Http\Request; \n";
            $content .= "use Illuminate\Support\Facades\Redirect; \n";
            $content .= "use App\Http\Controllers\Controller; \n";
            $content .= "use App\Http\Controllers\\".$object."Controller; \n\n";
            $content .= "class ".$object."Locator extends Controller\n";
            $content .= "{\n\n";
            $content .= "    //path's name of resources/views\n";
            $content .= "    protected \$basePathViews = 'pages.';\n\n\n";
            $content .= "    //construct\n";
            $content .= "    public function __construct()\n    {\n         \$this->middleware('permissions', [ 'except' => []]);\n    }\n\n\n";
            $content .= "    //retorna array do objeto\n";
            $content .= "    public function lista()\n    {\n        \$controller = new ".$object."Controller();\n\n        \$response   = \$controller->lista();\n\n        if(!\$response['success'])\n        {\n            return Redirect::back()->withInput()->with('log',\$response['log']);\n        }\n\n        return view(\$this->basePathViews.'', \$response);\n    }\n\n\n";
            $content .= "    //chamada da tela para adicionar um objeto\n";
            $content .= "    public function adiciona()\n    {\n        \$controller = new ".$object."Controller();\n\n        \$response   = \$controller->adiciona();\n\n        if(!\$response['success'])\n        {\n            return Redirect::back()->withInput()->with('log',\$response['log']);\n        }\n\n        return view(\$this->basePathViews.'', \$response);\n    }\n\n\n";
            $content .= "    //post para adicionar um objeto\n";
            $content .= "    public function adicionaPost(Request \$request)\n    {\n        \$controller = new ".$object."Controller();\n\n        \$response   = \$controller->adicionaPost(\$request);\n\n        if(!\$response['success'])\n        {\n            return Redirect::back()->withInput()->with('log',\$response['log']);\n        }\n\n        return \\redirect('/".strtolower($object)."s')->with('log',\$response['log']);\n    }\n\n\n";
            $content .= "    //chamada da tela para editar um objeto\n";
            $content .= "    public function edita($".strtolower($object)."_id)\n    {\n        \$controller = new ".$object."Controller();\n\n        \$response   = \$controller->edita($".strtolower($object)."_id);\n\n        if(!\$response['success'])\n        {\n            return Redirect::back()->withInput()->with('log',\$response['log']);\n        }\n\n        return view(\$this->basePathViews.'', \$response);\n    }\n\n\n";
            $content .= "    //post para editar um objeto\n";
            $content .= "    public function editaPost(Request \$request, $".strtolower($object)."_id)\n    {\n        \$controller = new ".$object."Controller();\n\n        \$response   = \$controller->editaPost(\$request, $".strtolower($object)."_id);\n\n        if(!\$response['success'])\n        {\n            return Redirect::back()->withInput()->with('log',\$response['log']);\n        }\n\n        return \\redirect('/".strtolower($object)."s')->with('log',\$response['log']);\n    }\n\n\n";
            $content .= "    //chamada da tela para visualizar um objeto\n";
            $content .= "    public function visualiza($".strtolower($object)."_id)\n    {\n        \$controller = new ".$object."Controller();\n\n        \$response   = \$controller->visualiza($".strtolower($object)."_id);\n\n        if(!\$response['success'])\n        {\n            return Redirect::back()->withInput()->with('log',\$response['log']);\n        }\n\n        return view(\$this->basePathViews.'', \$response);\n    }\n\n\n";
            $content .= "    //post para excluir um objeto\n";
            $content .= "    public function excluiPost(Request \$request, $".strtolower($object)."_id)\n    {\n        \$controller = new ".$object."Controller();\n\n        \$response   = \$controller->excluiPost(\$request, $".strtolower($object)."_id);\n\n        if(!\$response['success'])\n        {\n            return Redirect::back()->withInput()->with('log',\$response['log']);\n        }\n\n        return \\redirect('/".strtolower($object)."s')->with('log',\$response['log']);\n    }\n\n\n";
            $content .= "}";

            $file = fopen(base_path("app/Http/Locators/".$object."Locator.php"),'w+');
            fwrite($file, $content);
            fclose($file);

            $this->info('Locator created successfully');

        }

        /*
         * Generate permissions
         *
         */
        $permission = true;

        if(File::exists(base_path("app/Http/Permissions/".$object."Permission.php")))
        {
            $this->alert('Permission has been created previously');

            if(!$this->confirm('Do you really wanna continue?'))
            {
                $permission = false;
            }
        }

        if(!$permission)
        {
            $this->comment('Permission don\'t created ');
        }
        else
        {
            $content  = "<?php \n\n";
            $content .= "namespace App\Http\Permissions; \n\n";
            $content .= "class ".$object."Permission \n";
            $content .= "{\n\n";
            $content .= "    /**\n";
            $content .= "     * @var array\n";
            $content .= "     * \n";
            $content .= "     * Array que contém as descrições, famílias e controle de permissões das funções do Locator,\n";
            $content .= "     * \n";
            $content .= "     * O array é lido toda vez que as permissões forem atualizadas no banco de dados,\n";
            $content .= "     * \n";
            $content .= "     * Para atualizar as funções disponíveis na tela de cadastro de perfil\n";
            $content .= "     * \n";
            $content .= "     */ \n";
            $content .= "    public static \$functions = [\n\n";
            $content .= "       'lista'         => [ 'controle' => '1', 'titulo' => 'Cadastro de ".$object."', 'descricao' => 'Listar ".$object."'],\n";
            $content .= "       'adiciona'      => [ 'controle' => '0', 'titulo' => 'Cadastro de ".$object."', 'descricao' => 'Tela de cadastro de ".$object."'],\n";
            $content .= "       'adicionaPost'  => [ 'controle' => '1', 'titulo' => 'Cadastro de ".$object."', 'descricao' => 'Adicionar ".$object."'],\n";
            $content .= "       'edita'         => [ 'controle' => '0', 'titulo' => 'Cadastro de ".$object."', 'descricao' => 'Tela de edição de ".$object."'],\n";
            $content .= "       'editaPost'     => [ 'controle' => '1', 'titulo' => 'Cadastro de ".$object."', 'descricao' => 'Editar ".$object."'],\n";
            $content .= "       'visualiza'     => [ 'controle' => '1', 'titulo' => 'Cadastro de ".$object."', 'descricao' => 'Visualizar ".$object."'],\n";
            $content .= "       'excluiPost'    => [ 'controle' => '1', 'titulo' => 'Cadastro de ".$object."', 'descricao' => 'Excluir ".$object."'],\n";
            $content .= "    ];\n\n";
            $content .= "}";

            $file = fopen(base_path("app/Http/Permissions/".$object."Permission.php"),'w+');
            fwrite($file, $content);
            fclose($file);

            $this->info('Permission created successfully');

        }


        /*
         * Generate routes
         *
         */
        $route = true;

        if(!$this->confirm('Do you really wanna create routes too?', true))
        {
            $route = false;
        }

        if(!$route)
        {
            $this->comment('Route don\'t created ');
        }
        else
        {
            $route = fopen(base_path('routes/web.php'), "a");

            $content  = "Route::get ('/".strtolower($object)."s',                          '".$object."Locator@lista');\n";
            $content .= "Route::get ('/".strtolower($object)."s/add',                      '".$object."Locator@adiciona');\n";
            $content .= "Route::post('/".strtolower($object)."s/add',                      '".$object."Locator@adicionaPost');\n";
            $content .= "Route::get ('/".strtolower($object)."s/{".strtolower($object)."_id}/edit',        '".$object."Locator@edita');\n";
            $content .= "Route::post('/".strtolower($object)."s/{".strtolower($object)."_id}/edit',        '".$object."Locator@editaPost');\n";
            $content .= "Route::get ('/".strtolower($object)."s/{".strtolower($object)."_id}/show',        '".$object."Locator@visualiza');\n";
            $content .= "Route::post('/".strtolower($object)."s/{".strtolower($object)."_id}/del',         '".$object."Locator@excluiPost');\n";

            fwrite($route, "\n\n". $content);
            fclose($route);

            $this->info('Route created successfully');


        }

    }

}

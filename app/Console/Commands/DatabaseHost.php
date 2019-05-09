<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatabaseHost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:host {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create virtual host to specific database from your url';

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
    public function handle($host, $hostIP, $directory)
    {
        /*
         * Adiciona IP no /etc/hosts - necessário que o arquivo esteja com permissão 777
         *
         */
        $etcHost = file_put_contents('/etc/hosts', $hostIP.PHP_EOL , FILE_APPEND | LOCK_EX);

        /*
         * Adiciona virtual host no apache - necessário que os diretórios estejam com permissão 777
         *
         */
        $a2path = "/etc/apache2/sites-available/{$host}.conf";

        if(!File::exists($a2path))
        {
            $virtualHost = '
            <VirtualHost *:80>
                ServerName '.$host.'
                DocumentRoot "'.$directory.'/public"
                <Directory "'.$directory.'/public">
                    Options Indexes FollowSymLinks IncludesNoExec
                    AllowOverride All
                    Require all granted
                </Directory>
                ErrorLog "'.$directory.'/storage/logs/'.$host.'.log"
            </VirtualHost>
            ';

            $vhConf = fopen($a2path,'w+');
            fwrite($vhConf, $virtualHost);
            fclose($vhConf);

            shell_exec("ln -s /etc/apache2/sites-available/$host.conf /etc/apache2/sites-enabled/$host.conf");
            shell_exec('sudo service apache2 reload');
        }


    }
}

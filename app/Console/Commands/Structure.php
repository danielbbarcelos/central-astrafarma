<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Structure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structure:create {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate structure of directories and files';

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
        $this->comment('Generating structure...');


        /*
         * Views generation
         *
         */
        if($this->argument('type') == 'views')
        {
            $generate = true;

            /*
             * Check if is existing
             *
             */
            if(File::exists(base_path('resources/views/pages')))
            {
                /*
                 * Generate alert and confirm the new generation
                 *
                 */
                $this->alert('Structure has been created previously');

                if(!$this->confirm('Do you really wanna continue?'))
                {
                    $this->line('Generate canceled');

                    $generate = false;
                }
                else
                {
                    File::deleteDirectory(base_path('resources/views/emails'));
                    File::deleteDirectory(base_path('resources/views/errors'));
                    File::deleteDirectory(base_path('resources/views/layouts'));
                    File::deleteDirectory(base_path('resources/views/pages'));

                }
            }


            if($generate)
            {
                File::makeDirectory(base_path('resources/views/emails'),  0755, true);
                File::makeDirectory(base_path('resources/views/errors'),  0755, true);
                File::makeDirectory(base_path('resources/views/layouts'), 0755, true);
                File::makeDirectory(base_path('resources/views/pages'),   0755, true);

                $this->info('Structure to views created successfully');
            }
        }

        /*
         * Permissions generate
         *
         */
        elseif($this->argument('type') == 'permissions')
        {
            $generate = true;

            /*
             * Check if is existing
             *
             */
            if(File::exists(base_path('app/Http/Permissions')))
            {
                /*
                 * Generate alert and confirm the new generation
                 *
                 */
                $this->alert('Structure has been created previously');

                if(!$this->confirm('Do you really wanna continue?'))
                {
                    $this->line('Generate canceled');

                    $generate = false;
                }
                else
                {
                    File::deleteDirectory(base_path('app/Http/Permissions'));

                }
            }


            if($generate)
            {
                File::makeDirectory(base_path('app/Http/Permissions'), 0755, true);

                $this->info('Structure to permissions created successfully');
            }

        }

        else
        {
            $this->error('Structure type is not accepted');
        }
    }
}

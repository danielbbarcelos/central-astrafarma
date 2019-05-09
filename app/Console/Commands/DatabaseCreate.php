<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database';

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
        $schema = "vex_{$this->argument('database')}_db";

        try
        {
            DB::statement("CREATE DATABASE {$schema}");

            $log = 'Database has been created with success';
        }
        catch(\Exception $exception)
        {
            $log = 'Database not created';
        }

        $this->info($log);
    }
}

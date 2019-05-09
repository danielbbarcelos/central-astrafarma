<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DatabaseMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate tables to specific database';

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
    public function handle($connection)
    {
        Artisan::call('migrate', ['--database' => $connection, '--path' =>  '/database/migrations/db-clients']);

    }
}

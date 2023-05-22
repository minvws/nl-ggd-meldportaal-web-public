<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Database\Events\SchemaLoaded;
use Illuminate\Support\Facades\Artisan;

class CreateMigrationsTableAfterSchemaLoadedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SchemaLoaded $event
     * @return void
     */
    public function handle(SchemaLoaded $event): void
    {
        // If the migrations table not exists then create the migrations table
        $migrator = app('migration.repository');
        if (! $migrator->repositoryExists()) {
            Artisan::call('migrate:install');
        }
    }
}

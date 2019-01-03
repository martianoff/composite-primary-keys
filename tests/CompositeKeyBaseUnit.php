<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Database\Events\QueryExecuted;
use Orchestra\Testbench\TestCase;

class CompositeKeyBaseUnit extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->seed(\TestOrganizationSeeder::class);
        $this->seed(\TestUserSeeder::class);
        $this->seed(\TestUserNonCompositeSeeder::class);
        $this->seed(\TestBinaryUserSeeder::class);

        if(env('DEBUG_QUERY_LOG', true)) {
            \DB::listen(
                function (QueryExecuted $queryExecuted) {
                    var_dump($queryExecuted->sql);
                    var_dump($queryExecuted->bindings);
                }
            );
        }
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function loadMigrationsFrom($paths)
    {
        $paths = (is_array($paths)) ? $paths : [$paths];
        $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }
}

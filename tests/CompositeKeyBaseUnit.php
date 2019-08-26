<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use DB;
use Exception;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use TestBinaryRoleSeeder;
use TestBinaryUserSeeder;
use TestOrganizationSeeder;
use TestRoleSeeder;
use TestUserNonCompositeSeeder;
use TestUserSeeder;

class CompositeKeyBaseUnit extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @throws Exception
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->seed(TestOrganizationSeeder::class);
        $this->seed(TestRoleSeeder::class);
        $this->seed(TestBinaryRoleSeeder::class);
        $this->seed(TestUserSeeder::class);
        $this->seed(TestUserNonCompositeSeeder::class);
        $this->seed(TestBinaryUserSeeder::class);

        if (env('DEBUG_QUERY_LOG', true)) {
            DB::listen(
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
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function loadMigrationsFrom($paths): void
    {
        $paths = (is_array($paths)) ? $paths : [$paths];
        $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }
}

<?php

use Illuminate\Database\Seeder;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestRole;

class TestRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        TestRole::create([
            'name' => 'Foo',
        ]);
    }
}

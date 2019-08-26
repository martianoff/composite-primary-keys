<?php

use Illuminate\Database\Seeder;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryRole;

class TestBinaryRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        TestBinaryRole::create([
            'role_id' => md5(1, true),
            'name' => 'Foo',
        ]);
    }
}

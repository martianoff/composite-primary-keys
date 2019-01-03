<?php

use Illuminate\Database\Seeder;

class TestBinaryUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryUser::create([
            'user_id'         => md5(20000, true),
            'organization_id' => \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::whereName('Foo')
                ->first()
                ->organization_id,
            'name' => 'Foo',
        ]);
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryUser::create([
            'user_id'         => md5(20001, true),
            'organization_id' => \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::whereName('Bar')
                ->first()
                ->organization_id,
            'name' => 'Bar',
        ]);
    }
}

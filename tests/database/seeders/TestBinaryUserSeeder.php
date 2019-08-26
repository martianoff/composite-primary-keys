<?php

use Illuminate\Database\Seeder;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryRole;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryUser;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryUserHex;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestRole;

class TestBinaryUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        TestBinaryUser::create([
            'user_id'         => md5(20000, true),
            'organization_id' => TestOrganization::whereName('Foo')
                ->first()
                ->organization_id,
            'name'    => 'Foo',
            'role_id' => TestRole::first()->getKey(),
        ]);
        TestBinaryUser::create([
            'user_id'         => md5(20001, true),
            'organization_id' => TestOrganization::whereName('Bar')
                ->first()
                ->organization_id,
            'name'           => 'Bar',
            'role_id'        => TestRole::first()->getKey(),
            'binary_role_id' => TestBinaryRole::first()->getKey(),
        ]);
        TestBinaryUserHex::create([
            'user_id'         => bin2hex(md5(20002, true)),
            'organization_id' => TestOrganization::whereName('Bar')
                ->first()
                ->organization_id,
            'name'           => 'Hex',
            'role_id'        => TestRole::first()->getKey(),
            'binary_role_id' => TestBinaryRole::first()->getKey(),
        ]);
    }
}

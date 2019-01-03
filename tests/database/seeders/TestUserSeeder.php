<?php

use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser::create([
            'user_id' => 1,
            'organization_id' => \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::whereName('Foo')
                ->first()
                ->organization_id,
            'name' => 'Foo',
        ]);
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser::create([
            'user_id' => 1,
            'organization_id' => \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::whereName('Bar')
                ->first()
                ->organization_id,
            'name' => 'Bar',
        ]);
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser::create([
            'user_id' => 2,
            'organization_id' => \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::whereName('Bar')
                ->first()
                ->organization_id,
            'name' => 'Foo Bar',
        ]);
    }
}

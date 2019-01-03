<?php

use Illuminate\Database\Seeder;

class TestOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::create([
            'organization_id' => 100,
            'name' => 'Foo',
        ]);
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::create([
            'organization_id' => 101,
            'name' => 'Bar',
        ]);
    }
}

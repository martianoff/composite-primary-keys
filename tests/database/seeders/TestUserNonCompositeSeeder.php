<?php

use Illuminate\Database\Seeder;

class TestUserNonCompositeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUserNonComposite::create([
            'user_id'         => 1,
            'organization_id' => \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::whereName('Foo')
                ->first()
                ->organization_id,
            'name' => 'Foo',
        ]);
        \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUserNonComposite::create([
            'user_id'         => 2,
            'organization_id' => \MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization::whereName('Foo')
                ->first()
                ->organization_id,
            'name' => 'Bar',
        ]);
    }
}

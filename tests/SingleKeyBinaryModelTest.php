<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryRole;

class SingleKeyBinaryModelTest extends CompositeKeyBaseUnit
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    /** @test */
    public function automaticBinaryKeysOnCreation()
    {
        /**
         * @var TestBinaryRole
         */
        $model = TestBinaryRole::create(['name' => 'Zoo']);
        $this->assertNotNull($model->role_id);
        $this->assertInstanceOf(TestBinaryRole::class, $model);

        return $model;
    }

    /** @test */
    public function validateSingleModelLookup()
    {
        /**
         * @var TestBinaryRole
         */
        $model = TestBinaryRole::find(md5(1, true));
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestBinaryRole::class, $model);

        return $model;
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelLookupModel(TestBinaryRole $model)
    {
        $this->assertEquals('Foo', $model->name);
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelUpdate(TestBinaryRole $model)
    {
        $model->update([
            'name' => 'FooBar',
        ]);
        $model->refresh();
        $this->assertEquals('FooBar', $model->name);
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateEagerRelations(TestBinaryRole $model)
    {
        $model->loadMissing(['users', 'hex_users']);
        $this->assertNotNull($model->toArray()['users']);
        $this->assertNotNull($model->toArray()['hex_users']);
        $this->assertNotNull($model->users);
        $this->assertNotNull($model->hex_users);
    }

    /** @test
     */
    public function validateLazyEagerRelations()
    {
        $model = TestBinaryRole::find(md5(1, true));
        $this->assertNotNull($model->users);
        $this->assertNotNull($model->hex_users);
    }
}

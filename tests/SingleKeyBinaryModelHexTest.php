<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryRoleHex;

class SingleKeyBinaryModelHexTest extends CompositeKeyBaseUnit
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    /** @test */
    public function automaticBinaryKeysOnCreation()
    {
        /**
         * @var TestBinaryRoleHex
         */
        $model = TestBinaryRoleHex::create(['name' => 'Zoo']);
        $this->assertNotNull($model->role_id);
        $this->assertInstanceOf(TestBinaryRoleHex::class, $model);

        return $model;
    }

    /** @test */
    public function validateSingleModelLookup()
    {
        /**
         * @var TestBinaryRoleHex
         */
        $model = TestBinaryRoleHex::find(md5(1));
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestBinaryRoleHex::class, $model);

        return $model;
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelLookupModel(TestBinaryRoleHex $model)
    {
        $this->assertEquals('Foo', $model->name);
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelUpdate(TestBinaryRoleHex $model)
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
    public function validateEagerRelations(TestBinaryRoleHex $model)
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
        $model = TestBinaryRoleHex::find(md5(1));
        $this->assertNotNull($model->users);
        $this->assertNotNull($model->hex_users);
    }
}

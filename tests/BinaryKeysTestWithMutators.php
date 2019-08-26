<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Middleware\SubstituteBindings;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryUserHex;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestRole;

class BinaryKeysTestWithMutators extends CompositeKeyBaseUnit
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['router']->get('binary-users/{binaryUser}', function (TestBinaryUserHex $binaryUser) {
            return $binaryUser->toJson();
        })->middleware(SubstituteBindings::class);
    }

    /** @test */
    public function validateHexInputModelLookup()
    {
        /**
         * @var TestBinaryUserHex
         */
        $model = TestBinaryUserHex::find([
            'user_id'         => md5(20002),
            'organization_id' => 101,
        ]);
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestBinaryUserHex::class, $model);

        return $model;
    }

    /** @test */
    public function validateSingleModelLookup()
    {
        /**
         * @var TestBinaryUserHex
         */
        $model = TestBinaryUserHex::find([
            'user_id'         => md5(20000),
            'organization_id' => 100,
        ]);
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestBinaryUserHex::class, $model);

        return $model;
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelLookupModel(TestBinaryUserHex $model)
    {
        $this->assertEquals(strtoupper(md5(20000)), $model->user_id);
        $this->assertEquals(100, $model->organization_id);
        $this->assertEquals('Foo', $model->name);
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelUpdate(TestBinaryUserHex $model)
    {
        $model->update([
            'name' => 'FooBar',
        ]);
        $model->refresh();
        $this->assertEquals('FooBar', $model->name);
    }

    /** @test */
    public function validateMultipleModelLookup()
    {
        /**
         * @var Collection|TestBinaryUserHex[]
         */
        $models = TestBinaryUserHex::find([[
            'user_id'         => md5(20000),
            'organization_id' => 100,
        ], [
            'user_id'         => md5(20001),
            'organization_id' => 101,
        ]]);
        $this->assertNotNull($models);
        $this->assertInstanceOf(Collection::class, $models);

        return $models;
    }

    /** @test
     *  @depends  validateMultipleModelLookup
     */
    public function validateMultipleModelLookupModels(Collection $models)
    {
        $this->assertEquals(strtoupper(md5(20000)), $models->get(0)->user_id);
        $this->assertEquals(100, $models->get(0)->organization_id);
        $this->assertEquals('Foo', $models->get(0)->name);
        $this->assertEquals(strtoupper(md5(20001)), $models->get(1)->user_id);
        $this->assertEquals(101, $models->get(1)->organization_id);
        $this->assertEquals('Bar', $models->get(1)->name);
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateBinaryValueRendering(TestBinaryUserHex $model)
    {
        $this->assertContains(strtoupper(md5(20000)), $model->toJson());
    }

    /** @test
     */
    public function validateMissingBinaryModelRouteBinding()
    {
        $data = $this->call('GET', 'binary-users/FF___1');
        $this->assertEquals(404, $data->getStatusCode());
    }

    /** @test
     */
    public function validateWrongBinaryKeyModelRouteBinding()
    {
        $data = $this->call('GET', 'binary-users/foo');
        /*
         * will fire WrongKeyException
         */
        $this->assertEquals(500, $data->getStatusCode());
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateBinaryModelRouteBinding(TestBinaryUserHex $model)
    {
        $model->refresh();
        $data = $this->call('GET', 'binary-users/'.$model->getKey());
        $this->assertEquals(200, $data->getStatusCode());
        $this->assertEquals($model->toJson(), $data->getContent());
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateEagerRelations(TestBinaryUserHex $model)
    {
        $model->loadMissing(['role']);
        $this->assertNotNull($model->toArray()['role']);
        $this->assertNotNull($model->role);
    }

    /** @test
     */
    public function validateLazyEagerRelations()
    {
        $model = TestBinaryUserHex::find([
            'user_id'         => md5(20000),
            'organization_id' => 100,
        ]);
        $this->assertNotNull($model->role);
    }

    /** @test
     */
    public function validateReverseEagerRelations()
    {
        $role = TestRole::with('users')->first();
        $this->assertNotNull($role->toArray()['users']);
        $this->assertNotNull($role->users);
    }

    /** @test
     */
    public function validateReverseLazyEagerRelations()
    {
        $role = TestRole::first();
        $this->assertNotNull($role->users);
    }
}

<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Middleware\SubstituteBindings;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryUser;

class BinaryKeysTest extends CompositeKeyBaseUnit
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['router']->get('binary-users/{binaryUser}', function (TestBinaryUser $binaryUser) {
            return $binaryUser->toJson();
        })->middleware(SubstituteBindings::class);
    }

    /** @test */
    public function validateSingleModelLookup()
    {
        /**
         * @var TestBinaryUser
         */
        $model = TestBinaryUser::find([
            'user_id' => md5(20000, true),
            'organization_id' => 100,
        ]);
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestBinaryUser::class, $model);

        return $model;
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelLookupModel(TestBinaryUser $model)
    {
        $this->assertEquals(md5(20000, true), $model->user_id);
        $this->assertEquals(100, $model->organization_id);
        $this->assertEquals('Foo', $model->name);
    }

    /** @test */
    public function validateMultipleModelLookup()
    {
        /**
         * @var Collection|TestBinaryUser[]
         */
        $models = TestBinaryUser::find([[
            'user_id' => md5(20000, true),
            'organization_id' => 100,
        ], [
            'user_id' => md5(20001, true),
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
        $this->assertEquals(md5(20000, true), $models->get(0)->user_id);
        $this->assertEquals(100, $models->get(0)->organization_id);
        $this->assertEquals('Foo', $models->get(0)->name);
        $this->assertEquals(md5(20001, true), $models->get(1)->user_id);
        $this->assertEquals(101, $models->get(1)->organization_id);
        $this->assertEquals('Bar', $models->get(1)->name);
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateBinaryValueRendering(TestBinaryUser $model)
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
    public function validateBinaryModelRouteBinding(TestBinaryUser $model)
    {
        $data = $this->call('GET', 'binary-users/'.$model->getKey());
        $this->assertEquals(200, $data->getStatusCode());
        $this->assertEquals($model->toJson(), $data->getContent());
    }
}

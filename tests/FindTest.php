<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Database\Eloquent\Collection;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser;

class FindTest extends CompositeKeyBaseUnit
{
    /** @test */
    public function validateSingleModelLookup()
    {
        /**
         * @var TestUser
         */
        $model = TestUser::find([
            'user_id'         => 1,
            'organization_id' => 100,
        ]);
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestUser::class, $model);

        return $model;
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelLookupModel(TestUser $model)
    {
        $this->assertEquals(1, $model->user_id);
        $this->assertEquals(100, $model->organization_id);
        $this->assertEquals('Foo', $model->name);
    }

    /** @test */
    public function validateMultipleModelLookup()
    {
        /**
         * @var Collection|TestUser[]
         */
        $models = TestUser::find([[
            'user_id'         => 1,
            'organization_id' => 100,
        ], [
            'user_id'         => 1,
            'organization_id' => 101,
        ], [
            'user_id'         => 2,
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
        $this->assertEquals(1, $models->get(0)->user_id);
        $this->assertEquals(100, $models->get(0)->organization_id);
        $this->assertEquals('Foo', $models->get(0)->name);
        $this->assertEquals(1, $models->get(1)->user_id);
        $this->assertEquals(101, $models->get(1)->organization_id);
        $this->assertEquals('Bar', $models->get(1)->name);
        $this->assertEquals(2, $models->get(2)->user_id);
        $this->assertEquals(101, $models->get(2)->organization_id);
        $this->assertEquals('Foo Bar', $models->get(2)->name);
    }

    /** @test */
    public function validateMultipleModelLookupWithFindMany()
    {
        /**
         * @var Collection|TestUser[]
         */
        $models = TestUser::findMany([[
            'user_id'         => 1,
            'organization_id' => 100,
        ], [
            'user_id'         => 1,
            'organization_id' => 101,
        ], [
            'user_id'         => 2,
            'organization_id' => 101,
        ]]);
        $this->assertNotNull($models);
        $this->assertInstanceOf(Collection::class, $models);

        return $models;
    }

    /** @test
     *  @depends  validateMultipleModelLookupWithFindMany
     */
    public function validateMultipleModelLookupWithFindManyModels(Collection $models)
    {
        $this->assertEquals(1, $models->get(0)->user_id);
        $this->assertEquals(100, $models->get(0)->organization_id);
        $this->assertEquals('Foo', $models->get(0)->name);
        $this->assertEquals(1, $models->get(1)->user_id);
        $this->assertEquals(101, $models->get(1)->organization_id);
        $this->assertEquals('Bar', $models->get(1)->name);
        $this->assertEquals(2, $models->get(2)->user_id);
        $this->assertEquals(101, $models->get(2)->organization_id);
        $this->assertEquals('Foo Bar', $models->get(2)->name);
    }
}

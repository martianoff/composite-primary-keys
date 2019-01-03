<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser;

class OtherQueriesTest extends CompositeKeyBaseUnit
{
    /** @test */
    public function validateKeyExclusion()
    {
        /**
         * @var TestUser
         */
        $model = TestUser::whereKeyNot([
            'user_id'         => 1,
            'organization_id' => 100,
        ])
            ->where('user_id',1)
            ->first();
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
        $this->assertEquals(101, $model->organization_id);
        $this->assertEquals('Bar', $model->name);
    }

    /**
     * @test
     */
    public function validateDeleteModel()
    {
        /**
         * @var TestUser
         */
        TestUser::find([
            'user_id'         => 1,
            'organization_id' => 100,
        ])->delete();

        $this->assertNull(TestUser::find([
            'user_id'         => 1,
            'organization_id' => 100,
        ]));
    }

    /**
     * @test
     */
    public function validateDeleteWithoutFetching()
    {
        /**
         * @var TestUser
         */
        TestUser::whereKey([
            'user_id'         => 1,
            'organization_id' => 100,
        ])->delete();

        $this->assertNull(TestUser::find([
            'user_id'         => 1,
            'organization_id' => 100,
        ]));
    }

}

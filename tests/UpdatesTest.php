<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser;

class UpdatesTest extends CompositeKeyBaseUnit
{
    /** @test */
    public function validateEmptyCounter()
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
        $this->assertEquals(0, $model->counter);

        return $model;
    }

    /** @test
     *  @depends validateEmptyCounter
     */
    public function incrementingTest(TestUser $model)
    {
        $model->increment('counter');
        $model->refresh();
        $this->assertEquals(1, $model->counter);

        return $model;
    }

    /** @test
     *  @depends validateEmptyCounter
     */
    public function decrementingTest(TestUser $model)
    {
        $model->decrement('counter');
        $model->refresh();
        $this->assertEquals(-1, $model->counter);
    }

    /** @test
     *  @depends validateEmptyCounter
     */
    public function updateTest(TestUser $model)
    {
        $model->update([
            'counter' => 9999,
        ]);
        $model->refresh();
        $this->assertEquals(9999, $model->counter);
    }

    /** @test
     *  @depends validateEmptyCounter
     */
    public function saveTest(TestUser $model)
    {
        $this->assertTrue($model->exists);
        $model->counter = 6666;
        $model->save();
        $model->refresh();
        $this->assertEquals(6666, $model->counter);
    }
}

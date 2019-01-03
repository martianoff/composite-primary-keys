<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Foundation\Bus\DispatchesJobs;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestJob;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser;

class ModelSerializationTest extends CompositeKeyBaseUnit
{
    use DispatchesJobs;

    /** @test */
    public function testModelSerialization()
    {
        $model = TestUser::find([
            'user_id' => 1,
            'organization_id' => 100,
        ]);
        $this->assertEquals(0, $model->counter);
        $job = new TestJob($model);
        $this->dispatch($job);
        $model->refresh();
        $this->assertEquals(3333, $model->counter);
    }
}

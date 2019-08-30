<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Foundation\Bus\DispatchesJobs;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryRoleHex;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestSingleBinaryKeyJob;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser;

class SingleKeyModelSerializationTest extends CompositeKeyBaseUnit
{
    use DispatchesJobs;

    /** @test */
    public function testModelSerialization()
    {
        /**
         * @var TestUser $model
         */
        $model = TestBinaryRoleHex::first();
        $this->assertEquals('Foo', $model->name);
        $job = new TestSingleBinaryKeyJob($model);
        $this->dispatch($job);
        $model->refresh();
        $this->assertEquals('Bar', $model->name);
    }
}

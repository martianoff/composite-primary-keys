<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryUser;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestBinaryUserHex;

class ToJsonTest extends CompositeKeyBaseUnit
{
    /** @test */
    public function validateJsonWithoutHexing()
    {
        $userId = md5(20000, true);
        /**
         * @var $model TestBinaryUser
         */
        $model = TestBinaryUser::find([
            'user_id'         => $userId,
            'organization_id' => 100,
        ]);
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestBinaryUser::class, $model);
        $json = $model->toJson();
        $array = json_decode($json, true);
        $this->assertEquals(bin2hex($userId), $array['user_id']);
    }

    /** @test */
    public function validateJsonWithHexing()
    {
        $userId = md5(20000);
        /**
         * @var $model TestBinaryUserHex
         */
        $model = TestBinaryUserHex::find([
            'user_id'         => $userId,
            'organization_id' => 100,
        ]);
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestBinaryUserHex::class, $model);
        $json = $model->toJson();
        $array = json_decode($json, true);
        $this->assertEquals(strtoupper($userId), $array['user_id']);
    }
}

<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Database\Eloquent\Collection;
use MaksimM\CompositePrimaryKeys\Exceptions\MissingPrimaryKeyValueException;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser;

class MissingKeysTest extends CompositeKeyBaseUnit
{
    /** @test */
    public function validateSingleModelLookup()
    {
        try {
            /**
             * @var TestUser
             */
            $model = TestUser::find(
                [
                    'user_id' => 1,
                ]
            );
        } catch (MissingPrimaryKeyValueException $missingPrimaryKeyValueException) {
            $this->assertEquals('organization_id', $missingPrimaryKeyValueException->getMissedValuePrimaryKey());
        }
        $this->assertFalse($this->doesNotPerformAssertions());
    }

    /** @test */
    public function validateMultipleModelLookup()
    {
        try {
            /**
             * @var Collection|TestUser[]
             */
            $models = TestUser::find([[
                'organization_id' => 100,
            ], [
                'user_id'         => 2,
                'organization_id' => 100,
            ]]);
        } catch (MissingPrimaryKeyValueException $missingPrimaryKeyValueException) {
            $this->assertEquals('user_id', $missingPrimaryKeyValueException->getMissedValuePrimaryKey());
        }
        $this->assertFalse($this->doesNotPerformAssertions());
    }
}

<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use MaksimM\CompositePrimaryKeys\Exceptions\WrongRelationConfigurationException;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser;

class CompositeToCompositeRelationTest extends CompositeKeyBaseUnit
{
    /** @test */
    public function validateMissingBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 1,
            'organization_id' => 101,
        ]);

        $referrer_user = $user->referrer()->first();

        $this->assertNull($referrer_user);
    }

    /** @test */
    public function validateBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        $referrer_user = $user->referrer()->first();

        $this->assertNotNull($referrer_user);

        $this->assertInstanceOf(TestUser::class, $referrer_user);
    }

    /** @test */
    public function validateImproperBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        try {
            $user->wrongConfiguredReferrer()->first();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(WrongRelationConfigurationException::class, $exception);
        }
    }

    /** @test */
    public function validateAutomaticBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        $referrer = $user->automaticReferrer()->first();
        $this->assertNull($referrer);
    }

    /** @test */
    public function validateEagerBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        $referrer_user = $user->referrer;

        $this->assertNotNull($referrer_user);

        $this->assertInstanceOf(TestUser::class, $referrer_user);
    }

    /** @test */
    public function validateEagerWithBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::with(['referrer'])->find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        $referrer_user = $user->referrer;

        $this->assertNotNull($referrer_user);

        $this->assertInstanceOf(TestUser::class, $referrer_user);
    }

    /** @test */
    public function validateEagerLoadBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        $user->load('referrer');

        $referrer_user = $user->referrer;

        $this->assertNotNull($referrer_user);

        $this->assertInstanceOf(TestUser::class, $referrer_user);
    }

    /** @test */
    public function validateUpdateBelongsToRRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        $user->referrer->update([
            'counter' => 3333,
        ]);

        $this->assertEquals(3333, $user->referrer->counter);
    }

    /** @test */
    public function validateQuickUpdateBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        $user->referrer()->update([
            'counter' => 3333,
        ]);

        $this->assertEquals(3333, $user->referrer()->first()->counter);
    }
}

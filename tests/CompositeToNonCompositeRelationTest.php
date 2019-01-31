<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Database\Eloquent\Collection;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestOrganization;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUser;

class CompositeToNonCompositeRelationTest extends CompositeKeyBaseUnit
{
    /** @test */
    public function validateBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::find([
            'user_id'         => 1,
            'organization_id' => 101,
        ]);

        $organization = $user->organization()->first();

        $this->assertNotNull($organization);
        $this->assertInstanceOf(TestOrganization::class, $organization);

        return $organization;
    }

    /** @test
     *  @depends  validateBelongsToRelation
     */
    public function validateHasManyRelation(TestOrganization $organization)
    {
        $users = $organization->users()->get();
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertEquals(2, $users->count());
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

        $referrer_user = $user->organization;

        $this->assertNotNull($referrer_user);

        $this->assertInstanceOf(TestOrganization::class, $referrer_user);
    }

    /** @test */
    public function validateEagerWithBelongsToRelation()
    {
        /**
         * @var TestUser
         */
        $user = TestUser::with(['organization'])->find([
            'user_id'         => 2,
            'organization_id' => 101,
        ]);

        $referrer_user = $user->organization;

        $this->assertNotNull($referrer_user);

        $this->assertInstanceOf(TestOrganization::class, $referrer_user);
    }

}

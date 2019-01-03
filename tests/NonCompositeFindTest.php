<?php

namespace MaksimM\CompositePrimaryKeys\Tests;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Middleware\SubstituteBindings;
use MaksimM\CompositePrimaryKeys\Tests\Stubs\TestUserNonComposite;

class NonCompositeFindTest extends CompositeKeyBaseUnit
{

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['router']->get('non-composite-users/{testUserNonComposite}', function (TestUserNonComposite $testUserNonComposite) {
            return $testUserNonComposite->toJson();
        })->middleware(SubstituteBindings::class);
    }

    /** @test */
    public function validateSingleModelLookup()
    {
        /**
         * @var TestUserNonComposite
         */
        $model = TestUserNonComposite::find(1);
        $this->assertNotNull($model);
        $this->assertInstanceOf(TestUserNonComposite::class, $model);

        return $model;
    }

    /** @test
     *  @depends  validateSingleModelLookup
     */
    public function validateSingleModelLookupModel(TestUserNonComposite $model)
    {
        $this->assertEquals(1, $model->user_id);
        $this->assertEquals(100, $model->organization_id);
        $this->assertEquals('Foo', $model->name);
    }

    /** @test */
    public function validateMultipleModelLookup()
    {
        /**
         * @var Collection|TestUserNonComposite[]
         */
        $models = TestUserNonComposite::find([1, 2]);
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
        $this->assertEquals(2, $models->get(1)->user_id);
        $this->assertEquals(100, $models->get(1)->organization_id);
        $this->assertEquals('Bar', $models->get(1)->name);
    }

    /** @test */
    public function validateMultipleModelLookupWithFindMany()
    {
        /**
         * @var Collection|TestUserNonComposite[]
         */
        $models = TestUserNonComposite::findMany([1, 2]);
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
        $this->assertEquals(2, $models->get(1)->user_id);
        $this->assertEquals(100, $models->get(1)->organization_id);
        $this->assertEquals('Bar', $models->get(1)->name);
    }

    /** @test
     */
    public function validateMissingNonCompositeModelRouteBinding()
    {
        $data = $this->call('GET', 'non-composite-users/99');
        $this->assertEquals(404, $data->getStatusCode());
    }

    /** @test
     * @depends  validateSingleModelLookup
     */
    public function validateNonCompositeModelRouteBinding(TestUserNonComposite $model)
    {
        $data = $this->call('GET', 'non-composite-users/1');
        $this->assertEquals(200, $data->getStatusCode());
        $this->assertEquals($model->toJson(), $data->getContent());
    }
}

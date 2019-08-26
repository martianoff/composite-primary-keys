<?php

namespace MaksimM\CompositePrimaryKeys\Eloquent;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MaksimM\CompositePrimaryKeys\Exceptions\WrongKeyException;

class CompositeKeyQueryBuilder extends Builder
{
    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     * @param array $columns
     *
     *@throws WrongKeyException
     *
     * @return Model|Collection|static[]|static|null
     */
    public function find($id, $columns = ['*'])
    {
        if ((!$this->getModel()->hasCompositeIndex() || (is_array($id) && !Arr::isAssoc($id))) && (is_array($id) || $id instanceof Arrayable)) {
            return $this->findMany($id, $columns);
        }

        return $this->whereKey($this->getModel()->hasCompositeIndex() ? [$id] : $id)->first($columns);
    }

    /**
     * Add a where clause on the primary key to the query.
     *
     * @param mixed $ids
     *
     * @throws WrongKeyException
     *
     * @return $this
     */
    public function whereKey($ids)
    {
        return $this->applyIds($ids);
    }

    /**
     * Add a where clause on the primary key to the query.
     *
     * @param mixed $ids
     *
     * @throws WrongKeyException
     *
     * @return $this
     */
    public function whereKeyNot($ids)
    {
        return $this->applyIds($ids, true);
    }

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param array   $models
     * @param string  $name
     * @param Closure $constraints
     *
     * @return array
     */
    protected function eagerLoadRelation(array $models, $name, Closure $constraints)
    {
        // First we will "back up" the existing where conditions on the query so we can
        // add our eager constraints. Then we will merge the wheres that were on the
        // query back to it in order that any where conditions might be specified.
        $relation = $this->getRelation($name);

        $this->disableBinaryMutators($models);
        $relation->addEagerConstraints($models);

        $constraints($relation);

        // Once we have the results, we just match those back up to their parent models
        // using the relationship instance. Then we just return the finished arrays
        // of models which have been eagerly hydrated and are readied for return.
        $eagerRelation = $relation->getEager();
        $this->disableBinaryMutators($eagerRelation);

        $matchedRelations = $relation->match(
            $relation->initRelation($models, $name),
            $eagerRelation, $name
        );

        $this->enableBinaryMutators($models);
        $this->enableBinaryMutators($eagerRelation);

        return $matchedRelations;
    }

    /**
     * OneTwoMany and Similar Relation support for binary columns.
     *
     * @param $models
     */
    private function disableBinaryMutators($models)
    {
        foreach ($models as $model) {
            if (method_exists($model, 'disableBinaryMutators')) {
                $model->disableBinaryMutators();
            }
        }
    }

    /**
     * OneTwoMany and Similar Relation support for binary columns.
     *
     * @param $models
     */
    private function enableBinaryMutators($models)
    {
        foreach ($models as $model) {
            if (method_exists($model, 'enableBinaryMutators')) {
                $model->enableBinaryMutators();
            }
        }
    }
}

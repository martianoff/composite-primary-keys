<?php

namespace MaksimM\CompositePrimaryKeys\Http\Traits;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use MaksimM\CompositePrimaryKeys\Eloquent\Relationships\CompositeBelongsTo;
use MaksimM\CompositePrimaryKeys\Eloquent\Relationships\CompositeBelongsToMany;

trait CompositeRelationships
{
    /**
     * @param      $related
     * @param null $foreignKey
     * @param null $ownerKey
     * @param null $relation
     *
     * @return BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        /**
         * @var Model $instance
         */
        $instance = $this->newRelatedInstance($related);

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        if (is_null($foreignKey)) {
            $foreignKey = is_array($instance->getKeyName()) ? array_map(
                function ($key) use ($relation) {
                    return Str::snake($relation).'_'.$key;
                },
                $instance->getKeyName()
            ) : Str::snake($relation).'_'.$instance->getKeyName();
        }

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $ownerKey = $ownerKey ?: $instance->getKeyName();

        $relationQuery = $this->newBelongsTo(
            $instance->newQuery(),
            $this,
            $foreignKey,
            $ownerKey,
            $relation
        );

        return $relationQuery;
    }

    protected function executeWithinOptionalBinaryTransformation(Closure $relation, ...$models)
    {
        foreach ($models as $model) {
            if (method_exists($model, 'disableBinaryMutators')) {
                $model->disableBinaryMutators();
            }
        }
        $relationResult = $relation();
        foreach ($models as $model) {
            if (method_exists($model, 'enableBinaryMutators')) {
                $model->enableBinaryMutators();
            }
        }

        return $relationResult;
    }

    /**
     * Instantiate a new BelongsTo relationship.
     *
     * @param Builder $query
     * @param Model   $child
     * @param string  $foreignKey
     * @param string  $ownerKey
     * @param string  $relation
     *
     * @return BelongsTo
     */
    protected function newBelongsTo(Builder $query, Model $child, $foreignKey, $ownerKey, $relation)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $child, $foreignKey, $ownerKey, $relation) {
            return new CompositeBelongsTo($query, $child, $foreignKey, $ownerKey, $relation);
        }, $query->getModel(), $child);
    }

    /**
     * Instantiate a new HasOne relationship.
     *
     * @param Builder $query
     * @param Model   $parent
     * @param string  $foreignKey
     * @param string  $localKey
     *
     * @return HasOne
     */
    protected function newHasOne(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $parent, $foreignKey, $localKey) {
            return new HasOne($query, $parent, $foreignKey, $localKey);
        }, $query->getModel(), $parent);
    }

    /**
     * Instantiate a new MorphOne relationship.
     *
     * @param Builder $query
     * @param Model   $parent
     * @param string  $type
     * @param string  $id
     * @param string  $localKey
     *
     * @return MorphOne
     */
    protected function newMorphOne(Builder $query, Model $parent, $type, $id, $localKey)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $parent, $type, $id, $localKey) {
            return new MorphOne($query, $parent, $type, $id, $localKey);
        }, $query->getModel(), $parent);
    }

    /**
     * Instantiate a new MorphTo relationship.
     *
     * @param Builder $query
     * @param Model   $parent
     * @param string  $foreignKey
     * @param string  $ownerKey
     * @param string  $type
     * @param string  $relation
     *
     * @return MorphTo
     */
    protected function newMorphTo(Builder $query, Model $parent, $foreignKey, $ownerKey, $type, $relation)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $parent, $foreignKey, $ownerKey, $type, $relation) {
            return new MorphTo($query, $parent, $foreignKey, $ownerKey, $type, $relation);
        }, $query->getModel(), $parent);
    }

    /**
     * Instantiate a new HasMany relationship.
     *
     * @param Builder $query
     * @param Model   $parent
     * @param string  $foreignKey
     * @param string  $localKey
     *
     * @return HasMany
     */
    protected function newHasMany(Builder $query, Model $parent, $foreignKey, $localKey)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $parent, $foreignKey, $localKey) {
            return new HasMany($query, $parent, $foreignKey, $localKey);
        }, $query->getModel(), $parent);
    }

    /**
     * Instantiate a new HasManyThrough relationship.
     *
     * @param Builder $query
     * @param Model   $farParent
     * @param Model   $throughParent
     * @param string  $firstKey
     * @param string  $secondKey
     * @param string  $localKey
     * @param string  $secondLocalKey
     *
     * @return HasManyThrough
     */
    protected function newHasManyThrough(Builder $query, Model $farParent, Model $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey) {
            return new HasManyThrough($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey);
        }, $query->getModel(), $farParent);
    }

    /**
     * Instantiate a new MorphMany relationship.
     *
     * @param Builder $query
     * @param Model   $parent
     * @param string  $type
     * @param string  $id
     * @param string  $localKey
     *
     * @return MorphMany
     */
    protected function newMorphMany(Builder $query, Model $parent, $type, $id, $localKey)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $parent, $type, $id, $localKey) {
            return new MorphMany($query, $parent, $type, $id, $localKey);
        }, $query->getModel(), $parent);
    }

    /**
     * Instantiate a new BelongsToMany relationship.
     *
     * @param Builder $query
     * @param Model   $parent
     * @param string  $table
     * @param string  $foreignPivotKey
     * @param string  $relatedPivotKey
     * @param string  $parentKey
     * @param string  $relatedKey
     * @param string  $relationName
     *
     * @return BelongsToMany
     */
    protected function newBelongsToMany(Builder $query, Model $parent, $table, $foreignPivotKey, $relatedPivotKey,
        $parentKey, $relatedKey, $relationName = null)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName) {
            return new CompositeBelongsToMany($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
        }, $query->getModel(), $parent);
    }

    /**
     * Instantiate a new MorphToMany relationship.
     *
     * @param Builder $query
     * @param Model   $parent
     * @param string  $name
     * @param string  $table
     * @param string  $foreignPivotKey
     * @param string  $relatedPivotKey
     * @param string  $parentKey
     * @param string  $relatedKey
     * @param string  $relationName
     * @param bool    $inverse
     *
     * @return MorphToMany
     */
    protected function newMorphToMany(Builder $query, Model $parent, $name, $table, $foreignPivotKey,
        $relatedPivotKey, $parentKey, $relatedKey,
        $relationName = null, $inverse = false)
    {
        return $this->executeWithinOptionalBinaryTransformation(function () use ($query, $parent, $name, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey,
            $relationName, $inverse) {
            return new MorphToMany($query, $parent, $name, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey,
                $relationName, $inverse);
        }, $query->getModel(), $parent);
    }
}

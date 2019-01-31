<?php

namespace MaksimM\CompositePrimaryKeys\Eloquent\Relationships;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MaksimM\CompositePrimaryKeys\Exceptions\WrongRelationConfigurationException;
use MaksimM\CompositePrimaryKeys\Scopes\CompositeKeyScope;

class CompositeBelongsTo extends BelongsTo
{
    protected $magicKeyDelimiter = '___';

    /**
     * Set the base constraints on the relation query.
     *
     * @throws WrongRelationConfigurationException
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            // For belongs to relationships, which are essentially the inverse of has one
            // or has many relationships, we need to actually query on the primary key
            // of the related models matching on the foreign key that's on a parent.
            $table = $this->related->getTable();

            $ownerKeys = $this->getOwnerKeys();
            $foreignKeys = $this->getForeignKeys();

            if (count($ownerKeys) != count($foreignKeys)) {
                throw new WrongRelationConfigurationException();
            }
            foreach ($ownerKeys as $keyIndex => $key) {
                $this->query->where($table.'.'.$key, '=', $this->child->{$foreignKeys[$keyIndex]});
            }
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param array $models
     *
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $ownerKeys = $this->getOwnerKeys();

        // We'll grab the primary key name of the related models since it could be set to
        // a non-standard name and not "id". We will then construct the constraint for
        // our eagerly loading query so it returns the proper models from execution.
        (new CompositeKeyScope(array_map(function ($keyName) {
            return  $this->related->getTable().'.'.$keyName;
        }, $ownerKeys), $this->getEagerModelKeys($models), false, method_exists($this->related, 'getBinaryColumns') ? $this->related->getBinaryColumns() : []))->apply($this->query);
    }

    public function getOwnerKeys()
    {
        if (method_exists($this->parent, 'hasCompositeIndex') && $this->parent->hasCompositeIndex() && !is_array($this->ownerKey) && strpos($this->ownerKey, $this->magicKeyDelimiter) !== false) {
            $this->ownerKey = explode($this->magicKeyDelimiter, $this->ownerKey);
        }

        return !is_array($this->ownerKey) ? [$this->ownerKey] : $this->ownerKey;
    }

    public function getForeignKeys()
    {
        if (method_exists($this->related, 'hasCompositeIndex') && $this->related->hasCompositeIndex() && !is_array($this->foreignKey) && strpos($this->foreignKey, $this->magicKeyDelimiter) !== false) {
            $this->foreignKey = explode($this->magicKeyDelimiter, $this->foreignKey);
        }

        return !is_array($this->foreignKey) ? [$this->foreignKey] : $this->foreignKey;
    }

    /**
     * Gather the keys from an array of related models.
     *
     * @param array $models
     *
     * @return array
     */
    protected function getEagerModelKeys(array $models)
    {
        $keys = [];

        $ownerKeys = $this->getOwnerKeys();
        $foreignKeys = $this->getForeignKeys();

        // First we need to gather all of the keys from the parent models so we know what
        // to query for via the eager loading query. We will add them to an array then
        // execute a "where in" statement to gather up all of those related records.
        foreach ($models as $model) {
            $compositeKey = [];
            foreach ($foreignKeys as $index => $foreignKey) {
                if (!is_null($value = $model->{$foreignKey})) {
                    $compositeKey[$this->related->getTable().'.'.$ownerKeys[$index]] = $value;
                }
            }
            $keys[] = $compositeKey;
        }

        if (count($foreignKeys) == 1) {
            // If there are no keys that were not null we will just return an array with null
            // so this query wont fail plus returns zero results, which should be what the
            // developer expects to happen in this situation. Otherwise we'll sort them.
            if (count($keys) === 0) {
                return [null];
            }

            sort($keys);

            return array_values(array_unique($keys));
        } else {
            return $keys;
        }
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param array                                    $models
     * @param \Illuminate\Database\Eloquent\Collection $results
     * @param string                                   $relation
     *
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $foreignKeys = $this->getForeignKeys();

        $ownerKeys = $this->getOwnerKeys();

        // First we will get to build a dictionary of the child models by their primary
        // key of the relationship, then we can easily match the children back onto
        // the parents using that dictionary and the primary key of the children.
        $dictionary = [];

        foreach ($results as $result) {
            $dictionary[implode($this->magicKeyDelimiter, array_map(function ($owner) use ($result) {
                return $result->getAttribute($owner);
            }, $ownerKeys))] = $result;
        }

        // Once we have the dictionary constructed, we can loop through all the parents
        // and match back onto their children using these keys of the dictionary and
        // the primary key of the children to map them onto the correct instances.
        foreach ($models as $model) {
            $foreignKey = implode($this->magicKeyDelimiter, array_map(function ($foreign) use ($model) {
                return $model->{$foreign};
            }, $foreignKeys));
            if (isset($dictionary[$foreignKey])) {
                $model->setRelation($relation, $dictionary[$foreignKey]);
            }
        }

        return $models;
    }
}

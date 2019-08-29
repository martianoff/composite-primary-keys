<?php

namespace MaksimM\CompositePrimaryKeys\Eloquent\Relationships;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection as BaseCollection;
use MaksimM\CompositePrimaryKeys\Http\Traits\CompositeRelationships;

class CompositeBelongsToMany extends BelongsToMany
{
    use CompositeRelationships;

    /**
     * Get all of the IDs from the given mixed value.
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function parseIds($value)
    {
        if ($value instanceof Model) {
            return [$this->executeWithinOptionalBinaryTransformation(function () use ($value) {
                return $value->{$this->relatedKey};
            }, $value)];
        }

        if ($value instanceof Collection) {
            return $value->pluck($this->relatedKey)->all();
        }

        if ($value instanceof BaseCollection) {
            return $value->toArray();
        }

        return (array) $value;
    }

    /**
     * Get the ID from the given mixed value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function parseId($value)
    {
        return $value instanceof Model ? $this->executeWithinOptionalBinaryTransformation(function () use ($value) {
            $value->{$this->relatedKey};
        }, $value) : $value;
    }

    /**
     * Create a new query builder for the pivot table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newPivotQuery()
    {
        $query = $this->newPivotStatement();

        foreach ($this->pivotWheres as $arguments) {
            call_user_func_array([$query, 'where'], $arguments);
        }

        foreach ($this->pivotWhereIns as $arguments) {
            call_user_func_array([$query, 'whereIn'], $arguments);
        }

        return $this->executeWithinOptionalBinaryTransformation(function () use ($query) {
            return $query->where($this->foreignPivotKey, $this->parent->{$this->parentKey});
        }, $this->parent);
    }

    /**
     * Create a new pivot attachment record.
     *
     * @param int  $id
     * @param bool $timed
     *
     * @return array
     */
    protected function baseAttachRecord($id, $timed)
    {
        $record[$this->relatedPivotKey] = $id;

        $record[$this->foreignPivotKey] = $this->executeWithinOptionalBinaryTransformation(function () {
            return $this->parent->{$this->parentKey};
        }, $this->parent);

        // If the record needs to have creation and update timestamps, we will make
        // them by calling the parent model's "freshTimestamp" method which will
        // provide us with a fresh timestamp in this model's preferred format.
        if ($timed) {
            $record = $this->addTimestampsToAttachment($record);
        }

        foreach ($this->pivotValues as $value) {
            $record[$value['column']] = $value['value'];
        }

        return $record;
    }
}

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
}

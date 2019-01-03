<?php

namespace MaksimM\CompositePrimaryKeys\Eloquent;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
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
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null
     *
     * @throws WrongKeyException
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
     * @return $this
     *
     * @throws WrongKeyException
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
     * @return $this
     *
     * @throws WrongKeyException
     */
    public function whereKeyNot($ids)
    {
        return $this->applyIds($ids, true);
    }
}

<?php

namespace MaksimM\CompositePrimaryKeys\Http\Traits;

trait PrimaryKeyInformation
{
    public function hasCompositeIndex()
    {
        return is_array($this->primaryKey);
    }
}

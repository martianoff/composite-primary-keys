<?php

namespace MaksimM\CompositePrimaryKeys\Exceptions;

use Throwable;

class MissingPrimaryKeyValueException extends \Exception
{
    private $missedValuePrimaryKey;

    public function __construct($missedValuePrimaryKey, string $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->missedValuePrimaryKey = $missedValuePrimaryKey;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getMissedValuePrimaryKey()
    {
        return $this->missedValuePrimaryKey;
    }
}

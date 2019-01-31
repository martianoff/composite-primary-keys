<?php

namespace MaksimM\CompositePrimaryKeys\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use MaksimM\CompositePrimaryKeys\Exceptions\WrongKeyException;

trait NormalizedKeysParser
{
    protected $magicKeyDelimiter = '___';

    /**
     * Get key-value array from normalized key.
     *
     * @param $normalizedKey
     *
     * @throws WrongKeyException
     *
     * @return array
     */
    public function parseNormalizedKey($normalizedKey)
    {
        $parsedKeys = explode($this->magicKeyDelimiter, $normalizedKey);
        $keys = $this instanceof Model ? $this->getRawKeyName() : $this->key;
        foreach ($keys as $index => $key) {
            $keys[$key] = in_array($key, $this->getBinaryColumns()) ? $this->recoverBinaryKey($key, $parsedKeys[$index]) : $parsedKeys[$index];
        }

        return $keys;
    }

    /**
     * Get normalized key.
     *
     * @return string
     */
    private function getNormalizedKeyName()
    {
        return implode($this->magicKeyDelimiter, array_merge($this->getRawKeyName()));
    }

    /**
     * Get normalized key.
     *
     * @return string
     */
    private function getNormalizedKey()
    {
        $rawKeys = $this->getRawKey();
        foreach ($rawKeys as $key => $value) {
            if (in_array($key, $this->getBinaryColumns())) {
                $rawKeys[$key] = strtoupper(bin2hex($value));
            }
        }

        return implode($this->magicKeyDelimiter, $rawKeys);
    }

    /**
     * @param $key
     * @param $hexValue
     *
     * @throws WrongKeyException
     *
     * @return bool|string
     */
    private function recoverBinaryKey($key, $hexValue)
    {
        try {
            return hex2bin($hexValue);
        } catch (\Exception $exception) {
            throw new WrongKeyException("$key has invalid hex value");
        }
    }
}

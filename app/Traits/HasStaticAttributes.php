<?php

namespace App\Traits;

trait HasStaticAttributes
{
    /**
     * @var array
     */
    private static array $attributes = [];

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    private function setAttribute(string $key, $value)
    {
        self::$attributes[$key] = $value;
    }

    /**
     * @param  string  $key
     * @return array
     */
    private function getAttribute(string $key)
    {
        return self::$attributes[$key] ?? null;
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }
}

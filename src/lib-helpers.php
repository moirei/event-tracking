<?php

use MOIREI\EventTracking\Helpers;

if (!function_exists('array_get_any')) {
    /**
     * Get any of the keys from object or array.
     * Allows "dot" notation.
     *
     * @param  mixed  $target
     * @param  string[]  $keys
     * @param  mixed  $default
     */
    function array_get_any($target, array $keys, mixed $default = null)
    {
        return Helpers::getAny($target, $keys, $default);
    }
}

if (!function_exists('is_enum')) {
    /**
     * Check if value is an enum.
     *
     * @param  mixed  $value
     * @return bool
     */
    function is_enum($value)
    {
        return Helpers::isEnum($value);
    }
}

if (!function_exists('resolve_model_event')) {
    /**
     * Resolve model event name.
     *
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @param string $method
     */
    function resolve_model_event(\Illuminate\Database\Eloquent\Model|string $model, string $method)
    {
        return Helpers::resolveModelEvent($model, $method);
    }
}

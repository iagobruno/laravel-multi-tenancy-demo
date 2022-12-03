<?php

/**
 * Run "composer dump-autoload" after edit this file!
 */

if (!function_exists('tenant')) {
    function tenant(string|null $attribute = null)
    {
        if (is_null($attribute)) {
            return request()->route('tenant');
        }
        return request()->route('tenant')->getAttribute($attribute);
    }
}


if (!function_exists('route_tenant')) {
    /**
     * Generate the URL to a named route binded to the current tenant.
     */
    function route_tenant(string $name, $parameters = [], $absolute = true)
    {
        return route($name, [
            'tenant' => tenant()->subdomain,
            ...$parameters,
        ], $absolute);
    }
}

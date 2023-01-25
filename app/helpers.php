<?php

/**
 * Run "composer dump-autoload" after edit this file!
 */

if (!function_exists('tenant')) {
    function tenant()
    {
        return request()->route('tenant');
    }
}


if (!function_exists('tenant_route')) {
    /**
     * Generate the URL to a named route binded to the current tenant.
     */
    function tenant_route(string $name, $parameters = [], $absolute = true)
    {
        return route("tenant.$name", [
            'tenant' => tenant()->subdomain,
            ...$parameters,
        ], $absolute);
    }
}

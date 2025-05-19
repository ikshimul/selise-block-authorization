<?php

namespace Inzamam\SeliseBlockAuthorization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see Inzamam\SeliseBlockAuthorization\AuthService
 */

class SeliseBlockAuthService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return 'selise-block-auth-service';
    }
}
<?php

namespace Lava\Filepond\Facades;

use Illuminate\Support\Facades\Facade;

class Filepond extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filepond';
    }
}

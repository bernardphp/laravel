<?php

namespace Bernard\Laravel\Facades;

/**
 * @package Bernard
 */
class Consumer extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'bernard:consumer';
    }
}

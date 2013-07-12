<?php

namespace Bernard\Laravel\Facades;

/**
 * @package Bernard
 */
class Producer extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'bernard:producer';
    }
}

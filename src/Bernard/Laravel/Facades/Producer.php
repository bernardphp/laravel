<?php

namespace Bernard\Laravel\Facades;

use Bernard\Message\DefaultMessage;

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

    /**
     * Create a new default message
     *
     * @param string $name       Name of the message
     * @param array  $parameters Parameters for the message
     */
    public static function create($name, array $parameters = array())
    {
        self::produce(new DefaultMessage($name, $parameters));
    }
}

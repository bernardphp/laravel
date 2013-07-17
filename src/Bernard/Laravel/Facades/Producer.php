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

    /**
     * Create a new message in the queue
     *
     * @param string $name         Name of the message
     * @param array  $parameters   Optional parameters for the message
     * @param string $messageClass Optional class of the message. Defaults to \Bernard\Message\DefaultMessage
     */
    public static function message($name, array $parameters = array(), $messageClass = '\\Bernard\\Message\\DefaultMessage')
    {
        self::produce(new $messageClass($name, $parameters));
    }
}

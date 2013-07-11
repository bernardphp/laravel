<?php

namespace Bernard\Tests\Laravel;

use Bernard\Message\DefaultMessage;
use Bernard\Tests\Laravel\Fixtures;

class RoundupTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->app['config']['bernard::driver']     = 'Bernard\Tests\Laravel\Fixtures\DummyDriver';
        $this->app['config']['bernard::connection'] = 'dummy';
        $this->app['config']['bernard::services']   = array(
            'Foo' => new Fixtures\DummyService
        );

        $this->app['dummy'] = new Fixtures\DummyConnection;
    }

    public function testAllJustWorksGreat()
    {
        $this->expectOutputString('Data: bar');

        $queue = $this->app['bernard.queues']->create('foo');

        $this->app['bernard.producer']->produce(new DefaultMessage('Foo', array('data' => 'bar')));
        $this->app['bernard.consumer']->consume($queue, null, array(
            'max-runtime' => 0.1,
        ));
    }

}

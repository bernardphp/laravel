<?php

class RoundupTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->app['config']['bernard::driver']     = '\\Bernard\\Laravel\\Tests\\DummyDriver';
        $this->app['config']['bernard::connection'] = 'dummy';
        $this->app['dummy'] = new \Bernard\Laravel\Tests\DummyConnection;
        $this->app['config']['bernard::services']   = array(
            'Foo' => new \Bernard\Laravel\Tests\DummyService
        );
    }

    public function testAllJustWorksGreat()
    {
        $this->expectOutputString('Data: bar');
        $this->app['bernard.producer']->produce(new \Bernard\Message\DefaultMessage('Foo', array('data' => 'bar')));
        $this->app['bernard.consumer']->consume(
            $this->app['bernard.queues']->create('foo'),
            null,
            array(
                'max-runtime' => 0.1
            )
        );
    }

}
<?php

class CustomDriverTest extends TestCase
{

    public function testUseCustomDriver()
    {
        $this->app['config']['bernard::driver']     = '\\Bernard\\Laravel\\Tests\\DummyDriver';
        $this->app['config']['bernard::connection'] = 'dummy_connection';
        $this->app['dummy_connection']              = new \Bernard\Laravel\Tests\DummyConnection;
        $this->assertInstanceOf('Bernard\Laravel\Tests\DummyDriver', $this->app['bernard.driver']);
    }

}
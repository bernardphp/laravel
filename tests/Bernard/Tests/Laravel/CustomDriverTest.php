<?php

namespace Bernard\Tests\Laravel;

use Bernard\Tests\Laravel\Fixtures;

class CustomDriverTest extends TestCase
{
    public function testUseCustomDriver()
    {
        $this->app['config']['bernard::driver']     = 'Bernard\Tests\Laravel\Fixtures\DummyDriver';
        $this->app['config']['bernard::connection'] = 'dummy_connection';
        $this->app['dummy_connection']              = new Fixtures\DummyConnection;

        $this->assertInstanceOf('Bernard\Tests\Laravel\Fixtures\DummyDriver', $this->app['bernard.driver']);
    }
}

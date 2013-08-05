<?php

namespace Bernard\Tests\Laravel;

use Bernard\Tests\Laravel\Fixtures;

class IntegrationTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->app['config']['bernard::driver']   = \Mockery::mock('stdClass, \\Bernard\\Driver');
        $this->app['config']['bernard::services'] = array(
            'Foo' => new Fixtures\DummyService
        );
    }

    public function testSerializerContainerIsAvailable()
    {
        $this->assertInstanceOf('Bernard\Serializer', $this->app['bernard.serializer']);
    }

    public function testDriverContainerIsAvailable()
    {
        $this->assertInstanceOf('Bernard\Driver', $this->app['bernard.driver']);
    }

    public function testDefaultSerializer()
    {
        $this->assertInstanceOf('Bernard\Serializer\NaiveSerializer', $this->app['bernard.serializer']);
    }

    public function testCheckSqsDriverContainer()
    {
        $this->app['config']['bernard::driver'] = 'sqs';
        $this->app['sqs'] = $this->getMockBuilder('\\Aws\\Sqs\\SqsClient')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertInstanceOf('Bernard\Driver\SqsDriver', $this->app['bernard.driver']);
    }

    public function testCheckIronMqDriverContainer()
    {
        $this->app['config']['bernard::driver'] = 'iron_mq';
        $this->app['iron_mq'] = $this->getMockBuilder('\\IronMQ')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertInstanceOf('Bernard\Driver\IronMqDriver', $this->app['bernard.driver']);
    }

    public function testCheckPredisDriverContainer()
    {
        $this->app['config']['bernard::driver'] = 'predis';
        $this->app['predis'] = $this->getMockBuilder('\\Predis\\ClientInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertInstanceOf('Bernard\Driver\PredisDriver', $this->app['bernard.driver']);
    }

    public function testCheckAppEngineDriverContainer()
    {
        $this->app['config']['bernard::driver'] = 'app_engine';
        $this->app['config']['bernard::queue_endpoints'] = array('some-queue' => '/url_endpoint');
        $this->assertInstanceOf('Bernard\Driver\AppEngineDriver', $this->app['bernard.driver']);
    }

    public function testThatConsumerContainerIsThere()
    {
        $this->assertInstanceOf('Bernard\Consumer', $this->app['bernard.consumer']);
    }

    public function testThatProducerContainerIsThere()
    {
        $this->assertInstanceOf('Bernard\Producer', $this->app['bernard.producer']);
    }
}

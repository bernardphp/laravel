<?php

namespace Bernard\Tests\Laravel;

use Bernard\Message\DefaultMessage;
use Bernard\Queue\InMemoryQueue;

class FacadeTest extends TestCase
{
    public function testProducerFacade()
    {
        $message = new DefaultMessage('SendNewsletter');

        $mock = $this->getMockBuilder('Bernard\Producer')->disableOriginalConstructor()->getMock();
        $mock->expects($this->once())->method('produce')->with($message);

        $this->app['bernard:producer'] = $mock;

        \Producer::produce($message);
    }

    public function testConsumerFacade()
    {
        $queue = new InMemoryQueue('queue');

        $mock = $this->getMockBuilder('Bernard\Consumer')->disableOriginalConstructor()->getMock();
        $mock->expects($this->once())->method('consume')->with($queue);

        $this->app['bernard:consumer'] = $mock;

        \Consumer::consume($queue);
    }
}

<?php

namespace Bernard\Laravel\Tests;

use Bernard\Driver;

class DummyDriver implements Driver {
    protected $data;

    public function __construct(DummyConnection $conn) {
        $this->data = array();
    }

    public function listQueues()
    {
        return array_keys($this->data);
    }

    public function createQueue($queueName)
    {
        $this->data[$queueName] = array();
    }

    public function countMessages($queueName)
    {
        if (isset($this->data[$queueName])) {
            return count($this->data[$queueName]);
        } else {
            return 0;
        }
    }

    public function pushMessage($queueName, $message)
    {
        if (isset($this->data[$queueName])) {
            $this->data[$queueName][] = $message;
        }
    }

    public function popMessage($queueName, $interval = 5)
    {
        if (!empty($this->data[$queueName])) {
            $msg = array_shift($this->data[$queueName]);
            return array($msg, null);
        } else {
            usleep(100000);
            return null;
        }
    }


    public function acknowledgeMessage($queueName, $receipt)
    {
    }

    public function peekQueue($queueName, $index = 0, $limit = 20)
    {
        return array();
    }

    public function removeQueue($queueName)
    {
        if (isset($this->data[$queueName])) {
            unset($this->data[$queueName]);
        }
    }

    public function info()
    {
    }
}
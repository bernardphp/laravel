<?php

namespace Bernard\Tests\Laravel\Fixtures;

use Bernard\Message\DefaultMessage;

class DummyService
{

    public function onFoo(DefaultMessage $message)
    {
        if (isset($message->data)) {
            echo "Data: ". $message->data;
        } else {
            echo "No Data";
        }
    }

}

<?php

namespace Bernard\Tests\Laravel;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageAliases()
    {
        return array(
            'Producer' => 'Bernard\Laravel\Facades\Producer',
            'Consumer' => 'Bernard\Laravel\Facades\Consumer',
        );
    }

    protected function getPackageProviders()
    {
        return array(
            'Bernard\Laravel\BernardServiceProvider',
        );
    }
}

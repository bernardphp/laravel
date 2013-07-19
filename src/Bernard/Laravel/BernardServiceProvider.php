<?php

namespace Bernard\Laravel;

use Illuminate\Support\ServiceProvider;
use Bernard\ServiceResolver\ObjectResolver;
use Bernard\Producer;
use Bernard\Consumer;
use Bernard\QueueFactory\PersistentFactory;
use Symfony\Component\Serializer\Serializer;

class BernardServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('bernard/laravel');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDrivers();
        $this->registerSerializers();
        $this->registerHelpers();
        $this->registerCommands();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * Overload package name to allow
     */
    protected function getPackageNamespace($package, $namespace)
    {
        return 'bernard';
    }


    /**
     * Register currently available Bernard drivers + custom for extension
     */
    protected function registerDrivers()
    {
        // SQS
        $this->app['bernard.driver.sqs'] = $this->app->share(function ($app) {
            $connection = $app['config']['bernard::connection'] ?: 'sqs';
            $queueUrls  = $app['config']['bernard::queue_urls'] ?: array();
            $prefetch   = $app['config']['bernard::prefetch'];
            return new \Bernard\Driver\SqsDriver(is_object($connection) ? $connection : $app[$connection], $queueUrls, $prefetch);
        });

        // Iron MQ
        $this->app['bernard.driver.iron_mq'] = $this->app->share(function ($app) {
            $connection = $app['config']['bernard::connection'] ?: 'iron_mq';
            return new \Bernard\Driver\IronMqDriver(is_object($connection) ? $connection : $app[$connection], $app['config']['bernard::options']);
        });

        // Predis
        $this->app['bernard.driver.predis'] = $this->app->share(function ($app) {
            $connection = $app['config']['bernard::connection'] ?: 'predis';
            $prefetch   = $app['config']['bernard::prefetch'];
            return new \Bernard\Driver\PredisDriver(is_object($connection) ? $connection : $app[$connection], $prefetch);
        });

        // Custom
        $this->app['bernard.driver.custom'] = $this->app->share(function ($app) {

            // setup driver class name
            $className = studly_case($app['config']['bernard::driver']);
            if (false === strpos($className, '\\')) {
                $className = '\\Bernard\\Driver\\'. $className. 'Driver';
            }

            // determine key holding connection in IoC
            $connection = $app['config']['bernard::connection'];
            $connection = is_object($connection) ? $connection : $app[$connection];

            // setup driver
            if ($options = $app['config']['bernard::options']) {
                return new $className($connection, $options);
            } else {
                return new $className($connection);
            }
        });
    }

    protected function registerSerializers()
    {
        $this->app['bernard.serializer'] = $this->app->share(function ($app) {
            $serializer = $app['config']['bernard::serializer'] ?: 'naive';

            if (is_object($serializer)) {
                return $serializer;
            }

            return $app['bernard.serializer.' . $serializer];
        });

        $this->app['bernard.serializer.naive'] = $this->app->share(function ($app) {
            return new \Bernard\Serializer\NaiveSerializer;
        });

        // serializer
        $this->app['bernard.serializer.symfony'] = $this->app->share(function ($app) {
            $normalizers = array(
                new \Bernard\Symfony\EnvelopeNormalizer,
                new \Bernard\Symfony\DefaultMessageNormalizer
            );

            if (isset($app['config']['bernard::normalizers'])) {
                $normalizers = array_map(function ($class) {
                    return is_object($class) ? $class : new $class;
                }, (array) $app['config']['bernard::normalizers']);
            }

            // the serializer class
            $serializerClass = isset($app['config']['bernard::serializer'])
                ? $app['config']['bernard::serializer']
                : '\\Bernard\\Serializer\\SymfonySerializer';

            // list of encoders
            $encoders = isset($app['config']['bernard::encoders'])
                ? array_map(function ($class) {
                    return is_object($class) ? $class : new $class;
                }, (array) $app['config']['bernard::encoders'])
                : array(new \Symfony\Component\Serializer\Encoder\JsonEncoder);

            return new $serializerClass(
                new Serializer($normalizers, $encoders)
            );
        });
    }

    /**
     * Registers helper containers
     */
    protected function registerHelpers()
    {
        // actual driver
        $this->app['bernard.driver'] = $this->app->share(function ($app) {
            $driver = $app['config']['bernard::driver'];
            if (is_object($driver)) {
                return $driver;
            } else {
                $accessor = 'bernard.driver.'. snake_case($driver);
                if (!isset($app[$accessor])) {
                    $accessor = 'bernard.driver.custom';
                }
                return $app[$accessor];
            }
        });

        // queues
        $this->app['bernard.queues'] = $this->app->share(function ($app) {
            return new PersistentFactory($app['bernard.driver'], $app['bernard.serializer']);
        });

        // the producer
        $this->app['bernard.producer'] = $this->app->share(function ($app) {
            return new Producer($app['bernard.queues']);
        });

        // the consumer
        $this->app['bernard.consumer'] = $this->app->share(function ($app) {
            $resolver = new ObjectResolver;
            $services = $app['config']['bernard::services'];
            foreach ($services as $name => $class) {
                $resolver->register($name, is_object($class) ? $class : (isset($app[$class]) ? $app[$class] : $class));
            }
            return new Consumer($resolver);
        });
    }


    /**
     * Registers helper containers
     */
    protected function registerCommands()
    {
        $this->app['bernard.command.consume'] = $this->app->share(function() {
            return new Commands\BernardConsumeCommand();
        });
        $this->app['bernard.command.produce'] = $this->app->share(function() {
            return new Commands\BernardProduceCommand();
        });
        $this->commands(
            'bernard.command.consume',
            'bernard.command.produce'
        );
    }
}

<?php

namespace Bernard\Laravel\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Bernard\Laravel\BernardServiceProvider as S;
use Bernard\Message\DefaultMessage;

class BernardProduceCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bernard:produce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produce new job in the Bernard queue.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $service = $this->argument('service');
        if (!isset($this->laravel['config']['bernard::services'][$service])) {
            throw new \InvalidArgumentException("Service '$service' is not defined in bernard config.");
        }

        $data = $this->argument('data') ?: array();
        if ($data) {
            try {
                $data = json_decode($data, true);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("Failed to parse json data");
            }
        }

        $this->laravel['bernard.producer']->produce(new DefaultMessage($service, $data));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            //array('queue', InputArgument::REQUIRED, 'Name of the queue to produce the new job in.'),
            array('service', InputArgument::REQUIRED, 'Name of the service (i.e. job), as registered in the bernard config.'),
            array('data', InputArgument::OPTIONAL, 'JSON encoded data for the new job.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            //array('fail-queue', null, InputOption::VALUE_OPTIONAL, 'Queue to re-order failed.', null),
        );
    }

}
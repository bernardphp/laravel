<?php

namespace Bernard\Laravel\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Bernard\Laravel\BernardServiceProvider as S;


class BernardConsumeCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bernard:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consumes and works on Bernard queue.';

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
        $pullQueue  = $this->argument('queue');
        $failQueue  = $this->option('fail-queue');
        $maxRetries = $this->option('max-retries');
        $maxRuntime = $this->option('max-runtime');
        $queues     = $this->laravel['bernard.queues'];


        $this->laravel['bernard.consumer']->consume(
            $queues->create($pullQueue),
            $failQueue ? $queues->create($failQueue) : null,
            array(
                'max-retries' => $maxRetries,
                'max-runtime' => $maxRuntime
            )
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('queue', InputArgument::REQUIRED, 'Name of the queue to consume.'),
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
            array('fail-queue', null, InputOption::VALUE_OPTIONAL, 'Queue to re-order failed.', null),
            array('max-retries', null, InputOption::VALUE_OPTIONAL, 'Max amount of retries.', 5),
            array('max-runtime', null, InputOption::VALUE_OPTIONAL, 'Max time for consuming messages.', null),
        );
    }

}
<?php

namespace Northwestern\SysDev\SOA\Console\Commands;

use Northwestern\SysDev\SOA\MQ;
use Illuminate\Console\Command;

abstract class CheckQueue extends Command
{
    protected $consumer;

    public function __construct(MQ\Consumer $consumer)
    {
        parent::__construct();

        $this->consumer = $consumer;
    } // end __construct

    public function handle()
    {
        // Determine how many messages we should read in one run.
        // By default, it'll only grab 100 -- don't want this to run forever!
        $max_messages = (int)$this->argument('max_messages');
        if ($max_messages == null) {
            $max_messages = config('nusoa.messageQueue.maxConsumptionPerRun');
        }

        for ($i = 0; $i < $max_messages; $i++) {
            $msg = $this->consumer->pullMessage($this->getTopic());

            if ($msg === false) {
                $this->error('Reading from the queue failed.');
                break;
            }

            if ($msg === null) {
                $this->info('No more messages in queue');
                break;
            }

            $this->processMessage($msg);
        }

        $this->info("Run complete. Got $i messages.");
        return true;
    } // end handle

    abstract protected function processMessage($msg);
    abstract protected function getTopic();

} // CheckQueue

<?php

namespace Bernard\Laravel\Driver;

use Bernard\Laravel\Model\BernardQueue;
use Bernard\Laravel\Model\BernardMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Driver supporting Doctrine DBAL
 *
 * @package Bernard
 */
class EloquentDriver implements \Bernard\Driver
{

    /**
     * {@inheritDoc}
     */
    public function listQueues()
    {
        $queues = BernardQueue::all();
        return array_pluck($queues->toArray(), 'name');
    }

    /**
     * {@inheritDoc}
     */
    public function createQueue($queueName)
    {
        $queue = new BernardQueue();
        $queue->name = $queueName;
        try {
            $queue->save();
        } catch (\Exception $e) {
            // name is unique, catch execption on create
            if (strpos($e->getMessage(), 'column name is not unique') === false) {
                throw $e;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function countMessages($queueName)
    {
        return BernardMessage::where('queue', '=', $queueName)->count();
    }

    /**
     * {@inheritDoc}
     */
    public function pushMessage($queueName, $message)
    {
        $this->createQueue($queueName);
        $msg = new BernardMessage();
        $msg->queue   = $queueName;
        $msg->message = $message;
        $msg->visible = true;
        $msg->send_at = new \DateTime();
        $msg->save();
    }

    /**
     * {@inheritDoc}
     */
    public function popMessage($queueName, $interval = 5)
    {
        $runtime = microtime(true) + $interval;
        while (microtime(true) < $runtime) {
            try {
                \DB::Transaction(function () use ($queueName, &$message) {
                    $message = BernardMessage::where('queue', '=', $queueName)
                        ->where('visible', '=', true)
                        ->orderBy('send_at', 'ASC')
                        ->firstOrFail();
                    $message->visible = false;
                    $message->save();
                });
            } catch (ModelNotFoundException $e) {}


            if ($message) {
                return array($message->message, $message->id);
            }

            //sleep for 10 ms
            usleep(10000);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function acknowledgeMessage($queueName, $receipt)
    {
        $message = BernardMessage::find($receipt);
        if ($message) {
            $message->delete();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function peekQueue($queueName, $index = 0, $limit = 20)
    {
        $messages = BernardMessage::where('queue', '=', $queueName)
            ->where('visible', '=', true)
            ->orderBy('send_at', 'ASC')
            ->skip($index)
            ->take($limit)
            ->get();
        return array_pluck($messages->toArray(), 'message');
    }

    /**
     * {@inheritDoc}
     */
    public function removeQueue($queueName)
    {
        BernardMessage::where('queue', '=', $queueName)->delete();
        BernardQueue::where('name', '=', $queueName)->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function info()
    {
        return array();
    }
}

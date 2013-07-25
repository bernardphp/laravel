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
    public function __construct()
    {
    }

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
        /*try {
            $this->connection->insert('bernard_queues', array('name' => $queueName));
        } catch (\Exception $e) {
            // Because SQL server does not support a portable INSERT ON IGNORE syntax
            // this ignores error based on primary key.
        }*/
    }

    /**
     * {@inheritDoc}
     */
    public function countMessages($queueName)
    {
        return BernardMessage::where('queue', '=', $queueName)->count();
        /*return $this->connection->fetchColumn('SELECT COUNT(id) FROM bernard_messages WHERE queue = :queue', array(
            'queue' => $queueName,
        ));*/
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

        /*$types = array('string', 'string', 'datetime');
        $data = array(
            'queue'   => $queueName,
            'message' => $message,
            'sentAt'  => new \DateTime(),
        );

        $this->createQueue($queueName);
        $this->connection->insert('bernard_messages', $data, $types);*/
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


        /*$runtime = microtime(true) + $interval;
        $query = 'SELECT id, message FROM bernard_messages
                  WHERE queue = :queue AND visible = :visible
                  ORDER BY sentAt, id ' . $this->connection->getDatabasePlatform()->getForUpdateSql();

        while (microtime(true) < $runtime) {
            $this->connection->beginTransaction();

            try {
                list($id, $message) = $this->connection->fetchArray($query, array('queue' => $queueName, 'visible' => true));

                $this->connection->update('bernard_messages', array('visible' => false), compact('id'));
                $this->connection->commit();
            } catch (\Exception $e) {
                $this->connection->rollback();
            }

            if (isset($message) && $message) {
                return array($message, $id);
            }

            //sleep for 10 ms
            usleep(10000);
        }*/
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
        /*$this->connection->delete('bernard_messages', array('id' => $receipt, 'queue' => $queueName));*/
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
        return $messages->count() ? array_pluck($messages->toArray(), 'message') : array();
    }

    /**
     * {@inheritDoc}
     */
    public function removeQueue($queueName)
    {
        BernardMessage::where('queue', '=', $queueName)->delete();
        BernardQueue::where('name', '=', $queueName)->delete();
        /*$this->connection->delete('bernard_messages', array('queue' => $queueName));
        $this->connection->delete('bernard_queues', array('name' => $queueName));*/
    }

    /**
     * {@inheritDoc}
     */
    public function info()
    {
        return null;
    }
}

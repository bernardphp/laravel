<?php

namespace Bernard\Laravel\Model;

/**
 * Queue model for bernard
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */
class BernardQueue extends \Eloquent
{
    protected $table = 'bernard_queues';
    public $timestamps = false;
}

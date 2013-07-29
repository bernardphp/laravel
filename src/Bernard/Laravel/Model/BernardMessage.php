<?php

namespace Bernard\Laravel\Model;

/**
 * Message model for bernard
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */
class BernardMessage extends \Eloquent
{
    protected $table = 'bernard_messages';
    public $timestamps = false;
}

<?
/*
 * This file is part of Bernard\Laravel\Model.
 *
 * (c) Ulrich Kautz <ulrich.kautz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bernard\Laravel\Model;

/**
 * Queue model for bernard
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */

class BernardMessage extends \Eloquent
{
    protected $table = 'bernard_messages';
    public $timestamps = false;
}

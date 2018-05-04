<?php

namespace App\Events\DefaultEvent;

/**
 * Description of IdEvent
 *
 * @author Ceddy
 */
class IdEvent
{
    public $id;

    /**
     * Create a new event instance.
     *
     * @param  int  $id
     * 
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
}

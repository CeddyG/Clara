<?php

namespace App\Events\DefaultEvent;

/**
 * Description of IdAndInputEvent
 *
 * @author Ceddy
 */
class IdAndInputEvent
{
    public $aInputs;
    
    public $id;


    /**
     * Create a new event instance.
     *
     * @param  int  $id
     * @param  array  $aInputs
     * 
     * @return void
     */
    public function __construct($id, array $aInputs)
    {
        $this->id       = $id;
        $this->aInputs  = $aInputs;
    }
}

<?php

namespace App\Events\DefaultEvent;

/**
 * Description of InputEvent
 *
 * @author Ceddy
 */
class InputEvent
{
    public $id;

    /**
     * Create a new event instance.
     *
     * @param  array  $aInputs
     * 
     * @return void
     */
    public function __construct(array $aInputs)
    {
        $this->aInputs  = $aInputs;
    }
}

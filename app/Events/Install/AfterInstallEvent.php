<?php

namespace App\Events\Install;

class AfterInstallEvent
{
    public $aInputs;
    
    /**
     * Create a new event instance.
     *
     * @param  array  $aInputs
     * 
     * @return void
     */
    public function __construct(array $aInputs)
    {
        $this->aInputs = $aInputs;
    }
}

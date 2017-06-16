<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Facades\App\Services\Clara\Dataflow;

class DataflowController extends Controller
{
    public function index($sFormat, $sToken, $sParam = null)
    {
        return Dataflow::flow($sFormat, $sToken, $sParam);
    }
}

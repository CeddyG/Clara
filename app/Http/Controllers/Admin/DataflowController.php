<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ContentManagerController;

use App\Repositories\DataflowRepository;
use Facades\App\Services\Clara\Dataflow;

class DataflowController extends ContentManagerController
{
    public function __construct(DataflowRepository $oRepository)
    {
        $this->sPath = 'admin/dataflow';
        $this->sName = 'Dataflow';
        
        $this->oRepository = $oRepository;
        $this->sRequest = 'App\Http\Requests\DataflowRequest';
    } 
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sPageTitle = $this->sName;
        
        $aRepositories = Dataflow::getRepositories();
        
        return view($this->sPath.'/form', compact('sPageTitle', 'aRepositories'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $oItem = $this->oRepository
            ->getFillFromView($this->sPath.'/form')
            ->find($id);
        
        $sPageTitle = $this->sName;
        
        $aRepositories = Dataflow::getRepositories();
        
        return view($this->sPath.'/form',  compact('oItem','sPageTitle', 'aRepositories'));
    }
}

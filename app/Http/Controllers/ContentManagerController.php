<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContentManagerController extends Controller
{
    protected $sPath;
    protected $sPathRedirect;
    protected $sName;

    protected $oRepository;
    protected $sRequest;
    
    protected $sEventBeforeStore    = '';
    protected $sEventAfterStore     = '';
    protected $sEventBeforeUpdate   = '';
    protected $sEventAfterUpdate    = '';
    protected $sEventBeforeDestroy  = '';
    protected $sEventAfterDestroy   = '';


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        return view($this->sPath.'/index', ['sPageTitle' => $this->sName]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAjax(Request $oRequest)
    {
        $this->oRepository->setReturnCollection(false);
        return $this->oRepository->datatable($oRequest->all());
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectAjax(Request $oRequest)
    {
        $this->oRepository->setReturnCollection(false);
        return $this->oRepository->select2($oRequest->all());
    }  
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->sPath.'/form', ['sPageTitle' => $this->sName]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {   
        $oRequest   = app($this->sRequest);
        $aInputs    = $oRequest->all();
        
        if ($this->sEventBeforeStore != '')
        {
            event(new $this->sEventBeforeStore());
        }
        
        $id = $this->oRepository->create($aInputs);
        
        if ($this->sEventAfterStore != '')
        {
            event(new $this->sEventAfterStore($id, $aInputs));
        }
        
        return redirect($this->sPathRedirect)->withOk("L'objet a été créé.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        
        return view($this->sPath.'/form',  compact('oItem','sPageTitle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $oRequest   = app($this->sRequest);
        $aInputs    = $oRequest->all();
        
        if ($this->sEventBeforeUpdate != '')
        {
            event(new $this->sEventBeforeUpdate());
        }
        
        $this->oRepository->update($id, $aInputs);
        
        if ($this->sEventAfterUpdate != '')
        {
            event(new $this->sEventAfterUpdate($id, $aInputs));
        }

        return redirect($this->sPathRedirect)->withOk("L'objet a été modifié.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->sEventBeforeDestroy != '')
        {
            event(new $this->sEventBeforeDestroy($id));
        }
        
        $this->oRepository->delete($id);
        
        if ($this->sEventAfterDestroy != '')
        {
            event(new $this->sEventAfterDestroy($id));
        }

        return redirect($this->sPathRedirect)->withOk("L'objet a été supprimé.");
    }
}

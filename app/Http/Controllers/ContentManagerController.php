<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContentManagerController extends Controller
{
    protected $sPath;
    protected $sName;

    protected $oRepository;
    protected $sRequest;

    /**
    * Constructeur du controller, c'est ici qu'on appelle les middlewares
    */
    public function __construct()
    {
        $this->middleware('log');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $sPageTitle = $this->sName;
        
        return view($this->sPath.'/index', compact('sPageTitle'));
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAjax(Request $oRequest)
    {
        return $this->oRepository->datatable($oRequest->all());
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectAjax(Request $oRequest)
    {
        return $this->oRepository->select2($oRequest->all());
    }  
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sPageTitle = $this->sName;
        
        return view($this->sPath.'/form', compact('sPageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {   
        $oRequest = app($this->sRequest);
        
        $this->oRepository->create($oRequest->all());

        return redirect($this->sPath)->withOk("L'objet a été créé.");
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
        $oRequest = app($this->sRequest);
        
        $this->oRepository->update($id, $oRequest->all());

        return redirect($this->sPath)->withOk("L'objet a été modifié.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->oRepository->delete($id);

        return redirect($this->sPath)->withOk("L'objet a été supprimé.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\Clara\Entity;
use App\Http\Controllers\Controller;

class EntityController extends Controller
{
    public function index()
    {
        $objects            = Entity::getTables();
        $sPageTitle         = 'Entity';
	    $aGotoOptions       = Entity::generateGotoSelectOptions($objects);
	    $aRelationOptions   = Entity::generateRelationSelectOptions($objects);
        
        return view('admin.entity.index', compact('objects', 'sPageTitle', 'aGotoOptions', 'aRelationOptions'));
    }
    
    public function store(Request $oRequest)
    {
        Entity::store($oRequest->all());
        
        return redirect('admin');
    }
}

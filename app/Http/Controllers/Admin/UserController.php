<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Sentinel;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    protected $sPath = 'admin/user';
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sPageTitle = 'Sentinel';
        $oItems     = User::All();

        return view($this->sPath.'/index', compact('oItems', 'sPageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sPageTitle     = "Ajout User";
        $aRoles         = Role::all()->pluck('name', 'id');
        
        return view($this->sPath.'/form', compact('aRoles', 'sPageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest $oRequest
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $oRequest)
    {
        UserRepository::store($oRequest->all());
        
        return redirect($this->sPath);
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
        $sPageTitle     = "Modification";
        $oItem          = Sentinel::findById($id)->load('roles');
        $aRoles         = Role::all()->pluck('name', 'id');
        
        return view($this->sPath.'/form',  compact('oItem', 'aRoles', 'sPageTitle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest $oRequest
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $oRequest, $id)
    {
        UserRepository::update($id, $oRequest->all());

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
        $oUser = Sentinel::findById($id);
        $oUser->roles()->detach();
        $oUser->delete();
        
        return redirect($this->sPath)->withOk("L'objet a été supprimé.");
    }
    
    //Affiche le formulaire du login
    public function login()
    {
        $sPageTitle = 'Login';
        
        //Si on est pas connecté, on affiche le formuaire, sinon on redirige vers l'accueil
        if (!Sentinel::check())
        {
            return view($this->sPath . '/login', compact('sPageTitle'));
        }
        else
        {
            return redirect('admin');
        }
    }
    
    public function authenticate(Request $oRequest)
    {
        $aInputs    = $oRequest->all();
        $bRemember  = array_key_exists('remember', $aInputs);
        
        if(Sentinel::authenticate($aInputs, $bRemember))
        {
            return redirect('admin');
        }
        else
        {
            return redirect('login')
            ->withInput($oRequest->only('email', 'remember'))
            ->withErrors([
                'fail' => trans('auth.failed'),
            ]);
        }
    }
    
    public function logout()
    {
        Sentinel::logout();
        return redirect('/');
    }
}

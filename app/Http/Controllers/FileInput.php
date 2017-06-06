<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App;

class FileInput extends Controller
{
    /**
     * Upload multiple files.
     *
     * 
     */
    public function upload(Request $request)
    {
        //Getting model to set in database
        $nameModel = 'App\\'.$request->get('model');
        $model = new $nameModel();
        
        $destinationPath = $request->get('path');
       
        // getting all of the post data
        $files = $request->file('images');
        
        foreach($files as $file) 
        {
            $rules = array('file' => 'required'); //'required|mimes:png,gif,jpeg,txt,pdf,doc'
            /*$validator = $this->validate($file, $rules);
            if($validator->passes())
            {*/
                $extension = $file->getClientOriginalExtension();
                
                //Création du nom, on vérifie qu'il n'existe pas
                do
                {
                    $filename = $this->random().'.'.$extension;
                } while(file_exists($destinationPath.'/'.$filename));
                
                //On upload
                $upload_success = $file->move($destinationPath, $filename);
                
                //On renseigne le nom du champ dans lequel est stocké le nom du fichier et on lui attribut le nom
                $request[$request->get('field_name')] = $filename;
                
                //On l'ajoute à la BDD
                $model->create($request->all());
            //}
        }
        
        //Variable à utiliser pour envoyer un message
        $output = [];
        
        echo json_encode($output);
    }

    /**
     * Delete file.
     *
     * 
     */
    public function DelFile(Request $request)
    {
        //Getting model to set in database
        $nameModel = 'App\\'.$request->get('model');
        $model = new $nameModel();
        
        //Suppression en BDD
        $id = $request->get('id');
        $model->findOrFail($id)->delete();
       
        //Suppression physique
        $file = $request->get('file');
        
        unlink($file);
        
        //Variable à utiliser pour envoyer un message
        $output = [];
        
        echo json_encode($output);
    }
    
    /**
     * Generate random name
     *
     * 
     */
    private function random() {
        $string = "";
        $chaine = "abcdefghijklmnpqrstuvwxy";
        srand((double)microtime()*1000000);
        
        for($i=0; $i<6; $i++) {
            $string .= $chaine[rand()%strlen($chaine)];
        }
        
        return $string;
    }
}

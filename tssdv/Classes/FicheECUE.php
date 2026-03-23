<?php

namespace tssdv;

use stdClass;
use tssdv\Controllers\FicheECUEController;


class FicheECUE extends \TDS\Document {
    public $uid = null;
    public $name = null;
    public $dir = null;
    public $data = null;

    public static function getDir($uid, $name=''){
        $app = \TDS\App::get();
        return "{$app::$pathList['plus']}/{$app::$appName}/ficheECUE/{$app::$currentYear}/{$uid}/{$name}";
    }

    public function load(){
        $this->data = json_decode(file_get_contents("{$this->dir}/data.json"));
    }

    public function save(){
        file_put_contents("{$this->dir}/data.json", json_encode($this->data));

    }

    public function __construct( String $uid, String $name){
        $this->uid = $uid;
        $this->name = $name;
        $this->dir = self::getDir($uid,$name);

        if (! file_exists($this->dir)){ // si le dossier n'existe pas le créer
            mkdir($this->dir, 0777, true );
        }

        if (! file_exists("{$this->dir}/data.json")){ // si le fichier n'existe pas le créer
            $this->data = json_decode(json_encode([
                "uid" => $uid, 
                "name" => $name,
                'intituleECUE' => 'intitulé',
                'nCM' => 1,
                'nTD' => 1,
                'nTP' => 1,
                'nEnseignantsTP' => 1, 
                'noteVersion' => 'En cours de conception...',
                ]));
            $this->save();
        }
        $this->load();
    } 

}
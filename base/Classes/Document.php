<?php
namespace base;

use base\Controllers\CandidatureController;

class Document extends \TDS\Document {



    // $className sert ici à recueillir l'uid des documents pour la candidature
    public function __construct( String $className, int $id, String $filename, int $timestamp){
        $this->className = $className;
        $this->filename = $filename;
        $this->fName = CandidatureController::getDir($className)."/Docs/{$filename}";
        $this->timestamp = $timestamp;
        $this->id = $id;
        $path_parts = pathinfo($filename);
        $this->title = $path_parts['filename'];
        $this->ext = strtolower($path_parts['extension']);

    } 

    public function getDocDownloadURL(){
        $app = App::get();
        $hex = $app::simpleEncrypt($this);
        return  "/{$app::$appName}/documentsME/getDoc/{$hex}" ;
    }


    public function documentPath(){
        return CandidatureController::getDir($this->className);
    }

    public function getFname(){
        return $this->fName;
    }

    public function rename($newTitle){
        $app = App::get();
        $dir = $this->documentPath();
        $filename = "{$newTitle}.{$this->ext}";
        $fName = "{$dir}/Docs/{$filename}";
        rename($this->fName, $fName);
        $this->title = $newTitle;
        $this->filename = $filename;
        $this->fName = $fName;
    }


}

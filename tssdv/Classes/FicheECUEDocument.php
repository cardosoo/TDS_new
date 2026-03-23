<?php

namespace tssdv;

use tssdv\Controllers\FicheECUEController;


class FicheECUEDocument extends \TDS\Document {
    public $uid = null;
    public $name = null;


    public function __construct( String $uid, String $name, String $filename, int $timestamp){
        $this->filename = $filename;
        $this->timestamp = $timestamp;
        $this->uid = $uid;
        $this->name = $name;
        $this->fName = $this->documentPath()."{$filename}";
        $path_parts = pathinfo($filename);
        $this->title = $path_parts['filename'];
        $this->ext = strtolower($path_parts['extension']);
    } 

    


    public function documentPath(){
        $app = App::get();
        return $app::$pathList['plus']."/{$app::$appName}/ficheECUE/{$app::$currentYear}/{$this->uid}/{$this->name}/Docs/";
    }

    public function rename($newTitle){
        $app = App::get();
        $dir = $this->documentPath();
        $filename = "{$newTitle}.{$this->ext}";
        $fName = "{$dir}{$filename}";
        $res = rename($this->fName, $fName);
        $this->title = $newTitle;
        $this->filename = $filename;
        $this->fName = $fName;
    }


    public function getDocDownloadURL(){
        $app = App::get();
        $hex = $app::simpleEncrypt($this);
        return  "/{$app::$appName}/ficheECUE/getDoc/{$hex}" ;
    }


}

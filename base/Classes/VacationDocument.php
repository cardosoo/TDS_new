<?php

namespace base;

use base\Controllers\VacationController;


class FicheECUEDocument extends \TDS\Document {
    public $id = null;
    public $name = null;


    public function __construct( String $id, String $filename, int $timestamp){
        $this->filename = $filename;
        $this->timestamp = $timestamp;
        $this->id = $id;
        $this->fName = $this->documentPath()."{$filename}";
        $path_parts = pathinfo($filename);
        $this->title = $path_parts['filename'];
        $this->ext = strtolower($path_parts['extension']);
    } 

    public function documentPath(){
        $app = App::get();
        return $app::$pathList['plus']."/{$app::$appName}/vacation/{$app::$currentYear}/{$this->id}/Docs/";
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
        return  "/{$app::$appName}/vacation/getDoc/{$hex}" ;
    }


}

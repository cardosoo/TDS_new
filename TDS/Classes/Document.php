<?php

namespace TDS;

class Document {

    public String $className; 
    public int $id;
    public String $filename;
    public int $timestamp;
    protected String $title;
    protected String $ext;
    protected String $fName;

    // liste des types mimes autorisés depuis https://developer.mozilla.org/fr/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types
    public static $mimeType = [
    'aac' => 'audio/aac',
    'abw' => 'application/x-abiword',
    'arc' => 'application/octet-stream',
    'avi' => 'video/x-msvideo',
    'azw' => 'application/vnd.amazon.ebook',
    'bin' => 'application/octet-stream',
    'bmp' => 'image/bmp',
    'bz' => 'application/x-bzip',
    'bz2' => 'application/x-bzip2',
    'csh' => 'application/x-csh',
    'css' => 'text/css',
    'csv' => 'text/csv',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'eot' => 'application/vnd.ms-fontobject',
    'epub' => 'application/epub+zip',
    'gif' => 'image/gif',
    'htm' => 'text/html',
    'html' => 'text/html',
    'ico' => 'image/x-icon',
    'ics' => 'text/calendar',
    'jar' => 'application/java-archive',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'js' => 'application/javascript',
    'json' => 'application/json',
    'mid' => 'audio/midi',
    'midi' => 'audio/midi',
    'mpeg' => 'video/mpeg',
    'mpkg' => 'application/vnd.apple.installer+xml',
    'odp' => 'application/vnd.oasis.opendocument.presentation',
    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    'odt' => 'application/vnd.oasis.opendocument.text',
    'oga' => 'audio/ogg',
    'ogv' => 'video/ogg',
    'ogx' => 'application/ogg',
    'otf' => 'font/otf',
    'png' => 'image/png',
    'pdf' => 'application/pdf',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'rar' => 'application/x-rar-compressed',
    'rtf' => 'application/rtf',
    'sh' => 'application/x-sh',
    'svg' => 'image/svg+xml',
    'swf' => 'application/x-shockwave-flash',
    'tar' => 'application/x-tar',
    'tif' => 'image/tiff',
    'tiff' => 'image/tiff',
    'ts' => 'application/typescript',
    'ttf' => 'font/ttf',
    'vsd' => 'application/vnd.visio',
    'wav' => 'audio/x-wav',
    'weba' => 'audio/webm',
    'webm' => 'video/webm',
    'webp' => 'image/webp',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'xhtml' => 'application/xhtml+xml',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'xml' => 'application/xml',
    'xul' => 'application/vnd.mozilla.xul+xml',
    'zip' => 'application/zip',
    '3gp' => 'video/3gpp',
    '3g2' => 'video/3gpp2',
    '7z' => 'application/x-7z-compressed',
    ];


    public function __construct( String $className, int $id, String $filename, int $timestamp){
        $this->className = $className;
        $this->filename = $filename;
        $this->fName = self::getDocumentPath($className)."{$id}/{$filename}";
        $this->timestamp = $timestamp;
        $this->id = $id;
        $path_parts = pathinfo($filename);
        $this->title = $path_parts['filename'];
        $this->ext = strtolower($path_parts['extension']);

    } 

    public static function getDocumentPath(String $className){
        $app = App::get();
        return $app::$pathList['plus']."/{$app::$appName}/Docs/{$className}/";
    }

    public function documentPath(){
        return $this::getDocumentPath($this->className);
    }

    public function rename($newTitle){
        $app = App::get();
        $dir = $this->documentPath();
        $filename = "{$newTitle}.{$this->ext}";
        $fName = "{$dir}{$this->id}/{$filename}";
        rename($this->fName, $fName);
        $this->title = $newTitle;
        $this->filename = $filename;
        $this->fName = $fName;
    }

    public function delete(){
        unlink($this->fName);        
    }

    public function getDocDownloadURL(){
        $app = App::get();
        $key = str_pad('', SODIUM_CRYPTO_SECRETBOX_KEYBYTES ,"{$app::$auth->user->id}");
        $hex = $app::simpleEncrypt($this, $key);
        return  "/{$app::$appName}/Docs/{$hex}" ;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getDate($fmt='d LLLL y'){
        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            null, //'Europe/Paris',
            \IntlDateFormatter::GREGORIAN,
            'd LLLL Y'
        );

        $date = new \DateTime();
        $date->setTimestamp($this->timestamp);
        return $formatter->format($date); 
    }

    public function download(){
        $mime= self::$mimeType[$this->ext]??'application/octet-stream';
        // var_dump($this->fName);
        // exit();
        header('Content-type: '.$mime);
        header('Content-Disposition: inline; filename="'."{$this->title}.{$this->ext}".'"');

        //include($this->fName);
        echo file_get_contents($this->fName);
        exit();
    }


    public static function downloadStatic($filename){
        $path_parts = pathinfo($filename);
        $title = $path_parts['filename'];
        $ext = strtolower($path_parts['extension']);

        $mime= self::$mimeType[$ext]??'application/octet-stream';
        header('Content-type: '.$mime);
        header('Content-Disposition: inline; filename="'."{$title}.{$ext}".'"');

        //var_dump($filename);
        echo file_get_contents($filename);
        exit();

    }


}

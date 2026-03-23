<?php
namespace TDS;

use base\Model\Personne;
use stdClass;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Authenticate
 *
 * @author olivier
 */
class Authenticate {
    
    public $isAuth = false;
    public $inBase = false; 
    public $roleList = [];
    public $isAdmin = false;
    public $isSuperAdmin = false;
    public $user = null;
    public $data = null;
    public $uid = null;
    
    public function __construct() {
        $app = \TDS\App::get();

        $this->data = null;
        if (isset($_SESSION['TDS_auth_'.$app::$appName])){
            $this->isAuth = true;
            $this->data =  $_SESSION['TDS_auth_'.$app::$appName];
            $this->inBase = $this->data->inBase ?? false;
            $this->user = $this->data->user;
            $this->roleList = $this->data->roleList ?? [];
            $this->uid = $this->data->uid;
        }
    }
    
    public function getSession(){
        return $_SESSION;
    }

    private static function CAS($logout = false){
        $cas = new CasExtern();
        if ($logout){
            $cas->logout();
            exit();
        }
        return $cas->getUser();
    }
    
    private  function forceCAS(){
        $this->data = new \stdClass();
        $this->data->uid = self::CAS();
        $this->uid = $this->data->uid;
        $this->data->method = 'CAS';
        $this->data->displayname = "force CAS-Pas de LDAP hors des murs";
// var_dump(['forceCAS' => $this->data]);        
    }
    
    
    private function forceMoi($id){
        $this->data = new \stdClass();
        $user = Personne::load($id);
        $this->data->uid = $user->uid;
        $this->uid = $user->uid; 
        $this->data->method = 'Force';
        $this->data->displayname = 'Force uid';
// var_dump(['forceMoi' => $this->data]);        
    }
    
    private function simpleEncrypt($message){
        $app = \TDS\App::get();

        $block_size = 16;
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $json = json_encode($message);
        $padded = sodium_pad($json, $block_size);
        $encrypted = sodium_crypto_secretbox($padded, $nonce, $app::$secretkey);
        $bin = $nonce . $encrypted;
        $hex = bin2hex($bin);
        return $hex;
    }
    
    
    function simpleDecrypt($hex){
        $app = \TDS\App::get();

        $block_size = 16;
        
        $bin = hex2bin($hex);
        $nonce = mb_substr($bin, 0, 24, '8bit');
        $encrypted = mb_substr($bin, 24, null, '8bit');
        
        $padded = sodium_crypto_secretbox_open($encrypted, $nonce, $app::$secretkey);
        $json = sodium_unpad($padded, $block_size);
        $message = json_decode($json);       
        return $message;
    }

    public function directLink($link){
        try {
            $message = $this->simpleDecrypt($link);
        } catch (\Exception $ex) {
            return false;
        }
        return $message;
    }
    
    
    public function buildDirectLink($id){
        $message = new \stdClass();
        $message->id = $id;
        $message->timestamp = time();
        $message->creator = $this->data->uid;
        $message->version = "0.0.0";
        return $this->simpleEncrypt($message);
    }
    
    
    public function forceAuth($id= null){
//        var_dump(['forceAuth' => $this->data]);        
        $app = \TDS\App::get();

//        $id = 292; // uncomment to force auth to id 292


        if (isset($_SESSION['TDS_auth_'.$app::$appName])){
            $this->data =  $_SESSION['TDS_auth_'.$app::$appName];
        } else {
            if ( ! is_null($id) ) {
                $this->forceMoi($id);
            } else {
                $this->forceCAS();
            }
            $_SESSION['TDS_auth_'.$app::$appName]=$this->data;
        }
        $this->uid = $this->data->uid;
        $this->data->user = $app::loadFromUid($this->data->uid);
        $this->user = $this->data->user;
        if ( ! is_null($this->user) ){
            $this->data->inBase=true;
            $this->inBase = true;
            $this->data->roleList = $app::getRoleList($this->user);
            $this->user->uid = $this->data->uid;
        } else {
            $this->data->inBase=false;
            $this->inBase = false;
            $this->data->roleList = [];
            $this->user = new stdClass;
            $this->user->uid = $this->data->uid;
        }
    }
    
    public static function forceLogout(){
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
    }
    
    public function deconnexion(){
        if ($this->data->method == 'CAS'){
            $this->data = null;
            $this->forceLogout();
            self::CAS(true);
        } else if ($this->data->method == 'Force'){
            $this->forceLogout();
        }
        $this->data = null;
    }
    
    public function isAuth(){
        return !is_null($this->isAuth);
    }
    
    public function isInBase(){
        return $this->inBase;
    }

    public function isAdmin(){
        $app = \TDS\App::get();

        $this->isAdmin = isset($app::$auth->roleList["Admin"]);
        $this->isSuperAdmin = isset($app::$auth->roleList["SuperAdmin"]);
    }

    public function hasRole($role){
        $app = \TDS\App::get();
        return array_key_exists($role, $app::$auth->roleList);
    }

    public function __debugInfo() {
        $array  = get_class_vars(get_class($this));
        $ret = [];
        foreach($array as $a => $v ){
            if ($a !== 'data'){
                $ret[$a]= $this->$a; 
            }
        }
        return $ret;
    }
}

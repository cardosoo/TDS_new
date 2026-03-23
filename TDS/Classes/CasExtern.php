<?php
namespace TDS;

class CasExtern {
    
    private $user = NULL;
    public $service = "";
    private static $domaine = "https://jitsi.physique.univ-paris-diderot.fr/ldol";
    //private static $domaine = "https://foire.physique.u-paris.fr/foire/";

    public function __construct($service = NULL){
        $app = \base\App::get();
        $appName = $app::$appName;

        if ($appName =='foire'){
            self::$domaine="https://foire.physique.u-paris.fr/foire/";
        }
        if ($appName =='tssdv'){
            self::$domaine="https://ts.sdv.u-paris.fr/tssdv/";
        }

        if (in_array($appName, ['service', 'services'])){
            self::$domaine = "https://apps.physique.univ-paris-diderot.fr/api/";
        }

        if (is_null($service)){
            //$server = $_SERVER['SERVER_NAME'];
            //$service = explode('.',$server,1);

            $service = $app::$service;
        }

        $this->service = $service;
    }


    public function forceAuth(){
        $app = \TDS\App::get();

        if ( (!isset($_SESSION['auth_cas_extern']) ) || (!isset($_GET['u'])) ){ // alors il faut faire appel à l'identification externe      
            $_SESSION['auth_cas_extern'] = uniqid(); // mais ce pourrait-être n'importe quoi
            $url = bin2hex($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            header("Location: ".self::$domaine."cas/login/{$this->service}/".$url);
            exit();
        }
        $user = filter_input(INPUT_GET, 'user', FILTER_UNSAFE_RAW);        
        $u = filter_input(INPUT_GET, 'u', FILTER_UNSAFE_RAW);
        $uUser = hash('ripemd160', $user);
        if ($u == $uUser){
            $this->user = $user;
        } else {
            $this->user = "Qui est-tu toi ? Que veux-tu ?";
        }
        unset($_SESSION['auth_cas_extern']);
    }

    public function auth(){
        if (is_null($this->user)){
            $this->forceAuth();
        }
    }

    public function getUser(){
        if (is_null($this->user)){
            $this->forceAuth();
        }
        return $this->user;
    }

    public function logout(){
        $app = \TDS\App::get();
        $this->user = NULL;
        header("Location: ".self::$domaine."cas/logout/{$this->service}");
    }



}
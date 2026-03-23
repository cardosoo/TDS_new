<?php
namespace base;

use \base\Model\Personne;

class App extends \zeroUP\App {
    static string $service = 'UPcite';
    static string $structure = 'UPCite';
    static string $phase ='';
    static array  $phaseList = []; 
    static array  $hETD = [];
    static string $mail = '';
    static string $webmestre = '';
    static array  $texte = [];
    static string $mailerUsername = "";
    static string $mailerPassword = "";
    static string $vacationYear = "";
    
    
    public static function loadFromUid($uid){
        $app = \TDS\App::get();
        return $app::call('Personne', 'loadOneWhere',["uid = '{$uid}'"]);
    }

    static function setPermission(){
        parent::setPermission();
    }

    public static function getPersonne($id){
        $app = \TDS\App::get();

        return $app::NS('Personne')::load($id);
    }

    public static function getEnseignement($id){
        $app = \TDS\App::get();

        return $app::NS('Enseignement')::load($id);
    }

    public static function getEnseignementList($code){
        $app = \TDS\App::get();

        return $app::NS('Enseignement')::loadWhere("actif and code LIKE '%{$code}%'");
    }
    

    public static function getVoeu($id){
        $app = \TDS\App::get();

        return $app::NS('Voeu')::load($id);
    }


    public static function getRoleList($personne){
        $app = \TDS\App::get();

        $roleList = [];
        foreach($personne->actasList as $role){
            $roleList[$role->role->nom]=true;
        }

        $responsableList = $app::$auth->user->responsableList;
        if (count($responsableList) >0){
            $roleList['respEtape'] = true;
        }
        return $roleList;
    }

    public  static function rrmdir($src): bool {
        $dir = opendir($src);
        $return = true;
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    $return &= self::rrmdir($full);
                }
                else {
                    $return &= unlink($full);
                }
            }
        }
        closedir($dir);
        $return &= rmdir($src);
        return $return;
    }

    public static function beforeAll(){
    }

}
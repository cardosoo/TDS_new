<?php
namespace TDS;

class LDAPExtern {
    

    //private static $domaine = "https://jitsi.physique.univ-paris-diderot.fr/";
    private static $domaine = "https://foire.physique.univ-paris-diderot.fr/foire/";


    public function __construct(){
        $app = \TDS\App::get();
        //self::$domaine = "https://jitsi.physique.univ-paris-diderot.fr/";
        self::$domaine = "https://foire.physique.u-paris.fr/{$app::$appName}/";       
    }

    public function search(string $filter){
         $context = stream_context_create([
            'ssl' => [
                // Désactiver la vérification du certificat SSL
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);
       
        $param = bin2hex(json_encode($filter));
        $rep = file_get_contents(self::$domaine."ldap/".$param, false, $context);
        return json_decode($rep);
    }

    public function list($filter, $attributes = ['*'], $limit = 100){
        $context = stream_context_create([
            'ssl' => [
                // Désactiver la vérification du certificat SSL
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);

        $param = bin2hex(json_encode(['filter'=> $filter, 'attributes' => $attributes, 'limit' => $limit]));
        $rep = file_get_contents(self::$domaine."ldap/".$param, false, $context);
        return  json_decode($rep);
    }

    
    private function reformatValues($rep){
        if ($rep->count ==0){
            return null;
        }
        if ($rep->count ==1){
            $fieldName = "0";
            return $rep->$fieldName;
        }
        $tab = [];
        for($index = 0 ; $index< $rep->count; $index++){
            $tab[] = $rep->$index;
        }
        return $tab;

    }

    private  function reformatEntries($rep){
        $obj = new \stdClass();
        for($index = 0 ; $index< $rep->count; $index++){
            $fieldName = $rep->$index;
            $tmp = $rep->$fieldName;
            $obj->$fieldName = $this->reformatValues($tmp);
        }
        return $obj;
    }

    public function reformat($rep){
        $tab = [];
        if ( is_null($rep)  ) return $tab;
        for($index = 0 ; $index< $rep->count; $index++){
            $t = $this->reformatEntries($rep->$index);
            $tab[$t->uid] = $t;
        }
        return $tab;
    }


}
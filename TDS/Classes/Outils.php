<?php
namespace TDS;

/**
 * Description of Outils
 *
 * @author olivier
 */
class Outils {

    static public function getIP(){
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR') . '-' . $ipaddress;
        }
        return $ipaddress;
    }

    /**
     * 
     * @param string   $className
     * @param string[] $filterList
     * @return array
     * 
     * récupère tous les éléments d'une classe dans la base de données
     * pour peu que num>0
     * $filterList est un tableau qui sera ajouté à la clause WHERE 
     * séparés par des AND
     */
/*        
    static public function getClassList($className, $filterList=[], $order = 'ordre'){
        
        $filterString = "";
        foreach($filterList as $filter){
            $filterString = '
             AND ('.$filter.')';
        }

        $objList = getAll("
            SELECT * 
            FROM ".strtolower($className)."
            WHERE num >0
            {$filterString}
            ORDER by {$order}
        ");
        $classList = [];
        
        foreach($objList as $obj){
            $classList[]=new $className($obj);
        }
        return $classList;
    }

    static public function load($tableName, $id){
        return getOne('
            SELECT *
            FROM '. $tableName.'
            WHERE num = '.$id.'    
        ');
    }
*/    
    static public function toUTF8Array($array){
        $res = $array;
        foreach($array as $key => $value){
            $res[$key]= self::toUTF8($value);
        }
        return $res;
    }
    
    static public function toUTF8Obj($obj){
        $res = $obj;
        foreach($obj as $key => $value){
            $res->$key = self::toUTF8($value);
        }
        return $res;
    }
    
    static public function toUTF8($elm){
        if (is_string($elm)){
            return utf8_encode ($elm);;
        }
        if (is_array($elm)){
            return self::toUTF8Array($elm);
        }
        if (is_object($elm)){
            return self::toUTF8Obj($elm);
        }
        return $elm;
    }

    
    static public function fromUTF8Array($array){
        $res = $array;
        foreach($array as $key => $value){
            $res[$key]= self::fromUTF8($value);
        }
        return $res;
    }
    
    static public function fromUTF8Obj($obj){
        $res = $obj;
        foreach($obj as $key => $value){
            $res->$key = self::fromUTF8($value);
        }
        return $res;
    }
    
    static public function fromUTF8($elm){
        if (is_string($elm)){
            return utf8_decode ($elm);;
        }
        if (is_array($elm)){
            return self::fromUTF8Array($elm);
        }
        if (is_object($elm)){
            return self::fromUTF8Obj($elm);
        }
        return $elm;
    }
}
    
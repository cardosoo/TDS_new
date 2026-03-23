<?php
namespace TDS;

use stdClass;
use TDS\Model\Field;

class Query {

    protected $sql = "";
    protected $entityList = [];
    protected $tableList = [];
    protected $select = [];
    protected $dbName;
    protected $alias;
    protected $r;
    protected $nRows;
    protected $nFields;
    
    /**
     * Undocumented function
     *
     * @param string $entityName
     * @param string $alias
     * @param boolean $hydrate true|false|array of properties to hybridate 
      */
    function __construct($entityName, $alias, $hydrate=true){
       
        $alias ??= $entityName;
        $alias = \strtolower($alias);
        $dbName = $entityName::dbName;

        $this->alias = $alias;
        $this->dbName = $dbName;

        $this->entityList[$alias]=$entityName;
        $this->tableList[$dbName]=$entityName;
        $this->sql = "";

        $this->addSelect($entityName, $alias, $hydrate); 
    }

    private function addSelect($entityName, $alias, $hydrate){
        if ($hydrate === false) return;

        $alias = \strtolower($alias);
        $idName = $entityName::idName;

        $this->select[] = "{$alias}.{$idName} AS {$alias}_{$idName}";
//        $this->select[] = "{$alias}.actif AS {$alias}_actif";
        foreach($entityName::entityDef as $name => $def){
            if ( ($hydrate === true) || in_array($name, $hydrate)){
                if ( ! in_array($def['type'], Field::NOT_FIELD )){
                    $this->select[] = "{$alias}.{$def['dbName']} AS {$alias}_{$def['dbName']}";
                }
            }
        }
    }

    public static function create($entityName, $alias, $hydrate=true){
        $new = new static($entityName, $alias, $hydrate);
        return $new;
    }
    
    /**
     * Undocumented function
     * tout cela aurait besoin d'être refactoré mais c'est pas mal
     * $link est de la forme alias.field  où Field est exprimé dans la terminologie du modèle 
     * 
     * @param string $link
     * @param string $alias
     * @param string $hybridate true|false|array of properties to hybridate
     * @return Query
     */
    function join($link, string $alias, $hydrate=true){
        
        list($a,$l) = explode('.',$link);
        $a = \strtolower($a);

        $sourceEntityName = $this->entityList[$a];
        $entityDef = $sourceEntityName::entityDef[$l];
        
        if ($entityDef['type'] == Field::ONETOONE){
            $destinationEntityName = $entityDef['targetEntity'];  
            $sourceId = $sourceEntityName::idName;
            $destinationId = $destinationEntityName::idName;
            $dbName = $destinationEntityName::dbName;
        } elseif ($entityDef['type'] == Field::ONETOMANY) {
            $destinationEntityName = $entityDef['targetEntity'];  
            $destinationId = $destinationEntityName::idName;
            $sourceId = $entityDef['mappedBy'];
            $dbName = $entityDef['dbName'];
        } elseif ($entityDef['type'] == Field::MANYTOONE) {
            $destinationEntityName = $entityDef['sourceEntity'];  
            $destinationId = $entityDef['mappedBy'];
            $sourceId = $sourceEntityName::idName;
            $dbName = $destinationEntityName::dbName;
        } elseif ($entityDef['type'] == Field::MANYTOMANY) {
            $destinationEntityName = $entityDef['joinTable'];  
            $destinationId = $entityDef['joinColumn'];
            $sourceId = $sourceEntityName::idName;
            $dbName = $destinationEntityName::dbName;
        } else {
            die('Je ne veux pas être là ');
        }
        $alias ??= $destinationEntityName;
        $alias = \strtolower($alias);
        $this->sql .= "LEFT JOIN {$dbName} as {$alias} on {$a}.{$sourceId} = {$alias}.{$destinationId}\n";
        $this->entityList[$alias]=$destinationEntityName;
        $this->tableList[$dbName]=$destinationEntityName;
        
        $this->addSelect($destinationEntityName, $alias, $hydrate);

        return $this;
    }

    function getSQL(){
        $sql ="SELECT \n    ".implode(",\n    ",$this->select)."\nFROM {$this->dbName} AS {$this->alias} \n".$this->sql;
        return $sql;
    }

    function addSQL($sql){
        $this->sql .= $sql;
        return $this;
    }

    private function hydrate($tab){
        $r = [];
        foreach ($tab as $entityName => $elmList){
            foreach($elmList as $alias =>$elm){
                $r[$alias] = \TDS\Table::cachedObject($entityName, $elm);
            }
        }
// var_dump($r);        
        return $r;
    }



    private function prepareHydratation($i){
        $dRow = pg_fetch_row($this->r,$i);
        $tab = [];
        for ($iFields = 0; $iFields < $this->nFields; $iFields++){
            $name = pg_field_name($this->r,$iFields);
            list($alias, $field) = explode('_', $name, 2);
            $entityName = $this->entityList[$alias];      
            if (! isset($tab[$entityName])){
                $tab[$entityName]= [];
            };
            if (! isset($tab[$entityName][$alias]) ){
                $tab[$entityName][$alias]= new stdClass();
            }
            $tab[$entityName][$alias]->$field = $dRow[$iFields];
        }
        return $tab;
    }


    private function asObj($i){
        $dRow = pg_fetch_row($this->r,$i);
        $obj = new stdClass();
        for ($iFields = 0; $iFields < $this->nFields; $iFields++){
            $name = pg_field_name($this->r,$iFields);
            list($alias, $field) = explode('_', $name, 2);
            $entityName = $this->entityList[$alias];      
            $obj->$field = $dRow[$iFields];
        }
        $r = new $entityName($obj);
        return $r->__org__;
    }


    function exec($fullHydrate = true){
        $app = \TDS\App::get();
        $sql = $this->getSQL();
        $this->r = $app::$db->query($sql);
        $this->nRows=pg_num_rows($this->r);        
        $this->nFields=pg_num_fields($this->r);

        $res=[];
        for ($i=0;$i<$this->nRows;$i++){
            if ($fullHydrate){
                $res[$i] = $this->hydrate($this->prepareHydratation($i));
            } else {
                $res[$i]= $this->asObj($i);
            }
        }
        return $res;
    }

 
    function getOne($fullHydrate=true){
        $rep = $this->exec($fullHydrate);
        return empty($rep)?null:$rep[0][$this->alias];
    }

    function test($i){
    }


    function __get($name){
        // Ici cela meriterait d'ajouter des vérification pour être certain que les champs existent
        list($alias, $field) = explode('_', $name, 2);
        $alias = \strtolower($alias);
        $field = strtolower($field);

        $entityName = $this->entityList[$alias];
        if ($field == "id"){
            $field = $entityName::idName;
        } else {
            $field = $entityName::entityDef[$field]['dbName'];
        }
        return "{$alias}.{$field}";
    }

    
}
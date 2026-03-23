<?php
namespace TDS\Model;

use TDS\App;

class Model{
    public static $appName;
    public static $parentApp = null;
    public static $idName = "num";
    private static array $entityList = [];
    private static array $mocodoList = [];

    public static function setMocodo($mocodoList){
        self::$mocodoList = $mocodoList;
    }

    public static function getEntityList(){

        return self::$entityList;
    }    

    public static function addEntity(Entity $entity){
        self::$entityList[$entity->getName()]=$entity;
        return $entity;
    }

    public static function removeEntity(Entity $entity){
        unset( self::$entityList[ $entity->getName()] );
    }

    public static function dump(){
        var_dump(self::$entityList);
    }

    private static function buildModel(string $entityName, Entity $entity, string $namespace){
/*
var_dump([
    'entityNme' => $entityName,
    'namespace' => $namespace,
]);        
*/  
    
        $app = \TDS\App::get();
        $className = $entity->getModelName();
        $basePath = $app::$pathList['base'];
        // echo "{$entityName} <--> {$className}\n";


        $fileName = "{$basePath}/Model/{$className}.php"; 
        file_put_contents($fileName, $entity->buildForGhost());
        $fileName = "{$basePath}/Model/{$entityName}.php";
        if ( ! file_exists($fileName) ) {
            file_put_contents($fileName, $entity->buildForModel($namespace));
        }
        
        $fileName = "{$basePath}/twig/templates/CRUD/{$className}.html.twig"; 
        file_put_contents($fileName, $entity->buildForGhostTwig());
        
        $appName = $app::$appName;
        $fileName = "{$basePath}/twig/templates/CRUD/{$appName}_{$entityName}.html.twig"; 
        if ( ! file_exists($fileName) ) {
            file_put_contents($fileName, $entity->buildForModelTwig());
        }
    }

    public static function build(string $namespace){
        foreach(self::$entityList as $entityName => $entity){
            self::buildModel($entityName, $entity, $namespace);
        } 
    }

    public static function getSQL(){
        $sql = [];
        foreach(self::$entityList as $entityName => $entity ){
var_dump($entityName);            
            $sql[] = $entity->getSQL();  
        }
        return join("\n", $sql);

    }

    public static function getMocodo(){
        $mocodo = [];
        foreach(self::$mocodoList as $moc){
            $mo = [];
            foreach($moc as $entity ){
                if (is_string($entity)){
                    $mo[] = ":";  
                } else {
                    $mo[] = $entity->getMocodo();  
                }
            }
            $mocodo[] = join("\n", $mo);
        }
        return join("\n\n", $mocodo);

    }
    
}

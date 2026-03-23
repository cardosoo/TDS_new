<?php

namespace TDS;

use Exception;
use stdClass;
use TDS\Model\Model;
use TDS\Model\Entity;
use TDS\Model\Field;
use TDS\Model\OneToOne;
use TDS\Model\OneToMany;
use TDS\Model\ManyToMany;

/**
 * TDSTable est la classe générique qui permet de gérer les Entités du modèle.
 * C'est elle qui gère la pseudo-couche d'abstraction en faisant le lien 
 * entre le modèle tel que défini et la base de données.
 * 
 * Elle permet de lire et d'écrire des objets depuis ou dans la base de données 
 * en gérant les laisons avec les autres éléments.
 *
 * @author olivier
 * 
 * 
 * 
 */

abstract class Table {

    public static array $loadedEntity = [];

    /**
     * Constructeur 
     * 
     * permet de créer une entité à partir d'un objet qui est directement issu
     * de la lecture de la base de données.
     * 
     * La constante entityDef qui est définie dans l'entité via le modèle
     * est utiilisée pour convertir les champs de la base de données en
     * les champs du modèle.
     * 
     * 
     * @param \stdClass $obj
     *
     * O.C. 14/01/2021 - version initiale ok
     */
    public function __construct(\stdClass $obj = null) {
        $entityName = get_called_class();
        if (is_null($obj) ){
            $obj = new stdClass();
            $obj->{$entityName::idName}=0;
            foreach ($entityName::entityDef as $name => $entityDef) {
                if ($entityDef['type'] === Field::ONETOMANY){  //
                    $obj->{$entityDef['dbName']} = 0;
                } elseif ($entityDef['type'] === Field::ONETOONE){
                    //toDo est-ce qu'il ne faut pas faire quelque chose ici ?
                } elseif ($entityDef['type'] === Field::MANYTOONE){  // 
                    //toDo on laisse comme cela pour que le chargement se fasse au moment des besoins
                } elseif ($entityDef['type'] === Field::MANYTOMANY){
                    //toDo on laisse comme cela pour que le chargement se fasse au moment des besoins
                } else {
                    $obj->{$entityDef['dbName']} = $entityDef['default'];
                    $fName = "__{$entityDef['dbName']}";
                }
            }
        }

//        if (!is_null($obj)) {
        $this->id = $obj->{$entityName::idName};
        $this->__org__['id'] = $this->id;
        foreach ($entityName::entityDef as $name => $entityDef) {
            if ($entityDef['type'] === Field::ONETOMANY){  //
                if (isset($obj->{$entityDef['dbName']})){ 
                    $this->{'__'.$name} = $obj->{$entityDef['dbName']};
                    $this->__org__[$name]=$obj->{$entityDef['dbName']};
                }
            } elseif ($entityDef['type'] === Field::ONETOONE){
                //toDo est-ce qu'il ne faut pas faire quelque chose ici ?
            } elseif ($entityDef['type'] === Field::MANYTOONE){  // 
                //toDo on laisse comme cela pour que le chargement se fasse au moment des besoins
            } elseif ($entityDef['type'] === Field::MANYTOMANY){
                //toDo on laisse comme cela pour que le chargement se fasse au moment des besoins
            } else {
                $dbName = $entityDef['dbName'];
                // ici il faut tester si la variable existe tout court !
                // mais isset() renvoie false si la variable est nulle aussi !
                //if (isset($obj->$dbName) || is_null($obj->$dbName)) {
                if (property_exists($obj, $dbName) ){ 
                    $value =  $obj->$dbName ?? $entityDef['default'];
                    $this->$name = $entityDef['type']=== Field::BOOL ? ($value === 't'? true :  false) : $value;
                    $this->__org__[$name] = $this->$name;
                    $this->__status__ = Entity::SYNCH;
                } else {
                    unset($this->$name);
                    unset($this->__org__[$name]);
                }
            }
        }
//        }
    }



    public static function getLoadedEntity(array $state = [Entity::NEW, Entity::SYNCH, Entity::MODIF, Entity::SUPR, Entity::SUPV] ){
        $r = [];
        foreach (self::$loadedEntity as $entityName => $entityList){
            foreach($entityList as $id => $entity) {
                if (in_array($entity->__status__, $state)){
                    $r[]=$entity;
                }
            }
        }
        return $r;
    }

    /**
     * 0.C. 26_02_2021 Je ne sais pas quand cette fonction a été revue
     * pour la dernière fois, mais je ne suis pas certain qu'elle fasse
     * ce qu'il faut qu'elle fasse.
     *
     * @return void
     */
    public function save(){
        $app = \TDS\App::get();
        $entityName = get_called_class();
        $mainDbName = $entityName::dbName; 
        $idName = $entityName::idName;
    
        if ($this->id === 0){ // alors c'est une création
            $this->id = intval($app::$db->getOne("
                SELECT max(\"{$idName}\")
                FROM \"{$mainDbName}\"
            ")->max)+1;
            $app::$db->h_query("
                INSERT INTO \"{$mainDbName}\" (\"$idName\") VALUES ($this->id);
            ");
        }

        $set = [];
        foreach ($entityName::entityDef as $name => $entityDef) {
            if ($entityDef['type'] === Field::ONETOMANY){  //
                $value = $this->{"__{$name}"};
                $dbName = $entityDef['dbName'];
                if ( $value !== intval($this->__org__[$name]) ){
                    $set[]= "\"{$dbName}\" = $value";
                }
            } elseif ($entityDef['type'] === Field::ONETOONE){
                //toDo est-ce qu'il ne faut pas faire quelque chose ici ?
            } elseif ($entityDef['type'] === Field::MANYTOONE){  // 
                //toDo on laisse comme cela pour que le chargement se fasse au moment des besoins
            } elseif ($entityDef['type'] === Field::MANYTOMANY){
                //toDo on laisse comme cela pour que le chargement se fasse au moment des besoins
            } else { // C'est un champ ordinaire ici...
                if ($this->$name !== $this->__org__[$name]) {
                    $dbName = $entityDef['dbName'];
                    $value = $entityDef['type']=== Field::BOOL ? ($this->$name ? 't' : 'f'):$this->$name;
                    $value = \pg_escape_string($app::$db->conn, $value);
                    $value = is_string($value) ? "'{$value}'" : $value;
                    $set[] = "\"{$dbName}\" = {$value}";
                }
            }
        }

        if (count($set)>0){
            $join = \join(", ", $set);
            $app::$db->h_query("
UPDATE \"{$mainDbName}\" 
SET 
    {$join}
WHERE {$idName} = {$this->id};
            ");
        }
    }

    public function delete(){
        $app = \TDS\App::get();
        $entityName = \get_called_class();
        $dbName = $entityName::dbName; 
        $idName = $entityName::idName;
    $this->__status__ = Entity::SUPV;
        $app::$db->h_query("
DELETE FROM \"{$dbName}\"
WHERE {$idName} = {$this->id};
        ");
    }

    /**
     * static getEntityName
     *
     * Cette fonction renvoie le nom la classe appelante et fait l'initialisation
     * de la variable $loadedEntity qui convient.
     * 
     * @return string
     * 
     * O.C. 15/01/2021 - version initiale ok
     */
    protected static function getEntityName(): string{
        $entityName = "\\".get_called_class();

        // si aucun objet de cette entité n'a été préalablement alors on
        // initialise le tableau destiné à les recevoir.
        if ( ! isset(self::$loadedEntity[$entityName] )){
            self::$loadedEntity[$entityName] = [];
        }
        return $entityName;
    }

    /**
     *  function isLoaded
     * 
     * indique si l'objet de la classe $entityName et  d'id est déjà chargé 
     *
     * @param string $entityName
     * @param integer $id
     * @return bool
     */
    public static function isLoaded(string $entityName, int|null $id) : bool {
        if (is_null($id)){
            return false;
        }

        return isset(self::$loadedEntity[$entityName][$id] );
    }

    // renvoie l'objet caché ou place l'objet en cache
    public static function cachedObject($entityName, $obj){
        $entityId = $entityName::idName;
        $id = $obj->$entityId;
        if ( ! self::isLoaded($entityName, $id)){
// var_dump("put in cache {$entityName} - {$id}");            
            self::$loadedEntity[$entityName][$id] = new $entityName($obj);
        } else {
// var_dump("get from cache {$entityName} - {$id}");            
        }
        return self::$loadedEntity[$entityName][$id];
    }

    public static function cachedObjectList($entityName, $objList){
        $entityList = [];
        foreach($objList as $obj){
            $entityList[] =self::cachedObject($entityName, $obj);
        }
        return $entityList;

    }
    /**
     *  static function loadOneWhere
     *
     * @param string $where
     * @return Entity|null
     * 
     * O.C. 25/01/2021 - version initiale ok
     */
    public static function loadOneWhere(string $where){
        $app = \TDS\App::get();
        $entityName = self::getEntityName();
        $tableName = $entityName::dbName;
        $obj = $app::$db->getOneWhere($tableName, $where);
        if (is_null($obj)) return null;

        return self::cachedObject($entityName, $obj);
    }

    /**
     * Permet de charger un enregistrement depuis la base de données sans charger les 
     * enregistrements liés
     * 
     * @param int $id   => l'identifiant de l'enregistrement à charger
     * @return \className => l'enregistrement chargé
     * 
     * O.C. 14/01/2021 - version initiale ok
     */
    public static function load($id){
        $entityName = self::getEntityName();
        // si l'objet que l'on cherche n'est pas déjà présent dans le tableau
        // des objets chargés alors on l'y mets
        if ( ! self::isLoaded($entityName, $id)){
            $idName = $entityName::idName;
            return self::loadOneWhere("\"{$idName}\" = {$id}");
        }
        return self::$loadedEntity[$entityName][$id];
    }


    /**
     * function loadWhere
     *
     * @param string $where
     * @param array|null $order 
     * @return array
     * 
     * O.C. 15/01/2021 - version initiale ok
     */
    public static function loadWhere(string $where, ?array $order = null, $idPositive = True){
        $app = \TDS\App::get();
        $entityName = self::getEntityName();
        $tableName = $entityName::dbName;
        $order = $order ?? $entityName::ORDER;

        $objList = $app::$db->getWhere($tableName, $where, $order, $idPositive);
        return self::cachedObjectList($entityName, $objList);
    }

    /**
     * loadList :
     * permet de charger l'ensemble des éléments pour les lequels
     * le champ $mappedBy vaut $id
     *
     * @param string $mappedBy
     * @param integer $id
     * @return array
     * 
     * O.C. 15/01/2021 - version initiale ok
     */
    protected static function loadList(string $mappedBy, int $id){
        if ($id==0) return [];
        return self::loadWhere("\"{$mappedBy}\" = {$id}");
    }


    public function getCrudEditLink($linkText){
        $app = \TDS\App::get();
        $entityName = self::getEntityName();
        $tmp = \explode('\\',$entityName);
        $eName = \end( $tmp);
        $appName = $app::$appName;
        return "<a href='/{$appName}/CRUD/{$eName}/{$this->id}'>{$linkText}</a>";
    }

    public function getCrudCreateLink($linkText){
        $app = \TDS\App::get();
        $entityName = self::getEntityName();
        $tmp = \explode('\\',$entityName);
        $eName = \end( $tmp);
        $appName = $app::$appName;

        return "<a href='/{$appName}/CRUD/{$eName}'>{$linkText}</a>";
    }

    public function getGeneric($withCrudLink = false){
        $entityName = self::getEntityName();
 
        // traitement spécial pour les relations ManyToMany
        if (\is_subclass_of($entityName, '\TDS\ManyToMany')){
            $left  = $this->__get($entityName::__LEFT__) ;
            $right = $this->__get($entityName::__RIGHT__);

            $leftGen = is_null($left)?"absent":$left->getGeneric($withCrudLink);
            $rightGen = is_null($right)?"absent":$right->getGeneric($withCrudLink);
            
            return $leftGen.' <-> '.$rightGen;
        }
        // Ici c'est le cas général
        $genericArray = $entityName::GENERIC ?? $entityName::SEARCH; // Si le générique n'est pas défini alors on utilise le search
        $rep = [];
        foreach($genericArray as $gen){
            $rep[]=$this->__get($gen);
        }
        $gen = implode(" ", $rep);
        if (empty(trim($gen))){
            $gen = '-??-';
        }
        if ( $this->__isset('actif') && ! $this->__get('actif')){
            $gen = "<del>{$gen}</del>";
        }
        if ($withCrudLink){
            return $this->getCrudEditLink($gen);
        }
        return $gen;
    }

    function getGenericWithLink(){
        return $this->getGeneric();
    }

    /**
     * setter pour TDS\Table
     *
     * @param string $name
     * @param mixed $value
     * 
     * O.C  15/01/2021 - toDo -> ajout de ce qu'il faut pour pouvoir ajouter un Lien 
     * O.C. 15/01/2021 - ok -> ajout de la prise en compte du statut de l'enregistrement de l'entité
     * O.C. 14/01/2021 - ok -> mais à compléter avec des vérification
    */
    public function __set(string $name, $value){
        // ici il faudrait faire les vérifications pour s'assurer que la valeur à mettre est conforme 
        // aux contraintes
        // 
        $this->__status__ = Entity::MODIF;
        $entityName = get_called_class();
        if (!isset($entityName::entityDef[$name])){ //ok 
            throw new \Exception("Propriétée {$name} invalide !");
        }
        $def = $entityName::entityDef[$name];

        if (Field::ONETOMANY ===$def['type']){ // toDo il faut le faire ça non ? 
            $fieldName = "__{$name}";
            $this->$fieldName = \intval($value);
            $this->__get($name);
            // ici il faudrait recharger les relations de l'entité père ou alors l'invalider, mais pour l'instant je ne sais pas faire...
            return; // est-ce qu'il ne faudrait pas renvoyer $value ?          
        } 

        $this->$name = $value;
    }

    /**
     * Cette fonction qui ne demande qu'à être surchargée permet d'avoir
     * aussi aux paramètres qui sont passés via le formulaire CRUD
     * juste avant qu'on fasse un update de l'entité.
     * On peut alors en profiter pour modiffer ou mettre à jour les paramètres dans $_PATCH['form']
     * pour par exemple réaliser un calcul spécifique pour un ou plusieurs paramètres
     * ou alors faire une action particulière, comme participer à un historique par exemple
     */
    public function CRUD_beforeUpdate(){
        global $_PATCH;
    }

    /**
     * Cette fonction qui ne demande qu'à être surchargée permet de modifier l'entité elle même
     * juste avant la fin du processus de création.
     * On peut par exemple initialiser des paramètres qui ne le serait pas par défaut :
     */
    public function CRUD_beforeCreate(){
    }

    /**
     * unsetter pour TDS\Table
     * 
     * @param string $name
     * 
     * O.C  15/01/2021 - toDo -> ajout de ce qu'il faut pour pouvoir supprimer un Lien 
     */
    public function __unset(string $name){        
    }


    /**
     * getter pour TDS\Table
     *
     * @param string $nom
     * @return mixed
     * 
     * O.C. 14/01/2021 - ok
     */
    public function __get($name){

        if (isset($this->$name)){ // ok
            return $this->$name;
        }

        $entityName = '\\'.get_called_class();
        if (!isset($entityName::entityDef[$name])){ //ok 
            throw new \Exception("Propriété {$name} invalide !");
        }
        $ed = $entityName::entityDef[$name];
        $type = $ed['type'];
//var_dump("Alors là...{$type} - {$entityName} - {$name}");        
        if (Field::ONETOMANY === $type){ //ok
            if ($this->{'__'.$name}>0 ){ // pour imposer que les liens avec des id <0 ne sont pas autorisés.
                $this->$name = $ed['targetEntity']::load($this->{'__'.$name});
            } else {
                $this->$name = null;
            }
           return $this->$name;
        }
        if (Field::MANYTOONE === $type){ //ok  
            $this->$name = $ed['sourceEntity']::loadList($ed['mappedBy'], $this->id);
// var_dump("Ici je suis bien...{$entityName} - {$name} - {$this->code}");          
            return $this->$name;
        }
        if (Field::MANYTOMANY === $type){ // ok
            $this->$name = $ed['joinTable']::loadList($ed['joinColumn'], $this->id);
            return $this->$name;
        }
        if (Field::ONETOONE === $type){ // il faut vérifier que cela fonctionne aussi avec une association bi-directionnelle
            // $this->$name = $ed['targetEntity']::load($this->id);
//var_dump("Ici ONETOONE..{$ed['targetEntity']} - {$ed['targetId']} - {$this->id}");          
//            $this->$name = $ed['targetEntity']::loadOneWhere("{$ed['targetId']} = {$this->id}");
            $this->$name = $ed['targetEntity']::load($this->id);
            return $this->$name;    
        }
        throw new \Exception("Propriété {$name} invalide !");
    }
    
    /**
     * Undocumented function
     *
     * @param string $name
     * @return boolean
     * 
     * O.C. 27/01/2021 - version initiale à faire
     */
     public function __isset($name){
        if (substr($name,0,2) === "__") return false; // pour faire en sorte que twig ne puisse pas accéder aux propriétés construites
        if (array_key_exists($name, get_class_vars(get_called_class()))) {
            return true;
            $rp = new \ReflectionProperty(\get_called_class(), $name);
            $rp->setAccessible(true);
            return $rp->isInitialized($this);
        }
        return false;
    }


    /**
     * renvoie un tableau avec l'ensemble des associations
     * liées à l'entité classées par catégories
     * chaque entrée renvoie le entityDef du champ correspondant
     * 
     * 
     * @return array
     */
    public static function getRelationList(){
        $entityName = get_called_class();
        $res = [
            Field::ONETOONE    => [],
            Field::ONETOMANY   => [],
            Field::MANYTOONE   => [],
            Field::MANYTOMANY  => [],
        ];
        foreach($entityName::entityDef as $name => $def){
            if (in_array($def['type'], [Field::ONETOONE, Field::ONETOMANY, Field::MANYTOONE, Field::MANYTOMANY])){
                $res[$def['type']][$name]=$def;
            }
       }
       return $res;
    }

    /**
     * recherche les éléments en utilisant les champs de recherche définis dans le modèle
     */
    public static function searchByModel($what){

        $entityName = get_called_class();

        if (empty($entityName::SEARCH)){
            return null;
        }

        $q = new Query($entityName, 'E');
        $sList = [];
        foreach($entityName::SEARCH as $s){
            $c = "E_{$s}";
            $sList[$s] =$q->{$c};
        }
        $find = join(" || ' ' || ", $sList);
        $q->addSQL(" WHERE unaccent({$find}) ILIKE unaccent('%{$what}%') ");
        $q->addSQL(" AND {$q->E_id} > 0 ; ");
        $res = $q->exec();
        return $res;
    }

    /**
     * recherche les éléments en utilisant tous les champs du modèle
     */
    public static function searchByFullModel($what){

        $entityName = get_called_class();

        $search = [];
        foreach($entityName::entityDef as $fieldName => $field){
            if (in_array($field['type'], [Field::STRING, Field::TEXT] )){
                $search[]=$fieldName;
            }
        }

        $q = new Query($entityName, 'E');
        $sList = [];
        foreach($search as $s){
            $c = "E_{$s}";
            $sList[$s] =$q->{$c};
        }

        $find = join(" || ' ' || ", $sList);
        $q->addSQL(" WHERE unaccent({$find}) ILIKE unaccent('%{$what}%') ");
        $q->addSQL(" AND {$q->E_id} > 0 ; ");
        $res = $q->exec();
        return $res;
    }

    /**
     * getAllDocumentsFromId
     * 
     * renvoie la liste des documents de l'entité sous forme d'un tableau de Document 
     * l'index du tableau est le timestamp de la dernière modification
     * 
     *
     * @param  int $id
     * @return \TDS\Document[] 
     */
    public function getDocumentList(){
        $entityName = get_called_class();
        $className =  explode('\\', $entityName);
        $className = end($className);

        $dir = Document::getDocumentPath($className)."/{$this->id}/*";
        
        $glob = glob($dir);
        $dList = [];
        foreach($glob as $g){
            if (!is_dir($g)){
                $dList[]=['basename' => basename($g), 't' => filemtime($g) ];
            }
        }
        krsort($dList);
        
        $docList = [];
        foreach($dList as $d){
            $docList[] = new Document($className, $this->id, $d['basename'], $d['t']);
        }
        
        return $docList;
    }


}
<?php

namespace TDS;

use TDS\Model\Entity;
use TDS\Model\Field;

class Crud {

    public static $changeList = [];



    public static function home(){
        $app = \TDS\App::get();


        $app::$cmpl['withMathjax'] = false;
        $app::$cmpl['withMarkdown'] = false;
        $app::$cmpl['withJQuery'] = true;
        $app::$cmpl['withKnockout'] = false;
        $app::$cmpl['withDataTables'] = false;
        $app::$cmpl['withChartJS'] = false;
        $app::$cmpl['withBokeh'] = false;
  
        include("{$app::$pathList['base']}/model.php");


        $entityList = \TDS\Model\Model::getEntityList();
        ksort($entityList, SORT_NATURAL +  SORT_FLAG_CASE);
        echo $app::$viewer->render( "CRUD/home.html.twig", ['entityList' => $entityList]);
}

    public static function createManyToMany($entityName, $from, $fromId){
        $app = \TDS\App::get();

        $fullEntityName = $app::NS($entityName);
        $elm = new $fullEntityName();
        $elm->actif = true;
        $elm->$from = $fromId;
        self::create($entityName, $elm);
    }

    public static function createEntity($entityName){
        $app = \TDS\App::get();

        $fullEntityName = $app::NS($entityName);
        $elm = new $fullEntityName();
        self::create($entityName, $elm);
    }
    
    private static function create($entityName, $elm){
        $app = \TDS\App::get();

        $elm->CRUD_beforeCreate(); // permet d'initialiser des champs qui auraient besoin d'une initialisation particulière
        $fullEntityName = $app::NS($entityName);
        $app::$cmpl['withMathjax'] = false;
        $app::$cmpl['withMarkdown'] = false;
        $app::$cmpl['withJQuery'] = true;
        $app::$cmpl['withKnockout'] = false;
        $app::$cmpl['withDataTables'] = false;
        $app::$cmpl['withChartJS'] = false;
        $app::$cmpl['withBokeh'] = false;
  
        $appName = $app::$appName;
        $withVisu = isset($_GET['withVisu']);
    
        echo $app::$viewer->render( "CRUD/{$appName}_{$entityName}.html.twig", [ $entityName => $elm, 'isCreation' => true, 'withVisu' => $withVisu ]);
    }


    /**
     *  Lecture d'une entité
     *  Lorque l'entité n'existe pas alors on propose sa création 
     */
    public static function read($entityName, $id){
        $app = \TDS\App::get();

        $fullEntityName = $app::NS($entityName);
        $elm = $fullEntityName::load($id);

        if (is_null($elm)){
            self::createEntity($entityName);
            return;
        }

        $app::$cmpl['withMathjax'] = false;
        $app::$cmpl['withMarkdown'] = false;
        $app::$cmpl['withJQuery'] = true;
        $app::$cmpl['withKnockout'] = false;
        $app::$cmpl['withDataTables'] = false;
        $app::$cmpl['withChartJS'] = false;
        $app::$cmpl['withBokeh'] = false;

        $appName = $app::$appName;

        $withVisu = isset($_GET['withVisu']);
        echo $app::$viewer->render( "CRUD/{$appName}_{$entityName}.html.twig", [ $entityName => $elm, 'isCreation' => false, 'withVisu' => $withVisu ]);
    } 


    public static function updateManyToMany($entityName, $from, $fromId){
        $app = \TDS\App::get();
        // si on arrive ici c'est forcement que c'est une création
        $fullEntityName = $app::NS($entityName);
        $elm = new $fullEntityName();
        $elm->actif = true;
        $elm->$from = $fromId;
        $elm->CRUD_beforeCreate(); // permet d'initialiser des champs qui auraient besoin d'une initialisation particulière
        self::update($entityName, $elm, -1);
    }


    public static function doCreateEntity($entityName){
        $app = \TDS\App::get();

        $fullEntityName = $app::NS($entityName);
        $elm = new $fullEntityName();
        self::update($entityName, $elm, 0);
    } 

    public static function updateEntity($entityName, $id){
        $app = \TDS\App::get();
        $fullEntityName = $app::NS($entityName);
        $elm = $fullEntityName::load($id);
        self::update($entityName, $elm, "");
    } 

    /**
     * si il s'agit d'une création pour une entité il faut mettre post à 0
     * si il s'agit d'une création pour une association manyToMany, il faut mettre post à -1
     * dans les autres cas il ne faut pas mettre de post ou passé ""
     */
    private static function update($entityName, $elm, $post=""){
        global $_PATCH;

        $app = \TDS\App::get();
        $fullEntityName = $app::NS($entityName);

        $_PATCH = [];
        parse_str(file_get_contents('php://input'), $_PATCH);

        $elm->CRUD_beforeUpdate(); // permet de faire des calculs sur à partir des champs qui ont été transmis via le formulaire
                                   // on pourrait aussi l'utiliser par exemple pour mettre à jour un historique de modification ou je ne sais quoi

        if (isset($_PATCH['field'])){
            foreach ($_PATCH['field'] as $key => $value){
                if ($fullEntityName::entityDef[$key]['type'] == Field::BOOL){
                    $elm->$key = $value=='true'?true:false;
                } else {
                    if ($value === "" ){
                        $value = $fullEntityName::entityDef[$key]['default'];
                    }
                    $elm->$key = $value;
                }
            }
            $elm->save();
        }

        $app::$db->startCapture();
        if (isset($_PATCH['MTM'])){
            $app::$db->startCapture();
            foreach ($_PATCH['MTM'] as $key => $z){
                $e = $_PATCH['MTM_E'][$key];
                $id = $_PATCH['MTM_I'][$key];
                $joinTable = $fullEntityName::entityDef[$e]['joinTable'];
                self::deleteEntity($joinTable, $id);
                // unset($elm->$key[$id]; il ne faut sans doute pas le faire ici...
            }
            $sql = $app::$db->stopCapture(false);
            $app::$db->h_query(\implode(';', $sql));

        }
        echo ("Done".($post===0?$elm->id:$post));

    }

    public static function deleteOneToOne($def, $id){
        $fullEntityName = $def['targetEntity'];
        if (! is_subclass_of($fullEntityName, "\TDS\View" )) {
            self::deleteEntity($fullEntityName, $id);
        }
    }

    public static function deleteEntity($fullEntityName, $id) {
        $app = \TDS\App::get();
        $elm = $fullEntityName::load($id);
        $gen = $elm->getGeneric(true);

        $tmp = \explode('\\',$fullEntityName);
        $entityName = \end( $tmp);
        $appName= $app::$appName;

        if (\is_subclass_of($elm, '\TDS\ManyToMany')){
            self::$changeList[]="Suppression de  l'<a href='/{$appName}/CRUD/{$entityName}/{$elm->id}'>association<a> ({$entityName}) : {$gen}";
        } else {
            self::$changeList[]="Suppression de ({$entityName}) : {$gen} ";
        }

        $relationList = $fullEntityName::getRelationList();
        // oneToOne
        foreach($relationList [Field::ONETOONE] as $name => $def ){
            self::deleteOneToOne($def, $id);
        }
        foreach($relationList [Field::ONETOMANY] as $name => $def ){
            // Ici il n'y a rien à faire a priori...
        }
        // manyToOne
        foreach($relationList [Field::MANYTOONE] as $name => $def ){
            $list = $elm->{$def['inversedBy']};
            foreach($list as $key=> $l){
                $l->{$def['mappedBy']}=0;
                $gen = $l->getGeneric();
                $cName = \get_class($l);
                $tmp = \explode('\\',$cName);
                $eName = \end( $tmp);
                
                $appName = \TDS\App::$appName;
                self::$changeList[]="Suppression du lien <a href='/$appName/CRUD/{$eName}/{}'>{$gen}</a> ($cName)";
//                unset($list[$key]); // O.C. 26/02/2021 Je ne suis pas très suûr qu'il faut faire cela
                                    // il est peut-être préférable de faire en sotre que ce soit la classe Table qui s'occupe de cela non ?
                $l->save();
            }
        }
        // manyToMany
        foreach($relationList [Field::MANYTOMANY] as $name => $def ){
            $list = $elm->{$def['inverseJoinColum']};
            foreach($list as $l){
                self::deleteEntity($def['joinTable'], $l->id);
            }
        }
        $elm->delete();
    }

    public static function delete($entityName, $id){
        $app = \TDS\App::get();

        $_DELETE = [];
        parse_str(file_get_contents('php://input'), $_DELETE);
        
        $confirm  = isset($_DELETE['confirm']);
        
        self::$changeList=[];
        $fullEntityName = $app::NS($entityName);
        $app::$db->startCapture();

        self::deleteEntity($fullEntityName, $id);

        $sql = $app::$db->stopCapture($confirm); // on demande de faire l'execution du sql généré uniquement si confirm est passé en paramètre
        if ($confirm){
            echo "Done";
            return;
        }
        echo ("<ul>\n    <li>");
        echo (implode("</li>\n    <li>", self::$changeList));
        echo ("</li>\n</ul>");
    } 

    /**
     * Pour récupérer la liste des entités qui contienne le terme $_GET['term']
     * Cela est utilisé pour les associtions oneToMany
     *
     * @param [type] $entityName
     * @return void
     */
    public static function listAllJSON($entityName){
        $app = \TDS\App::get();
        $search = \pg_escape_string($app::$db->conn, \filter_input( INPUT_GET, 'term', FILTER_SANITIZE_SPECIAL_CHARS));
//        $entity = "\\".$app::$appName."\\Model\\{$entityName}";
        $entity = $app::NS($entityName);

        $q = new Query($entity, "E");
        // fabrication de la requête pour le WHERE
        $searchArray = $entity::SEARCH;
        $sL = [];
        foreach($searchArray as $s){
            $f = "E_{$s}";
            $sL[] = "unaccent(trim({$q->$f}))";
        }

        $genericArray = $entity::GENERIC ?? $searchArray; // Si le générique n'est pas défini alors on utilise le search
        $orderArray = $entity::ORDER ?? $genericArray;     // Si l'ordre n'est pas défini alors on utilise le générique

        $actif = isset($entity::entityDef['actif']);

        // fabrication de la requête pour le ORDER
        $oL= [];
        foreach($orderArray as $o){
            $f = "E_{$o}";
            $oL[] = $q->$f;
        }

        $searchString = implode(" || ' ' || ", $sL)."   ILIKE unaccent('%{$search}%') ";
        $orderString = implode(", ", $oL);

        // fabrication de la requête et exécution
        $q->addSQL("WHERE {$q->E_id}>0   
        AND ({$searchString})
        ORDER BY {$orderString}
        LIMIT 50
        "); 
        $repList =  $q->exec();

        // fabrication du générique
        $r = [[
            'label' => "--aucun",
            'id' => "0",
        ]];

        // les histoires d'actif
        foreach($repList as $rep){
            $value = [];
            foreach($genericArray as $gen){
                $value[]=$rep['e']->$gen;
            }
            $value = implode(' ', $value);
            $actifClass = $rep['e']->actif?"":"passif";
            $r[]=[
                'label' => $value,
                'id' => "{$rep['e']->id}",
                'actif' => $actif?"{$actifClass}":"",
            ];
        }
        // envoie de la réponse en json
        echo(json_encode($r));
    }


    /**
     * Pour récupérer la liste des entités qui contienne le terme $_GET['term']
     * Cela est utilisé pour les associtions oneToMany
     *
     * @param [type] $entityName
     * @return void
     */
    public static function listAll($entityName){
        $app = \TDS\App::get();
        $entityNS = $app::NS($entityName);
        $list = $entityNS::loadWhere('id >0');
        echo $app::$viewer->render( "CRUD/listAll.html.twig", [ 'entityName' => $entityName, 'list' => $list ]);
    }

  
  public static function entity_history(){
        $app = \TDS\App::get();

        $args = [
            'E' => FILTER_UNSAFE_RAW,
            'id' => FILTER_VALIDATE_INT,
        ];
        
        
        $propList = filter_input_array(INPUT_POST, $args);
        $entityName = $propList['E'];
        $id = $propList['id'];

        $updateList = \TDS\Historique::entity($entityName, $id);
        echo $app::$viewer->render( "CRUD/entity_history.html.twig", [ 'entityName' => $entityName, 'updateList' =>$updateList ]);
    }
 

    public static function entity_field_history(){
        $app = \TDS\App::get();

        $args = [
            'E' => FILTER_UNSAFE_RAW,
            'F' => FILTER_UNSAFE_RAW,
            'id' => FILTER_VALIDATE_INT,
        ];
        
        $propList = filter_input_array(INPUT_POST, $args);
        $entityName = $propList['E'];
        $fieldName = $propList['F'];
        $id = $propList['id'];

        $updateList = \TDS\Historique::entity_field($entityName, $fieldName, $id) ;
        echo $app::$viewer->render( "CRUD/entity_field_history.html.twig", [ 'fieldName' => $fieldName, 'updateList' =>$updateList ]);
               
    }

    public static function entity_links(){
        $app = \TDS\App::get();

        $args = [
            'E' => FILTER_UNSAFE_RAW,
            'id' => FILTER_VALIDATE_INT,
            'L' => FILTER_UNSAFE_RAW,
        ];
        
        $propList = filter_input_array(INPUT_POST, $args);
        $entityName = $propList['E'];
        $linkName = $propList['L'];
        $id = $propList['id'];

        $updateList = \TDS\Historique::entity_links($entityName, $linkName, $id);
        echo $app::$viewer->render( "CRUD/entity_links_history.html.twig", [ 'linkName' => $linkName, 'updateList' =>$updateList ]);
    }

    public static function entity_manyToMany_history(){
        $app = \TDS\App::get();

        $args = [
            'E' => FILTER_UNSAFE_RAW,
            'id' => FILTER_VALIDATE_INT,
        ];
        
        $propList = filter_input_array(INPUT_POST, $args);
        $entityName = $propList['E'];
        $id = $propList['id'];

        $updateList = \TDS\Historique::entity_manyToMany($entityName, $id);
        echo $app::$viewer->render( "CRUD/entity_manyToMany_history.html.twig", [ 'entityName' => $entityName, 'updateList' =>$updateList ]);
    }


}
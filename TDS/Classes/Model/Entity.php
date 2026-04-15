<?php
namespace TDS\Model;

class Entity {

    // les différents états possibles pour les enregistremets du modèle
    public const NEW   = 0; // nouveau : l'enregistrement est nouveau et n'existe pas encore dans la base de données
    public const SYNCH = 1; // synchronisé : l'enregistrement est synchonisé avec la base de données
    public const MODIF = 2; // modifié : l'enregistrement a été modifié par rapport à son état dans la base de données
    public const SUPV  = 3; // suppression virtuelle : l'enregistrement a été supprimé (mais n'est pas encore supprimé dans la base de données)
    public const SUPR  = 4; // suppression réelle : l'enregistremnet a été supprimé et est aussi supprimé dans la base de données

    protected string $namespace;
    protected string $name; 
    protected string $twigName;
    protected string $dbName;
    protected string $idName;
    protected ?array  $search;
    protected ?array  $generic;
    protected ?array  $order;


    protected array $fieldList = [];      // la liste des champs qui composent l'entité
    protected array $linkedList = [];     // la liste des liens vers cette entité
    protected array $oneToOneList = [];   // la liste des liens oneToOne vers cette entité

    protected int $fragmentNum = 0;


    // Définition des constantes pour les options de champs
    const dbName = 'dbName' ;
    const idName = 'idName' ;
    const twigName = 'twigName' ;
    const search = 'search' ;
    const generic = 'generic';
    const order = 'order';
    
    /**
     * Constructeur de l'entité
     *
     * @param string $name le nom de l'entité
     * @param array  $options les options associées sous forme d'un tableau associatif
     * @return void
     * 
     * voici la liste des différentes options possibles
     * 
     * - dbName    : le nom de la table dans la base de données associée
     *               à l'entité. Par défaut c'est le nom de l'entité en minuscule
     * - idName    : le nom du champ qui sert d'identifiant unique à la table
     *               par défaut c'est num
     * - twigName  : le nom utilisé par twig pour nommer les choses
     *               par défaut c'est $name
     * - search    : un tableau avec le nom des champs à travers lesquels se font la recherche
     * 
     * - generic   : un tableau avec le nom des champs avec lesquels ont fabrique 
     * 
     * - order     : un tableau avec les clés de tri
     * 
     * - withActif : indique si il doit y avoir l'entrée actif (par défaut true)
     */
    function __construct(string $name, array $options=[]) {
        
        // On fait cela pour récupérer le namespace même lorsque cela vient d'une relation ManyToMany
        // en fait ce n'est pas le namespace qu'on récupère, mais le nom répertoir appelant.
        $this->namespace = 'Model';
        $i = 0;
        while ('Model' === $this->namespace ){
            $backtrace = debug_backtrace();
            $file = $backtrace[$i]['file'];
            $parts = preg_split('#'.DIRECTORY_SEPARATOR.'#', $file);
            $this->namespace = $parts[count($parts)-2];
            $i++;
        }        
        $this->name = $name;
        $this->twigName  = $options['twigName']  ?? strtolower($name);
        $this->dbName  = $options['dbName']  ?? strtolower($name);
        $this->idName  = $options['idName']  ?? Model::$idName;
        $this->search  = $options['search']  ?? null;
        $this->generic = $options['generic'] ?? null;
        $this->order   = $options['order']   ?? null;
        $this->withActif   = $options['withActif']   ?? true;

        if ($this->withActif){
            $this->addField(new Field('actif', Field::BOOL, [Field::default => 'TRUE']), true);
        }
    }



    /**
     * Ajoute un champ à la liste des champs de l'entité
     *
     * @param \TDS\Model\Field $field
     * @return void
     * 
     */
     public function addField(Field $field, $isForced = false){
        $name = $field->getName();
        if ( ! $isForced){
            if ( ! isset($this->search) ){
                if ($field->isTextual()){
                    $this->search = [ $name ];
                }
            }    
        }
        $this->fieldList[$name]=$field;

    }

    public function addTwigFragment(string $fragment){
        $this->fragmentNum++;
        $this->fieldList['__fragment__'.$this->fragmentNum]= $fragment;
    }

    public function getName(){
        return $this->name;
    }

    public function getDbName(){
        return $this->dbName;
    }

    public function getIdName(){
        return $this->idName;
    }

    public function gettwigName(){
        return $this->twigName;
    }


    public function addOneToMany(Entity $targetEntity, array $options = [], $forManyToManyFirst = null){
        $link = new OneToMany($this, $targetEntity, $options, $forManyToManyFirst);
        $this->addField($link);
        $inversedBy = $link->getInversedBy();
        $targetEntity->linkedList[$inversedBy]=$link;
        return $link;
    }

    /**
     * Les options possibles sont :
     * - name -> (default : targetEntity->name) indique le nom de la liaison
     * - birectionnal -> (default : false) indique si l'entité destinatrice doit aussi comporter une trace de la relation
     * - targetId -> (default : 'id' ) indique le nom du champ qui sert pour faire la liaison
     * 
     * @param Entity $targetEntity
     * @param array $options
     * @return void
     */
    public function addOneToOne(Entity $targetEntity, array $options = []){
        $bidirectionnal = $options['bidrectionnal'] ?? false;
        $name = $options['name'] ?? $targetEntity->name;
        $targetId = $options['targetId'] ?? 'id'; // et on en fait quoi ?
        $this->oneToOneList[$name] = [ $targetEntity, $targetId];
        if ($bidirectionnal){  // Je ne suis pas certain que ce qui suit soit suffisant pour faire le bidirectionnel en particulier depuis l'implementation du targetId
            $targetEntity->oneToOneList[$name]= [$this, 'id'];
        }

    }

    /**
     * Fabrique le nom du modèle
     * si prefix est true alors ajoute le nom de l'application parente au début et supprime le _ à la fin.
     */
    public function getModelName($prefix = false){
        if (!$prefix) return "_{$this->name}_";
        if (is_null(Model::$parentApp)) return "_{$this->name}_"; 
        $parentPrefix = Model::$parentApp;
        return "{$parentPrefix}_{$this->name}";
    }

/**
 * Pour construire les modèles...
 */

    protected function buildModelHeaders(){
        $date = new \DateTime();
        $dateStr = $date->format("r");
        $modelName = $this->getModelName();
        $appName = Model::$appName;

        $search = var_export($this->search, true);
        $generic = var_export($this->generic, true);
        $order = var_export($this->order, true);

        $a = "<?php
namespace Model;
use \\TDS\\Model\\Entity;
use \\TDS\\Model\\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : {$modelName}.php
* created : {$dateStr} UTC
*/ 

interface {$modelName}interface_ {
    const dbName = '{$this->dbName}';
    const idName = '{$this->idName}';
    const SEARCH = {$search};
    const GENERIC = {$generic};
    const ORDER = {$order};

    // les définitions de l'entité {$this->name}
    const entityDef = [
";
        $b = "
trait {$modelName} {
    protected int \$id;            
";
        return [$a, $b];
    }

    protected function buildModelFooters(){
        $a =  "
    ]; 
}
";
        $b = "
    protected int \$__status__ = Entity::NEW; 
    protected array \$__org__ = [];
}
";
    return [$a, $b];
    }
   

    protected function buildModelFields(){
        $a = "";
        $b = "";
        foreach($this->fieldList as $field){
            if (is_string($field)) { // alros c'est un fragment 
                // on ne fait rien                 
            } else {
                list($tb, $ta) = $field->buildForModel();
                $a.=$ta;
                $b.=$tb;
            }
        }
        foreach($this->oneToOneList as $fieldName => $target ){
            $targetEntity = $target[0];
            $targetId = $target[1]; 
            $targetClass = '\\'.Model::$appName.'\\Model\\'.$targetEntity->getName();
            $tb = "    protected ?{$targetClass} \${$fieldName};\n";
            $ta = "
        '{$fieldName}' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '{$targetClass}',
            'dbName' => '{$targetEntity->dbName}',
            'twigName' => '{$targetEntity->twigName}',
            'targetId' => '{$targetId}',
        ],    
";
            $a.=$ta;
            $b.=$tb;
        }
        return [$a, $b];
    }

    protected function buildModelLinks(){
        $a = "";
        $b = "";
        if (count($this->linkedList)>0) {
            foreach($this->linkedList as $link ){
                list($tb, $ta) = $link->buildForTargetModel();
                $a.=$ta;
                $b.=$tb;
            }
        }   
        return [$a, $b];
    }

    protected function buildModelInversedLinks(){
        $a = "";
        $b = "";
        if (count($this->linkedList)>0) {
            foreach($this->linkedList as $link ){
                list($tb, $ta) = $link->buildForTargetModel();
                $a.=$ta;
                $b.=$tb;
            }
        }
        return [$a, $b];

    }

    public function buildForGhost(){
        list($a1, $b1) = $this->buildModelHeaders();
        list($a2, $b2) = $this->buildModelFields();
        list($a3, $b3) = $this->buildModelLinks();
//        list($a4, $b4) = $this->buildModelInversedLinks();
        list($a5, $b5) = $this->buildModelFooters();

        return    $a1
                . $a2
                . $a3
                // . $a4
                . $a5

                . $b1
                . $b2
                . $b3
                // . $b4
                . $b5
                ;
    }


    public function buildForModel($namespace){
var_dump([
    'namespace' => $namespace,
    'parentApp' => Model::$parentApp,
]);
        $app = \TDS\App::get();
        $modelName = $this->getModelName();
        $appName = $app::$appName;
        // $extends = $namespace === $this->namespace ? 'Table' : '\\'.$this->namespace.'\\Model\\'.$this->name;
        $extends = $namespace === $this->namespace ? 'Table' : '\\'.Model::$parentApp.'\\Model\\'.$this->name;
        return "<?php
namespace {$appName}\\Model;

use \\TDS\\Table;
use \\TDS\\App;

class {$this->name} extends {$extends} implements \\Model\\{$modelName}interface_ {
    use \\Model\\{$modelName};

}        
        ";
    }

    /************************************************************************************
     * 
     *    Construction des template pour twig 
     * 
     ***********************************************************************************/

    protected function buildModelHeadersTwig(){
        $date = new \DateTime();
        $dateStr = $date->format("r");
        $modelName = $this->getModelName();
        $appName = Model::$appName;
        return "
{#  This file is auto-generated and should not be changed by hand.
    filename : {$modelName}.html.twig
    created : {$dateStr} UTC
#}

{% extends \"CRUD/CRUD.html.twig\" %}

{% block main %}


<link rel=\"stylesheet\" href=\"/{{ App.appName }}/assets/css/forms.css\"> 

<div class=\"wrapper\">
    <div id=\"modal\" class=\"hidden\">
        <div id='modal_in'>
            <div class=\"modal__content\">
                <div id='modal_content'>
                    Il faut attendre un peu...
                </div>
                <div class=\"modal__footer\">
                    <button onclick='doDelete2()'>Confirmer la Suppression</button>
                </div>
                <a href=\"#\" class=\"modal__close\" onclick='closeModal()'>&times;</a>
            </div>
        </div>
    </div>
    <form>
        <fieldset>
            <legend><i class='fas fa-history history_entity' data-tipped-options='ajax: {data: { E: \"{$this->name}\", id: \"{{ {$this->name}.id }}\" }}'></i>{$this->name}</legend>
";
    }

    protected function buildModelFootersTwig(){
        return "
        </fieldset>
        <br>
        <br>
        <br>
        <br>
        </form>
</div>
{% endblock %}       
        ";
    }

    protected function buildModelFieldsTwig(){
        $a = "";
        foreach($this->fieldList as $n =>$field){
            if (is_string($field)) { // alors c'est un fragment 
                $a .= $field;                 
            } else {
                $a .= $field->buildForModelTwig($this->name);
            }
        }
        return $a;
    }

    protected function buildModelLinksTwig(){
        $a = "";
        foreach ($this->linkedList  as $name => $link){
            $linkType = is_null($link->isForManyToManyFirst())?"manyToOne":"manyToMany";
            $twigName = is_null($link->isForManyToManyFirst())?$name:$link->getTwigName();
            
            $twigName = $link->getInversedBy()."_".$twigName;

            $a .= "
    {% block {$linkType}_{$twigName} %} 
    <fieldset>            
        <legend><i class='fas fa-history history_Links' data-tipped-options='ajax: {data: { E: \"{$this->name}\", id: \"{{ {$this->name}.id }}\", L: \"{$name}\" }}'></i>{$name} - {{ ({$this->name}.{$name})|length }} élément{{({$this->name}.{$name})|length >1?'s':''}}</legend>
";
            $a .= $link->buildForTargetModelTwig($this->name, $this, $twigName);
            $a.="
    </fieldset>
    {% endblock {$linkType}_{$twigName} %}    
";
        }
        return $a;
    }

    protected function buildForModelInlineTwig($parentEntityName){
        $a = "
    <table class='w3-table-all w3-hoverable w3-small'>
        <thead>
            <tr class='w3-light-blue'>";
        foreach($this->fieldList as $n =>$field){
            $name = $field->getName(); 
            $a .= "    
                    <th>{$name}</th>";
        }
        $a .= "
            </tr>
        </thead>
        <tbody>";
        $a .= "
            <tr>";
            foreach($this->fieldList as $n =>$field){
                $value = $field->buildForModelTwigInline($this->name); 
                $a .= "    
                        <td>{{ {$parentEntityName}.{$this->name}.{$field->getName()} }}</td>";
            }
            $a .="
            </tr>
        </tbody>
    </table>
";
        return $a;
    }


    protected function buildModelOneToOneTwig(){
        $a = "";
        foreach($this->oneToOneList as $name => $oneToOne){
            $targetEntity = $oneToOne[0];
            $targetId = $oneToOne[1];
            $twigName = $targetEntity->getTwigName();
            $a .="
    {% block oneToOne_{$twigName} %}  
    <fieldset>
        <legend>{$name}</legend>
        {% if not isCreation %}
";
            $a .= $targetEntity->buildForModelInlineTwig($this->name);
            $a .="
        {% endif %}
    </fieldset>
    {% endblock oneToOne_{$twigName}%}
    ";
        }
        return $a;
    }

    public function buildForGhostTwig(){
        $a1 = $this->buildModelHeadersTwig();
        $a2 = $this->buildModelFieldsTwig();
        $a3 = $this->buildModelLinksTwig();
        $a4 = $this->buildModelOneToOneTwig();
        $a5 = $this->buildModelFootersTwig();
        return $a1.$a2.$a3.$a4.$a5;
    }


    public function buildForModelTwig(){
        $app = \TDS\App::get();
        
        //$modelName = $this->getModelName(false); // O.C. modification du 14/03/2021
        $modelName = $this->getModelName(true); // O.C. modification du 26/03/2026
        

        $appName = $app::$appName;

        return "{% extends \"CRUD/{$modelName}.html.twig\" %}
        ";

    }

    public function getSQL($schema=""){
        $idName = str_pad($this->idName, 20, " ");
        $sql = [];
        foreach($this->fieldList as $fieldName => $field){
            if (!is_string($field)){
                $sql[] = $field->getSQL(); // pas un fragment
            }
        }

        $sql = join(",", $sql);

        $sql ="
-- ****************************** {$this->name} as {$this->dbName}
CREATE TABLE {$schema}{$this->dbName}(
    {$idName}    integer PRIMARY KEY,
{$sql}    
);";

        return $sql;
    }

    public function getMocodo(){
        $idName = str_pad($this->idName, 20, " ");
        $mocodo = "$this->dbName:"; 
        $mocodo = [$this->idName];
        foreach($this->fieldList as $fieldName => $field){
            if (!is_string($field)){ //  pas un fragment
                $mocodo[] = $field->getMocodo(); 
            }
        }
        $mocodo = join(",", $mocodo);
        $mocodo ="{$this->dbName}: {$mocodo}";
        return $mocodo;
    }

}

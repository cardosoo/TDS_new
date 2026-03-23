<?php
namespace TDS\Model;

use TDS\App;

class OneToMany extends Field {
    

    protected Entity $sourceEntity;
    protected Entity $targetEntity;
    
    protected string $mappedBy;       // nom de la propriété qui est support du lien
    protected string $dbName;
    protected string $inversedBy;
    protected ?bool  $forManyToManyFirst; // null si pas pour ManyToMany ; true si pour la première relation ; false pour la deuxième

    protected ManyToMany $manyToMany;

    const mappedBy = 'mappedBy';
    const dbName = 'dbName';
    const twigName = 'twigName';
    const nullable = 'nullable';
    const inversedBy = 'inversedBy';
 
    /**
     * Undocumented function
     *
     * @param Entity $linkedEntity
     * @param [type] $options
     * @param $forManyToManyFirst null si pas pour ManyToMany ; true si pour la première relation ; false pour la deuxième
     * liste des options possibles 
     * - mappedBy   : le nom de la propriété qui est support du lien si omis c'est le nom de l'entité (en minuscule)
     * - dbName     : le nom de la propriété dans la base de données
     * - nullable   : indique si il s'agit d'une relation 0n ou 1n, TRUE par défaut
     * - inversedBy : par défaut nom de l'entité (en minuscule) suivi de List
     */
    function __construct(Entity $sourceEntity, Entity $targetEntity, array $options=[],  $forManyToManyFirst=null) {
        $this->sourceEntity = $sourceEntity;
        $this->targetEntity = $targetEntity;
        $this->mappedBy = $options['mappedBy'] ?? strtolower($targetEntity->getName());
//        $this->dbName = $options['dbName'] ?? $this->mappedBy;
        $this->dbName = $options['dbName'] ?? $this->mappedBy ;
        $this->inversedBy = $options['inversedBy'] ?? strtolower($sourceEntity->getName())."List";
        $this->twigName = $options['twigName'] ?? $this->inversedBy;
        $this->forManyToManyFirst = $forManyToManyFirst;
        parent::__construct($this->mappedBy, Field::INT, $options);
    }

    function isForManyToManyFirst(){
        return $this->forManyToManyFirst;
    }

    function getInversedBy(){
        return $this->inversedBy;
    }

    function getMappedBy(){
        return $this->mappedBy;
    }

    function buildForModel(){
        $targetClass = '\\'.Model::$appName.'\\Model\\'.$this->targetEntity->getName();
        $oneToMany = 'Field::ONETOMANY';
        $a = "    protected ?{$targetClass} \${$this->mappedBy};".( ("" === $this->comment)?"":" // {$this->comment}")."
    protected int \$__{$this->mappedBy};
";
        $b = "
        '{$this->mappedBy}' => [
            'type' => {$oneToMany}, 
            'targetEntity' => '{$targetClass}',
            'mappedBy' => '{$this->mappedBy}',
            'inversedBy' => '{$this->inversedBy}',
            'twigName' => '{$this->twigName}',
            'dbName' => '{$this->dbName}', 
        ],    
";
        return [$a, $b];
    }

    function buildForTargetModel(){
        $sourceClass =  '\\'.Model::$appName.'\\Model\\'.$this->sourceEntity->getName();
        $a =  "    protected array \${$this->inversedBy};\n";

        if (! is_null($this->forManyToManyFirst)){
            $this->manyToMany = $this->sourceEntity;
            list($targetEntity, $joinTable) = $this->manyToMany->getDataToBuildModel($this->targetEntity); 
            
            $manyToMany = 'Field::MANYTOMANY';
            $isFirst = $this->forManyToManyFirst?'true':'false';
            $b = "
        '{$this->inversedBy}' =>[ 
            'type' => {$manyToMany},
            'targetEntity' => '{$targetEntity}',
            'joinTable' => '{$joinTable}',
            'joinColumn' => '{$this->mappedBy}',
            'twigName' => '{$this->twigName}',
            'inverseJoinColum' => '{$this->inversedBy}',
            'isFirst' => {$isFirst},
        ],
";
        } else {
            $manyToOne = 'Field::MANYTOONE';
            $b = "
        '{$this->inversedBy}' => [
            'type' => {$manyToOne}, 
            'sourceEntity' => '{$sourceClass}',
            'mappedBy' => '{$this->mappedBy}',
            'inversedBy' => '{$this->inversedBy}',
            'twigName' => '{$this->twigName}',
        ],
";
        }
        return [$a, $b];
    }




    function buildForTargetModelTwig($name, Entity $up, $twigName = null){
        if (! is_null($this->forManyToManyFirst)){ // alors c'est du ManyToMany
            return $this->sourceEntity->buildManyToManyForTargetModelTwig("{$name}", "{$this->inversedBy}", $up, $this->forManyToManyFirst , $twigName, );            
        } else { // Il faut le prendre comme du manyToOne 
            $a = "
                <ul>
                {% for L in {$name}.{$this->inversedBy} %}";
            $a.="   {% block manyToOne_{$name}_{$this->twigName} %}
                    <li class='{{ L.actif?\"\":\"passif\" }}'>
                        {{ L.getGeneric(true)|raw }}
                    </li>
                    {% endblock manyToOne_{$name}_{$this->twigName} %}";
            $a.="
                {% endfor %}
                </ul>";
            return $a;
        }
    }


    public function buildForModelTwig($entityName, $prefix=""){
        $app = \TDS\App::get();
        $id = $prefix.$this->name;
        $targetName = $this->targetEntity->getName();
        $appName = $app::$appName;

        return "
{% block oneToMany{$entityName}_{$this->twigName}_full %}
<p>
    {% block oneToMany{$entityName}_{$this->twigName}_label %}
    <label class='select' for='{$id}'>
        {{  {$entityName}.{$this->name} ? {$entityName}.{$this->name}.getCrudEditLink(\"{$this->name}\")|raw : \"{$this->name}\"}}
        <i class='fas fa-history history_field' data-tipped-options='ajax: {data: { E: \"{$entityName}\", F: \"{$this->name}\", id: \"{{{$entityName}.id}}\" }}'></i>
    </label>
    {% endblock oneToMany{$entityName}_{$this->twigName}_label %}
    {% block oneToMany{$entityName}_{$this->twigName}_active %}    
    <input class='text {{ ( {$entityName}.{$this->name}.id ==0 or  {$entityName}.{$this->name}.actif)?\"\":\" passif\" }}'  value=\"{{ {$entityName}.{$this->name}.generic() }}\" name=\"$this->name\" id=\"{$id}\"  _id_=\"{{ {$entityName}.{$this->name}.id }}\">
    <script>
        setAutocomplete(\"#{$id}\", \"/{$appName}/CRUD/L/{$targetName}\");
    </script>
    {% endblock oneToMany{$entityName}_{$this->twigName}_active %}    
</p>
{% endblock oneToMany{$entityName}_{$this->twigName}_full %}
";
    }


    public function getSQL(){
        $dbName = str_pad($this->dbName, 20, " ");

        $targetTableName = $this->targetEntity->getDbName();
        $targetIdName = $this->targetEntity->getIdName();
        $sql = parent::getSQL();
        return "{$sql},
    FOREIGN KEY({$this->dbName}) REFERENCES {$targetTableName}($targetIdName)";
    }

    public function getMocodo(){
        $targetTableName = $this->targetEntity->getDbName();
        $targetIdName = $this->targetEntity->getIdName();
//        $mocodo = parent::getSQL();
//        return "#{$this->dbName}->{$targetTableName}->{$targetIdName}";
        return "#{$this->dbName}->{$targetTableName}>{$targetIdName}";
    }

}

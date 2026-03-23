<?php
namespace TDS\Model;

class Field {

    // les constantes pour les types de champs
    public const STRING = 0;
    public const INT = 1;
    public const FLOAT = 2;
    public const BOOL = 3;
    public const TEXT = 4;
    public const DATE = 5;

    public const ONETOONE   = 10;
    public const ONETOMANY  = 11;
    public const MANYTOONE  = 12;
    public const MANYTOMANY = 13;

    public const NOT_FIELD = [self::MANYTOMANY, self::MANYTOONE, self::ONETOONE]; // Pour indiquer les choses qui ne sont pas de champs de la base de données

    public const RAW = 'raw';
    public const HTML = 'html';
    public const MARKDOWN = 'markdown';


    // lien entre les constant
    public const type = [
        self::STRING => 'STRING', 
        self::INT => 'INT', 
        self::FLOAT => 'FLOAT', 
        self::BOOL => 'BOOL', 
        self::TEXT => 'TEXT', 
        self::DATE => 'DATE',
    ];


    // lien entre les types de champs et les types de la base de données
    protected const BDTYPE = [ 
        self::STRING => "varchar", 
        self::INT => "int",
        self::FLOAT => "float",
        self::BOOL => "boolean",
        self::TEXT => "text",
        self::DATE => "timezone without timezone",
    ];

    // lien entre les type des champs et les types PHP du modèle
    protected const MODELTYPE = [
        self::STRING => "string", 
        self::INT => "int",
        self::FLOAT => "float",
        self::BOOL => "bool",
        self::TEXT => "string",
        self::DATE => "string",
    ];
    
    // les tableaux pour reconnaitres les différentes catégories de types
    protected const NUMERIC_TYPES = [self::INT, self::FLOAT];
    protected const STRING_TYPES = [self::STRING, self::TEXT, self::DATE];
    protected const BOOL_TYPES = [self::BOOL];

    protected string $name;
    protected int $type;
    protected string $bdType;
    protected string $modelType;
    protected string $size;
    protected string $dbName;
    protected string $twigName;
    protected bool   $nullable;
    protected string $comment;


    const size = 'size';
    const default = 'default';
    const nullable = 'nullable';
    const dbName = 'dbName';
    const comment = 'comment';
    const twigName = 'twigName';
    const mode = 'mode';    
    /**
     * Constructeur du champ
     *
     * @param string $name
     * @param int    $type  le type de champ à prendre dans FIELD:: constants 
     * @param array $options
     * 
     * voici la liste des options possibles
     * 
     *  - size     : la taille du champ (utile pour le type varchar)
     *  - default  : la valeur par défaut
     *  - nullable : par defaut TRUE
     *  - dbName   : par defaut $name
     *  - twigName : par defaut $name
     *  - comment  : un commentaire éventuel à joindre au Model (et/ou à la base de données)
     *     public const STRING = 0;
     *     public const INT = 1;
     *     public const FLOAT = 2;
     *     public const BOOL = 3;
     *     public const TEXT = 4;
     *     public const DATE = 5;
     * 
     *     public const ONETOONE   = 10;
     *     public const ONETOMANY  = 11;
     *     public const MANYTOONE  = 12;
     *     public const MANYTOMANY = 13;
     */
    function __construct(string $name, int $type, array $options=[]) {
        $this->name = $name;
        $this->dbName = $options['dbName'] ?? $name;
        $this->twigName = $options['twigName'] ?? $name;
//        $this->dbName = '"'.($options['dbName'] ?? $name).'"';
        $this->type = $type;
        $this->bdType = self::BDTYPE[$type];
        $this->modelType = self::MODELTYPE[$type];
        $this->size = $options['size'] ?? 100;
        $this->default = $options['default']?? ( in_array($type, self::NUMERIC_TYPES) ? 0: (  in_array($type, self::STRING_TYPES) ? "" : 'FALSE') ) ;
        $this->nullable = $options['nullable'] ?? TRUE;
        $this->comment = $options['comment'] ?? "";
        $this->mode = $options['mode'] ?? "raw";
    }

    public function getName(){
        return $this->name;
    }

    public function getTwigName(){
        return $this->twigName;
    }
    
    public function isTextual(){
        return in_array($this->type, self::STRING_TYPES); 
    }

    public function buildForModel(){
        $nullable = $this->nullable ? 'TRUE': 'FALSE';
        $default = in_array($this->type, self::STRING_TYPES)?"'".\addslashes($this->default)."'":"{$this->default}";        
        $type = 'Field::'.self::type[$this->type];
        $a = "    protected {$this->modelType} \${$this->name} = $default;".( ("" === $this->comment)?"":" // {$this->comment}")."\n";
        $b = "
        '{$this->name}' => [
            'dbName' => '{$this->dbName}',
            'type' => {$type},
            'size' => {$this->size},
            'default' => {$default},
            'nullable' => {$nullable},
            'twigName' => '{$this->twigName}',
            'mode' => '{$this->mode}',
            ],
";
        return [$a, $b];
    }


    public function buildForModelTwigInline($entityName){
        return "{$entityName}.{$this->name}";
    }


    public function buildForModelTwig($entityName, $prefix=""){
        $nullable = $this->nullable ? 'TRUE': 'FALSE';
        $default = in_array($this->type, self::STRING_TYPES)?"'".\addslashes($this->default)."'":"{$this->default}";        
        $id = $prefix.$this->name; 
        $startBlock = "
{% block {$this->twigName}_full %}
    <p>
    {% block {$this->name}_label %}
    <label class='text' for=\"{$id}\"> {$this->name} <i class='fas fa-history history_field' data-tipped-options='ajax: {data: { E: \"{$entityName}\", F: \"{$this->name}\", id: \"{{{$entityName}.id}}\" }}'></i></label>    
    {% endblock {$this->name}_label %}
    {% block {$this->twigName}_active %}";

        $endBlock = "
    {% endblock {$this->twigName}_active %}
    </p>
{% endblock {$this->twigName}_full %}    
";

        switch ($this->type){
            case self::STRING: 
                $size = $this->size >50 ?50:$this->size;
                return "{$startBlock}
    <input class='text'  type=\"text\" value=\"{{ {$entityName}.{$this->name} }}\" name=\"$this->name\" id=\"{$id}\" size=\"{$size}\" maxlength=\"{$this->size}\", placeholder=\"{$this->default}\">
{$endBlock}";
                break;
            case self::INT:
                return "{$startBlock}
    <input class='text' type=\"number\"  value=\"{{ {$entityName}.{$this->name} }}\" name=\"$this->name\" id=\"{$id}\" placeholder=\"{$this->default}\">
{$endBlock}";
            break;     
            case self::FLOAT:
                return "{$startBlock}
    <input class='text' type=\"number\"  value=\"{{ {$entityName}.{$this->name} }}\" name=\"$this->name\" id=\"{$id}\" step=\"any\" placeholder=\"{$this->default}\">
{$endBlock}";
            break;     
            case self::BOOL:
                return "{$startBlock}
    <input class='text' type=\"checkbox\"  {{ {$entityName}.{$this->name}==1?'checked':'' }} name=\"$this->name\" id=\"{$id}\">
{$endBlock}";
            break;     
            case self::DATE:
                return "{$startBlock}
    <input class='text' type=\"datetime-local\"  value=\"{{ {$entityName}.{$this->name}|date('Y-m-d') }}T{{ {$entityName}.{$this->name}|date('H:i') }}\" name=\"$this->name\" id=\"{$id}\">
{$endBlock}";
            break;     
            case self::TEXT:
                return "{$startBlock}
    <textarea mode='{$this->mode}' class='textarea' name=\"$this->name\" id=\"{$id}\" size=\"10\"  placeholder=\"{$this->default}\">{{ {$entityName}.{$this->name} }}</textarea>
{$endBlock}";
                break;
            default:
                return "";
        }
    }

    public function getSQL(){
        $app = \TDS\App::get();

        $dbName = str_pad($this->dbName, 20, " ");
        $size = str_pad("({$this->size})", 6, " ");
        $default = pg_escape_string($app::$db->conn, $this->default);
        switch ($this->type){
            case self::STRING: 
                return "
    {$dbName}    character varying{$size}  DEFAULT '{$default}'::character varying";
                break;
            case self::INT:
                return "
    {$dbName}    integer                  DEFAULT {$default}::integer";
            break;     
            case self::FLOAT:
                return "
    {$dbName}    real                     DEFAULT '{$default}'::real";
                break;     
            case self::BOOL:
                return "
    {$dbName}    boolean                  DEFAULT '{$default}'::boolean";
                break;     
            break;     
            case self::DATE:
                return "
    {$dbName}    date                    DEFAULT '{$default}'::date";
                break;     
            break;     
            case self::TEXT:
                return "
    {$dbName}    text                     DEFAULT '{$default}'::text";
                break;
            default:
                return "";
        }
    }

    public function getMocodo(){
        $app = \TDS\App::get();
        $dbName = str_pad($this->dbName, 20, " ");
        $size = str_pad("({$this->size})", 6, " ");
        $default = pg_escape_string($app::$db->conn, $this->default);
        switch ($this->type){
            case self::STRING: 
                return "{$this->dbName} [varchar({$this->size}) DEFAULT '{$default}']";
                break;
            case self::INT:
                return "{$this->dbName} [integer DEFAULT {$default}]";
            break;     
            case self::FLOAT:
                return "{$this->dbName} [real DEFAULT {$default}]";
                break;     
            case self::BOOL:
                return "{$this->dbName} [boolean DEFAULT {$default}]";
                break;     
            break;     
            case self::TEXT:
                return "{$this->dbName} [text DEFAULT '{$default}']";
                break;
            default:
                return "";
        }
    }


}

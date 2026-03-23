<?php
namespace TDS;

/**
 * Description of Database
 * 
 * Cette classe permet de gérer les base de données
 * 
 * @author olivier
 */
class Database {
    public $conn = null;
    private $capture = false;
    private $captureString = "";
    private $affected_rows = 0;
    private $captureArray = [];

    public function __construct(string $baseName, string $user, string $pass, $host) {
        $connString = "dbname=".$baseName
                . " host=".$host
                ." user=".$user
                ." password=".$pass;
        
        $this->conn = pg_connect($connString);
        if (!$this->conn) {
            die("Erreur d'ouverture de la base de données !");
        }
        pg_set_client_encoding($this->conn,"Unicode");
        $this->query("select set_config('DateStyle','European',false);");
    }

    public function getConnection(){
        return $this->conn;
    }
    
    public function query($sql){
        $app = \TDS\App::get();
        $app::$sqlList[]=$sql;
//var_dump(xdebug_get_function_stack());
        $logFile = $app::$pathList['log']."/sql.log";
        \file_put_contents($logFile, date(\DateTime::ATOM)." ===========\n".$sql."\n", FILE_APPEND);
        return pg_query($this->conn, $sql);
    }
    
    public function getSqlList(){
        $app = \TDS\App::get();
        return $app::$sqlList;
    }
    
    public function startCapture(){
        $this->capture = true;
        $this->captureArray = [];
    }

    public function stopCapture($play = true){
        $this->capture = false;        
        if ($play){
            $sql = implode(";\n", $this->captureArray);
            $this->h_query($sql);
        }
        return $this->captureArray;
    }

    /**
     * Execute la requête et l'inscrit dans l'historique
     * 
     * @param String $sql
     */
    public function h_query($sql){
        $app = \TDS\App::get();
        $sql = trim($sql);
        if ($sql[-1] !== ";"){
            $sql .= ";";
        }
        if ($this->capture){
            $this->captureArray[]=$sql;
            $this->affected_rows = 0;
        } else {
            $logFile = $app::$pathList['log']."/sql.log";
            \file_put_contents($logFile, date(\DateTime::ATOM)." ===========\n".$sql."\n", FILE_APPEND);
            $this->affected_rows = Historique::h_query($sql);
        }
    }
    
    public function getAffectedRows(){
        return $this->affected_rows;
    }

    /**
     * lis une ligne d'un résulat postgresql sous forme d'une variable associative
     *  avec le nom de la table puis le nom du champ
     * renvoie array[nom_table][nom_champ]
     * 
     * @param \PgSql\Result $r => le résultat postgres
     * @param int $i => le nom de la ligne
     * @return StdClass => l'objet du résultat
     */
    private function fetchOneRow($r, $i){
        $nFields=pg_num_fields($r);
        $dRow = pg_fetch_row($r,$i);
        $res = new \StdClass();
        
        for ($iFields = 0; $iFields < $nFields; $iFields++){
            $table = pg_field_table($r,$iFields);
            $name = pg_field_name($r,$iFields);
            if (false === $table){
                $res->$name=$dRow[$iFields];
            } else {
                if (!isset($res->$table)) {
                    $res->$table = new \StdClass();
                }
                $res->$table->$name=$dRow[$iFields];        
            }
        }
        return $res;
    }

    
    
    /**
     * Renvoie tous les résultats d'une requête sous forme d'un tableau d'objets
     * 
     * @param string $sql => la requête
     * 
     * @return StdClass[] => le tableau des résultats
     */
    public function fetchAll($sql){
        $r = $this->query($sql);
        $res=[];
        $nRows=pg_num_rows($r);        
        for ($i=0;$i<$nRows;$i++){
            $res[$i]=$this->fetchOneRow($r,$i);        
        }
        return $res;
    }
 
    /**
     * Renvoie le premier résultat d'une requête sous forme d'un objet
     * 
     * @param string $sql => la requête
     * @return \StdClass => le premier résultat
     */
    public function fetchOne($sql){
        $tmp = $this->fetchAll($sql);
        if (count($tmp)>0){
            return $tmp[0];
        }
        return null;
    }

    
    private function getOneRow($r, $i){
        $nFields=pg_num_fields($r);
        $dRow = pg_fetch_row($r,$i);       
        $res = new \StdClass();
        for ($iFields = 0; $iFields < $nFields; $iFields++){
            $name = pg_field_name($r,$iFields);
            $res->$name=$dRow[$iFields];
        }
        return $res;
    }
    
    /**
     * Quelle est la différence avec fetchAll ?
     * @param string $sql
     * @return array
     */
    public function getAll($sql){
        $r = $this->query($sql);
        $res=[];
        $nRows=pg_num_rows($r);        
        for ($i=0;$i<$nRows;$i++){
            $res[$i]=$this->getOneRow($r,$i);        
        }
        return $res;        
    }

    /**
     * Quelle est la différence avec fetchOne ?
     * @param string $sql
     * @return 
     */
    public function getOne($sql){
        $tmp = $this->getAll($sql); 
        return (count($tmp)===0)?null:$tmp[0];
    }
    
    /**
     *  function getOneWhere
     *
     * @param string $tableName
     * @param string $where
     * @return \stdClass
     * 
     * O.C. 15/01/2021 : première version
     */
    public function getOneWhere(string $tableName, string $where){
        return $this->getOne("
            SELECT *
            FROM \"{$tableName}\"
            WHERE {$where}    
        ");
    }
    
    /**
     *  function getWhere
     *
     * @param string $tableName
     * @param string $where
     * @return array
     * 
     * O.C. 15/01/2021 : première version
     */
    public function getWhere(string $tableName, string $where,  $order= null, $idPositive = true){
        if (!is_null($order) ){
            $order = "ORDER BY ".join(', ', $order);
        }
        
        $sql = "
            SELECT 
               \"{$tableName}\".*
            FROM \"{$tableName}\""
            .($idPositive?"
            WHERE \"{$tableName}\".id>0 
            AND (":"")." {$where}".($idPositive?")":"")."
            {$order}
        ";
        return $this->getAll($sql);
    }

    
    /**
     * Renvoie le prochain num disponible pour inserer un enregistrement
     * 
     * @param type $tableName
     * @return int
     */
    public function getFreeNum($tableName){
        return $this->fetchOne("select max(num) as max from \"{$tableName}\"")->max +1;
    }

    /**
     * 
     * @param type $tableName
     * @return int
     */
    public function addOne($tableName){
        $num = $this->fetchOne("select max(num) as max from \"{$tableName}\"")->max +1;
        $this->h_query("
            INSERT INTO \"{$tableName}\" 
                (num)
            VALUES
                ({$num}) 
        ");
        return $num;
    }


}

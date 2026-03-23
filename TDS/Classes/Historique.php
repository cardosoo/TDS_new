<?php
namespace TDS;
/**
 * Description of Historique
 *
 * @author olivier
 */
class Historique {    
    static public function logUser($st){
        $app = \TDS\App::get();
        $str =date(DATE_COOKIE)."\t".$app::$auth->user->id."\t".Outils::getIP()."\t".$st."\n";
        $h = fopen(__DIR__."/../../log/".$app::$baseName.".log",'a');
        fwrite($h, $str);
        fclose($h);        
    }
    
    static public function h_query($sql) {
        $app = \TDS\App::get();
        $conn = $app::$db->getConnection();
        $result = pg_query($conn, $sql);
        $affected_rows = pg_affected_rows($result);
        $result = $result===false?pg_last_error($conn):pg_last_notice($conn);
   //     self::logUser($sql);  // Il faut sans doute remettre un truc comme cela, mais il faut y réfléchir globalement sur la façon de loguer
        $U  = is_null($app::$auth->user)?0:$app::$auth->user->id;
        $nom = is_null($app::$auth->user)?'---':$app::$auth->user->getGeneric();
        $IP = $nom." / ".\TDS\Utils::getIP();

        $app::doLogSQL($sql);

        $requete = '
    INSERT INTO  historique 
        (ip, qui, date, requete, result) 
    VALUES
        ($1, $2, now(), $3, $4);
        ';    
        
        pg_query_params($conn, $requete, [
            $IP,
            $U,
            $sql,
            $result
        ]);
        return $affected_rows;
    }


    public static function entity(string $entityName, int $id){
        $app = \TDS\App::get();

        $entityNS = $app::NS($entityName);
        $dbTableName = $entityNS::dbName;

        $sql = "
        SELECT 
        P.nom,
        P.prenom,
        H.* 
        FROM historique as H
        LEFT JOIN personne as P on P.id = H.qui  
        WHERE requete LIKE '%UPDATE \"{$dbTableName}\"%WHERE id = {$id};%' 
        OR requete LIKE '%INSERT INTO \"{$dbTableName}\"%VALUES ({$id})%'
        ORDER BY H.date DESC 
        ";

        $updateList = $app::$db->fetchAll($sql);
        return $updateList;
    }

    public static function entity_field(string $entityName, string $fieldName, int $id){
        $app = \TDS\App::get();

        $entityNS = $app::NS($entityName);
        $dbTableName = $entityNS::dbName;
        $dbFieldName = $entityNS::entityDef[$fieldName]['dbName'];

        $sql = "
        SELECT 
        P.nom,
        P.prenom,
        H.* 
        FROM historique as H
        LEFT JOIN personne as P on P.id = H.qui  
        WHERE requete LIKE '%UPDATE \"{$dbTableName}\"%\"{$dbFieldName}\" =%WHERE id = {$id};%' 
        ORDER BY H.date DESC 
        ";

        $updateList = $app::$db->fetchAll($sql);
        return $updateList;               
    }

    public static function entity_links(string $entityName, string $linkName, int $id){
        $app = \TDS\App::get();

        $entityNS = $app::NS($entityName);
        $dbTableName = $entityNS::dbName;
        $joinedNS = $entityNS::entityDef[$linkName]['joinTable'];
        $dbJoinTableName = $joinedNS::dbName;

        $updateList = $app::$db->fetchAll("
        SELECT 
        P.nom,
        P.prenom,
        H.* 
        FROM historique as H
        LEFT JOIN personne as P on P.id = H.qui  
        WHERE requete LIKE '%UPDATE \"{$dbJoinTableName}\"%\"{$dbTableName}\" = {$id};%' 
        ORDER BY H.date DESC 
        ");

        return $updateList;
    }

    public static function entity_manyToMany(string $entityName, int $id){
        $app = \TDS\App::get();

        $entityNS = $app::NS($entityName);
        $dbTableName = $entityNS::dbName;

        $updateList = $app::$db->fetchAll("
        SELECT 
        P.nom,
        P.prenom,
        H.* 
        FROM historique as H
        LEFT JOIN personne as P on P.id = H.qui  
        WHERE requete LIKE '%UPDATE \"{$dbTableName}\"%WHERE id = {$id};%' 
        OR requete LIKE '%INSERT INTO \"{$dbTableName}\"%VALUES ({$id})%'
        ORDER BY H.date DESC 
        ");

        return $updateList;
    }



}

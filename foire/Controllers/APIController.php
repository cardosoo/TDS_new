<?php
namespace foire\Controllers;

use TDS\Query;
use voku\helper\HtmlDomParser;

class APIController extends \base\Controllers\APIController {


    /**
     * Cette fonction est requise par Jupy pour faire les inscriptions des enseignants
     * Elle renvoie les uid et les noms et prenoms de toutes les personnes qui sont
     * activent dans la foire
     * 
     */
    public static function getFullActiveUsers($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $userList = $db-> getAll("
            SELECT DISTINCT
                P.uid,
                P.nom,
                P.prenom,
                S.nom as statut
            FROM Personne as P
            LEFT JOIN Statut as S on P.statut = S.id
            WHERE P.actif AND P.id>0 
            AND NOT P.uid LIKE '--%'
            ORDER BY nom, prenom
        ");

        echo "uid\tNom\tPrénom\tStatut\n";
        foreach($userList as $user){
            echo "{$user->uid}\t{$user->nom}\t{$user->prenom}\t{$user->statut}\n";
        }

    }


    public static function noFinishLine(){
           //var_dump('désactivation de /foire/test/test2'); exit();
        $app = \TDS\App::get();

        $dirName = $app::$pathList['plus']."/noFinishLine";
        if (!file_exists($dirName)){
            mkdir($dirName);
        }

        $html = `curl 'http://nflp.livetour.fr/classement.php' --compressed -X POST -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:138.0) Gecko/20100101 Firefox/138.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' -H 'Accept-Language: fr-FR,en-US;q=0.7,en;q=0.3' -H 'Accept-Encoding: gzip, deflate' -H 'Referer: http://nflp.livetour.fr/classement.php' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Origin: http://nflp.livetour.fr' -H 'DNT: 1' -H 'Sec-GPC: 1' -H 'Connection: keep-alive' -H 'Cookie: langue_affich=_fr; showMenu=' -H 'Upgrade-Insecure-Requests: 1' -H 'Priority: u=0, i' -H 'Pragma: no-cache' -H 'Cache-Control: no-cache' --data-raw 'mode=&epreuve=24h&sex='`;
        $dom = HtmlDomParser::str_get_html($html);
        foreach($dom->find('tr') as $tr){
            $tdList = $tr->find('td');
            if ($tdList->count <2) continue;
            $rang = $tdList[0]->innerhtml;
            $dossard = $tdList[1]->text;
            $nom = $tdList[2]->text;
            $sexe = $tdList[3]->text;
            $tours = $tdList[5]->text;
            $temps = $tdList[6]->text;

            $fName = "{$dirName}/dossard{$dossard}.csv";
            file_put_contents($fName, "{$rang}\t{$dossard}\t{$nom}\t{$sexe}\t{$tours}\t{$temps}\n", FILE_APPEND);
        }
        exit(0);
    }

    public static function getNoFinishLine($dossard){
        $app = \TDS\App::get();

        $dirName = $app::$pathList['plus']."/noFinishLine";
        $fName = "{$dirName}/dossard{$dossard}.csv";

        echo (file_get_contents($fName));
    }

}

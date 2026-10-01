<?php

namespace services\Controllers;


use base\Controllers\VacationController;
use base\Struct;
use TDS\Query;
use TDS\App;

use Structure;
use StructureQuery;
use Etape;
use EtapeQuery;
use ECUE;
use ECUEQuery;
use ecue_etape;
use ecue_etapeQuery;
use Matrix\Decomposition\QR;

class TestController extends \foire\Controllers\TestController {

    public static function test1(){
        // importation des données de test
        // var_dump('désactivation de /services/test/test1'); exit();
  
        $app = \TDS\App::get();
        $BASE_DIR = \TDS\App::$basePath;
        $IMPORT_DIR = $BASE_DIR . "/../Docs/repartition_historique";

        foreach(range(2015, 2023) as $year) {
            $nYear = $year + 1;
            $file = $IMPORT_DIR . "/repartition_{$year}_{$nYear}.csv";
            if (file_exists($file)) {
                $data = file_get_contents($file);
                // Traitement des données CSV ici
                var_dump("Importation des données pour l'année " . $year);
            } else {
                var_dump("Fichier non trouvé pour l'année " . $year);
            }
        }

//        var_dump(glob($IMPORT_DIR . "/*.csv"));



    }

    public static function test2_org(){
        ///////////////////////////////////////////////////////
        // Importation de la structure des enseignements depuis
        // la base de données structure;..
        ///////////////////////////////////////////////////////

        // var_dump('désactivation de /services/test/test2'); exit();

        $app = \TDS\App::get();
        $BASE_DIR = \TDS\App::$basePath;
        $filename = $BASE_DIR . "/../Docs/structure.sq3";

        $pdo = new \PDO("sqlite:{$filename}");
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        
        $pdo->exec(
<<< SQL

DROP TABLE IF EXISTS s_structure;
CREATE TEMPORARY TABLE s_structure AS
SELECT DISTINCT
    C.id as id,
    C.nom as nom,
    0 as ose_id,
    C.code as  ose_nom
FROM composante as C;

INSERT INTO s_structure (id, nom, ose_id, ose_nom) VALUES 
(0, 'Structure Inconnu', 0, 'Structure Inconnu');

DROP TABLE IF EXISTS s_etape;
CREATE TEMPORARY TABLE s_etape AS
SELECT
    ROW_NUMBER() OVER (ORDER BY M.code ASC) AS id,
    M.nom,
    M.code,
    SUBSTR(M.type, 1, LENGTH(M.type) - 1) as type,
    SUBSTR(M.type , -1) as niveau,
    M.domaine, 
    0 as effectif,
    COALESCE(S.id, 0) as structure_id,
    0 as ose_id,
    M.nom as ose_nom
FROM (SELECT DISTINCT "Elément de formation" as nom, "Code ROF" as code, "Niveau / Année" as type, "Type de diplôme" as domaine, "Composante" as composante FROM maquette) as M
LEFT JOIN s_structure as S ON M.composante = S.nom;

INSERT INTO s_etape (id, nom, code, type, niveau, domaine, effectif, structure_id, ose_id, ose_nom) VALUES 
(0, 'Etape Inconnue', 'ETAPE_INCONNUE', 'Inconnu', '0', 'Inconnu', 0, 0, 0, 'Etape Inconnue');


DROP TABLE IF EXISTS s_ecue;
CREATE TEMPORARY TABLE s_ecue AS
SELECT
    ROW_NUMBER() OVER (ORDER BY UE."UE Code" ASC) AS id,
    UE."UE Libellé" as nom,
    UE."UE Code" as code,
    UE."Semestre Calendaire" as periode,
     COALESCE((SELECT E2.id FROM s_etape E2 WHERE E2.code = TRIM(SUBSTR(UE."UE Mutualisée" || CHAR(10), 1, INSTR(UE."UE Mutualisée" || CHAR(10), CHAR(10)) - 1)) LIMIT 1), 0) as etape_id,
    UE."UE Libellé" as ose_nom,
    0 as effectif,
    json_object(
        'CM', COALESCE(UE."CM", 0),
        'TD', COALESCE(UE."TD", 0),
        'TP', COALESCE(UE."TP", 0),
        'PT', COALESCE(UE."PT", 0),
        'AA', COALESCE(UE."AA", 0),
        'Projet', COALESCE(UE."Projet", 0)
    ) as besoins
FROM UE as UE
WHERE UE."UE dispensée" = "OUI";


DROP TABLE IF EXISTS s_ecue_etape;
CREATE TEMPORARY TABLE s_ecue_etape AS
WITH RECURSIVE split_codes(ue_code, remaining, etape_code) AS (
    SELECT
        UE."UE Code",
        UE."UE Mutualisée",
        TRIM(SUBSTR(UE."UE Mutualisée", 1,
            CASE WHEN INSTR(UE."UE Mutualisée", CHAR(10)) > 0
                 THEN INSTR(UE."UE Mutualisée", CHAR(10)) - 1
                 ELSE LENGTH(UE."UE Mutualisée") END))
    FROM UE
    WHERE UE."UE dispensée" = "OUI" AND UE."UE Mutualisée" IS NOT NULL

    UNION ALL

    SELECT
        s.ue_code,
        SUBSTR(s.remaining, INSTR(s.remaining, CHAR(10)) + 1),
        TRIM(SUBSTR(SUBSTR(s.remaining, INSTR(s.remaining, CHAR(10)) + 1), 1,
            CASE WHEN INSTR(SUBSTR(s.remaining, INSTR(s.remaining, CHAR(10)) + 1), CHAR(10)) > 0
                 THEN INSTR(SUBSTR(s.remaining, INSTR(s.remaining, CHAR(10)) + 1), CHAR(10)) - 1
                 ELSE LENGTH(SUBSTR(s.remaining, INSTR(s.remaining, CHAR(10)) + 1)) END))
    FROM split_codes s
    WHERE INSTR(s.remaining, CHAR(10)) > 0
)
SELECT
    ROW_NUMBER() OVER () AS id,
    ec.id AS ecue_id,
    COALESCE(et.id, 0) AS etape_id,
    0 as effectif,
    'Extraction' as source
FROM split_codes sc
JOIN s_ecue ec ON ec.code = sc.ue_code
JOIN s_etape et ON et.code = sc.etape_code
WHERE sc.etape_code != '';

SQL);


$pdo->exec("ALTER TABLE s_etape ADD COLUMN search TEXT");
$stmt = $pdo->query("SELECT * FROM s_etape");
$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
$update = $pdo->prepare("UPDATE s_etape SET search = ? WHERE id = ?");
foreach ($rows as $row) {
    // Concaténer les colonnes (adaptez selon vos besoins)
    $concatenated = $row['nom'] . $row['nom'] . $row['code'];
    $searchValue = $app::normalizeText($concatenated);
    $update->execute([$searchValue, $row['id']]);
}

$pdo->exec("ALTER TABLE s_ecue ADD COLUMN search TEXT");
$stmt = $pdo->query("SELECT * FROM s_ecue");
$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
$update = $pdo->prepare("UPDATE s_ecue SET search = ? WHERE id = ?");
foreach ($rows as $row) {
    // Concaténer les colonnes (adaptez selon vos besoins)
    $concatenated = $row['code'] . $row['nom'] . $row['nom'];
    $searchValue = $app::normalizeText($concatenated);
    $update->execute([$searchValue, $row['id']]);
}




// Dans TestController.php, après avoir créé vos tables temporaires
$newFilename = "/home/olivier/pourGIT/TDS2024/src/TDS_plus/structure/structure_saclay2025.sq3";
 
// Attacher la nouvelle base
$pdo->exec("ATTACH DATABASE '{$newFilename}' AS newdb");

// Créer les tables (sans préfixe) et copier les données
$pdo->exec(
<<< SQL


CREATE TABLE newdb.structure AS SELECT * FROM s_structure WHERE 0;
CREATE TABLE newdb.etape AS SELECT * FROM s_etape WHERE 0;
CREATE TABLE newdb.ecue AS SELECT * FROM s_ecue WHERE 0;
CREATE TABLE newdb.ecue_etape AS SELECT * FROM s_ecue_etape WHERE 0;

INSERT INTO newdb.structure SELECT * FROM s_structure;
INSERT INTO newdb.etape SELECT * FROM s_etape;
INSERT INTO newdb.ecue SELECT * FROM s_ecue;
INSERT INTO newdb.ecue_etape SELECT * FROM s_ecue_etape;
SQL
);

// Détacher la nouvelle base
$pdo->exec("DETACH DATABASE newdb");


        $stmt = $pdo->query(
<<< SQL

SELECT
*
FROM s_ecue_etape;

SQL);
        var_dump($stmt->fetchAll(\PDO::FETCH_ASSOC)); exit();

        echo $app::$viewer->render('test/test2.html.twig', [
            'RL' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
        ]);

    }


    public static function test2(){
        $importer = new \services\Importer();
        $importer->attachNewDB();
        
        $importer->createStructure();
        $importer->createEtape();
        $importer->createEcue();
        $importer->createEcueEtape();

        $importer->detachNewDB();
    }

    public static function test3(){
        // importation repartition
        // on commence par l'année 2022 ?
        // les données sont dans la base sql3 habituelle;
        // var_dump('désactivation de /service/test/test3'); exit();

        $importer = new \services\Importer();

        // $importer->createStatuts();
        // $importer->createPersonnes();
        $importer->createEnseignement();
        $importer->createVoeux();

    }


    public static function test4(){

        $importerList = [];
        for($year=2022; $year <= 2025; $year++){
            $importerList[$year] = new \services\Importer($year);
        }

        foreach($importerList as $year => $importer){
            var_dump(['year' => $year, 'unique_code' => $importer->test_unique_code_ecue()]);
        }

        foreach($importerList as $year => $importer){
            var_dump(['year' => $year, 'unique_intitule' => $importer->test_unique_intitule()]);
        }
    }

}

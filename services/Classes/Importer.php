<?php

namespace services;

class Importer{

    private string $app;
    private string $year;
    private \PDO $pdo;
    private string $tableName;


    public function __construct(?string $year = null){
        $this->app = App::get();

        $this->year = $year ?? $this->app::$currentYear; 
        $year_plus_un = $this->year+1;

        $this->tableName = "repartition_{$this->year}_{$year_plus_un}";

        $BASE_DIR = $this->app::$basePath;
        $filename = $BASE_DIR . "/../Docs/structure.sq3";

        $this->pdo = new \PDO("sqlite:{$filename}");
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);



    }


    public function attachNewDB(){
        $newFilename = "/home/olivier/pourGIT/TDS2024/src/TDS_plus/structure/structure_saclay2025.sq3";
        $this->pdo->exec("ATTACH DATABASE '{$newFilename}' AS newdb");
    }


    public function detachNewDB(){
        $this->pdo->exec("DETACH DATABASE newdb");
    }

    public function createStructure(){
        $this->pdo->exec(
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

DROP TABLE IF EXISTS newdb.structure; 
CREATE TABLE newdb.structure AS SELECT * FROM s_structure WHERE 0;
INSERT INTO newdb.structure SELECT * FROM s_structure;

SQL );



}


    public function createEtape(){
        $this->pdo->exec(
<<< SQL
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
SQL );


        $this->pdo->exec("ALTER TABLE s_etape ADD COLUMN search TEXT");
        $stmt = $this->pdo->query("SELECT * FROM s_etape");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $update = $this->pdo->prepare("UPDATE s_etape SET search = ? WHERE id = ?");
        foreach ($rows as $row) {
            // Concaténer les colonnes (adaptez selon vos besoins)
            $concatenated = $row['nom'] . $row['nom'] . $row['code'];
            $searchValue = $this->app::normalizeText($concatenated);
            $update->execute([$searchValue, $row['id']]);
        }

        $this->pdo->exec(
<<< SQL


DROP TABLE IF EXISTS newdb.etape;
CREATE TABLE newdb.etape AS SELECT * FROM s_etape WHERE 0;
INSERT INTO newdb.etape SELECT * FROM s_etape;
SQL
);


    }

    public function createEcue(){

        $this->pdo->exec(
<<< SQL

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
    
SQL );

        $this->pdo->exec("ALTER TABLE s_ecue ADD COLUMN search TEXT");
        $stmt = $this->pdo->query("SELECT * FROM s_ecue");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $update = $this->pdo->prepare("UPDATE s_ecue SET search = ? WHERE id = ?");
        foreach ($rows as $row) {
            // Concaténer les colonnes (adaptez selon vos besoins)
            $concatenated = $row['code'] . $row['nom'] . $row['nom'];
            $searchValue = $this->app::normalizeText($concatenated);
            $update->execute([$searchValue, $row['id']]);
        }

        $this->pdo->exec(
<<< SQL

DROP TABLE IF EXISTS newdb.ecue;
CREATE TABLE newdb.ecue AS SELECT * FROM s_ecue WHERE 0;
INSERT INTO newdb.ecue SELECT * FROM s_ecue;
SQL
);


    }

    public function createEcueEtape(){

        $this->pdo->exec(
<<< SQL

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


DROP TABLE IF EXISTS newdb.ecue_etape;
CREATE TABLE newdb.ecue_etape AS SELECT * FROM s_ecue_etape WHERE 0;
INSERT INTO newdb.ecue_etape SELECT * FROM s_ecue_etape;

SQL);

    }


    // Création des status mais en fait non, je ne le fais pas, 
    // je récupère la vieille table
    public function createStatuts(){
  
    // on récupère les enseignants à créer
        $stmt = $this->pdo->query(
<<< SQL
SELECT DISTINCT
    statut as nom
FROM {$this->tableName};
SQL);

        // création des fiches statut
        $statutList = $stmt->fetchAll(\PDO::FETCH_CLASS);
        foreach($statutList as $statut){
            $statutNS = $this->app::NS('Statut');
            $S = new $statutNS();
            $S->nom = $statut->nom;
            $S->save();
        }
    }

    public function createPersonnes(){
    // on récupère les personnes à créer
        $stmt = $this->pdo->query(
<<< SQL
SELECT DISTINCT
    uid,
    nom,
    prenom, 
    mail,
    tel, 
    statut
FROM {$this->tableName};
SQL);

        $personneList = $stmt->fetchAll(\PDO::FETCH_CLASS);
        foreach($personneList as $personne){
            
            $personneNS = $this->app::NS('Personne');
            $P = new $personneNS();
            $P->uid = $personne->uid??"";
            $P->nom = $personne->nom??"";
            $P->prenom = $personne->prenom??"";
            $P->email = $personne->mail??"";  
            $P->tel1 = $personne->tel??"";
            
            $statut= $this->app::NS('Statut')::loadOneWhere("actif and id>0 and nom ILIKE '{$personne->statut}'");
            
            $P->statut = $statut->id;
            $P->save();
        }
    }


    public function createEnseignement(){
    // on récupère les enseignements à créer
        $stmt = $this->pdo->query(
<<< SQL
SELECT DISTINCT
    T.sigle,
    T.intitule,
    T.code_ecue, 
    UE."UE Libellé" as libelle,
    UE."Semestre Calendaire" as semestre,
    UE."Objectifs d'apprentissage" as apprentissage,
    UE."Programme/Plan/Contenus" as programme,
    UE."Organisation générale de l'UE et modalités
pédagogiques"  as organisation,
    UE."Bibliographie" as bibliographie,
    UE."Prérequis" as prerequis,
    UE."Disciplines" as disciplines,
    UE."Modalités pédagogiques particulières" as modalites,
    UE."CM",
    UE."TD",
    UE."TP",
    UE."PT",
    UE."AA",
    UE."Projet"
FROM {$this->tableName} as T
LEFT JOIN UE on UE."UE Code" = T.code_ecue
WHERE type_cours in ('Cours', 'Cours-TD', 'TD', 'TP');

SQL);

        $enseignementList = $stmt->fetchAll(\PDO::FETCH_CLASS);
        foreach($enseignementList as $enseignement){
            
            $syllabus = <<< SYLLABUS
## Semestre : {$enseignement->semestre}

## Objectifs d'apprentissage : 
{$enseignement->apprentissage}

## Programme/Plan/Contenus
{$enseignement->programme}

## Organisation générale de l'UE et modalités pédagogiques :
{$enseignement->organisation}

## Bibliographie
{$enseignement->bibliographie}

## Prérequis
{$enseignement->prerequis}

## Disciplines
{$enseignement->disciplines}

## Modalités pédagogiques particulières
{$enseignement->modalites}
SYLLABUS;

            $syllabus = $this->app::$viewer->render('MDtoHTML.html.twig', ['MD' => $syllabus]);

            $enseignementNS = $this->app::NS('Enseignement');
            $E = new $enseignementNS();
            $E->nuac = $enseignement->sigle??"";
            $E->code = $enseignement->code_ecue??$enseignement->sigle;
            $E->variante = $enseignement->sigle??"";  
            $E->nom = $enseignement->libelle??"";
            $E->intitule = $enseignement->initule??"";
            $E->attribuable = TRUE;
            $E->syllabus = $syllabus;

            $E->save();
        }
    }


    public function createVoeux(){
         $stmt = $this->pdo->query(
<<< SQL
SELECT DISTINCT
    uid,
    nom,
    prenom,
    code_ecue,
    sigle,
    type_cours,
    "hCM",
    "hCMTD",
    "hTD",
    "hTP",
    "hExtra"

FROM {$this->tableName}
WHERE type_cours in ('Cours', 'Cours-TD', 'TD', 'TP');
SQL);

        $voeuList = $stmt->fetchAll(\PDO::FETCH_CLASS);
        foreach($voeuList as $voeu){
            $voeuNS = $this->app::NS('Voeu');
            if (is_null($voeu->code_ecue)){
                $voeu->code_ecue = $voeu->sigle;
                var_dump($voeu->code_ecue);
            }

            if (is_null($voeu->uid)){
                $voeu->uid = strtolower("_{$voeu->prenom}.{$voeu->nom}");
                var_dump($voeu->uid);
            }

            $personne = $this->app::NS('Personne')::loadOneWhere("actif and id>0 and uid='{$voeu->uid}'");
            $enseignement= $this->app::NS('Enseignement')::loadOneWhere("actif and id>0 and code='{$voeu->code_ecue}'");
            $V = $this->app::NS('Voeu')::loadOneWhere("actif and id > 0 and personne = {$personne->id} and enseignement = {$enseignement->id}");
            if (is_null($V)){
                $V = new $voeuNS();
                $V->personne = $personne->id;
                $V->enseignement = $enseignement->id;
            }
            switch ($voeu->type_cours){
                case 'Cours': 
                    $V->cm = $voeu->hCM;
                    $enseignement->cm=1;
                    $enseignement->s_cm=1;
                    $enseignement->i_cm=1;
                    $enseignement->d_cm=1;
                    $enseignement->n_cm=1;
                    break;
                case 'Cours-TD': $V->ctd = $voeu->hCMTD;
                    $enseignement->td=1;
                    $enseignement->s_ctd=1;
                    $enseignement->i_ctd=1;
                    $enseignement->d_ctd=1;
                    $enseignement->n_ctd=1;
                    break;
                case 'TD': $V->td = $voeu->hTD;
                    $enseignement->td=1;
                    $enseignement->s_td=1;
                    $enseignement->i_td=1;
                    $enseignement->d_td=1;
                    $enseignement->n_td=1;
                    break;
                case 'TP': $V->tp = $voeu->hTP;
                    $enseignement->tp=1;
                    $enseignement->s_tp=1;
                    $enseignement->i_tp=1;
                    $enseignement->d_tp=1;
                    $enseignement->n_tp=1;
                    break;
            }
            $V->save();
            $enseignement->save();
        }
       
    }


    /**
     * test_unique_code_intitule
     *
     * @return array
     */
    public function test_unique_intitule(): array{
        $stmt = $this->pdo->query(
<<< SQL
SELECT 
   T.sigle,
   sum(1) as S
FROM (
   SELECT DISTINCT
      sigle, intitule
      FROM repartition_2025_2026
	  WHERE type_cours in ('Cours', 'Cours-TD', 'TD', 'TP')
   ) as T
GROUP BY sigle
HAVING S>1
ORDER BY S DESC;
SQL);
        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    /**
     * test_unique_code_ecue
     *
     * @return array
     */
    public function test_unique_code_ecue(): array{
        $stmt = $this->pdo->query(
<<< SQL
SELECT 
   T.sigle,
   sum(1) as S
FROM (
   SELECT DISTINCT
      sigle, code_ecue
      FROM repartition_2025_2026
	  WHERE type_cours in ('Cours', 'Cours-TD', 'TD', 'TP')
   ) as T
GROUP BY sigle
HAVING S>1
ORDER BY S DESC;
SQL);
        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

}
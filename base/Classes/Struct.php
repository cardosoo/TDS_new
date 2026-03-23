<?php

namespace base;

use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;

use Structure;
use StructureQuery;
use Etape;
use EtapeQuery;
use ECUE;
use ECUEQuery;
use ecue_etape;
use ecue_etapeQuery;
use Google\Service\Firestore\StructuredQuery;
use \Propel\Runtime\ActiveQuery\Criteria;
use stdClass;

function toInt($val){
    if (empty($val)){
        return 0;
    }
    return intval($val);
}

class Struct  {
    public String $structurePath;
    private static $defaultLogger = null;
    public String $configFile; 
    public String $year;
    
    private $codeList =  null;
    public $explain = false;

    public Array $structureList = [];
    public Array $etapeList = [];
    public Array $ecueList = [];

    public Array $coeffType = [
        "CM" => 1.50,
        "TD" => 1.00,
        "TP" => 1.00,
        "PROJET" => 1.00,
        "TD2" => 1.00,
        "MD" => 1.00,
        "CMTD7" => 1.16,
        "FORFAI" => 1.00,
        "CMTD" => 1.25,
        "TP7" => 1.00,
        "CMTP" => 1.16,
        "" => 1.0, 
    ];

    public Array  $importExtractionColumns = [
        "COD_ANU" => 'year',
        "COD_CMP" => 'codeComposante',
        "LIB_CMP" => 'libelleComposante',
        "COD_NEL" => 'typeElm',
        "COD_ELP" => 'codeElm',
        "LIC_ELP" => 'nomElm',
        "LIB_ELP" => 'libelleElm',
        "TEM_CAL_CHG" => 'temoinCalcul',
        "COD_ETP_PORTEUSE" => 'codeEtape',
        "LIB_ETP" => 'libelleEtape',
        "COD_VRS_VET_PORTEUSE" => 'versionEtape',
        "COD_TYP_HEU" => 'typeHeure',
        "LIC_TYP_HEU" => 'libelleTypeHeure',
        "COEFF_HETD" => 'coeffTypeHeure',
        "NBR_HEU_ELP" => 'heure',
        "NRM_ELP" => 'nbEtudiant',
        "NBR_MIN_ETU_GRP_SUPP" => 'maxEtuGrp',
    ];

    public Array $importMutualisationColumns = [
        "Année universitaire" => "year",
        "Version d'étape (code)" => "versionEtape",
        "ELP - Version étape porteuse (code)" => "versionEtapePorteuse",
        "Etape (lib.)" => "libelleEtape",
        "ELP (code)" => "codeElm",
        "ELP - Nature (code)" => "typeElm",
        "ELP (lib.)" => "libelleElm",
        "ELP - Part. calcul charges (O/N)" => "temoinCalcul",
        "ELP - Étape porteuse (code)" => "codeEtapePorteuse",
        "ELP - Étape porteuse (lib.)" => "",
        "ELP - Suspendu (O/N)" => "suspendu",
        "Type d’heure (lib. court)" => "libelleTypeHeure",
        "Nombre d’heures" => "heure",
        "Type d’heure (code)" => "typeHeure",
        "ELP - Section CNU principale  (lib.)" => "CNU",
        "Etape (code)" => "codeEtape",
        "Version d'étape - Composante (lib)" => "libelleComposante",
    ];

    public Array $importAjoutColumns = [
        "COD_ANU" => 'year',
        "LIB_CMP" => 'libelleComposante',
        "COD_ETP" => 'codeEtape',
        "COD_VRS_VET" => 'versionEtape',
        "LIB_ETP" => 'libelleEtape',
        "COD_ELP" => 'codeElm',
        "COD_NEL" => 'typeElm',
        "LIB_ELP" => 'libelleElm',
    ];

    public Array $typeDiplomes = [
        'AG' => 'AGREGATION',
        'BT' => 'BATCHELOR UNIVERSITAIRE DE TECHNOLOGIE',
        'CA' => 'CAPES',
        'DA' => 'D.A.E.U',
        'AU' => 'AUDITEUR',
        'PC' => 'PREPARATION CAPES',
        'CI' => 'CONVENTION INTERNATIONALE',
        'ER' => 'CONVENTION ERASMUS',
        'CP' => 'CAPACITÉ',
        'LG' => 'LICENCE GÉNÉRALE',
        'LP' => 'LICENCE PROFESSIONNELLE',
        'DT' => 'D.U.T',
        'MA' => 'MASTER',
        'MG' => 'MAGISTERE',
        'DR' => 'DOCTORAT',
        'HD' => 'H.D.R',
        'IG' => 'DIPLOME D\'INGÉNIEUR',
        'PA' => 'PACES',
        'PM' => 'DFGSM',
        'DM' => 'DFASM',
        'TM' => 'DFSSM',
        'PO' => 'DFGSO',
        'DO' => 'DFASO',
        'TO' => 'DFSSO',
        'PP' => 'DFGSP',
        'DP' => 'DFASP',
        'TP' => 'DFSSP',
        'TH' => 'THESE',
        'IF' => 'IFSI',
        'PF' => 'DFGSF',
        'DU' => 'D.U.',
        'DI' => 'D.I.U',
        'IJ' => 'IEJ',
        'DS' => 'D.E.S',
        'DC' => 'D.E.S.C',
        'QF' => 'QUALIFICATION',
        'FQ' => 'FORMATION QUALIFIANTE',
        'EZ' => 'ERASMUS',
    ];
    
    public Array $populationVET = [
        "LG" => [
            "AA" => "Anglais/Langues asiatiques",
            "AS" => "Arts du spectacle",
            "CH" => "Chimie",
            "DR" => "Droit",
            "EC" => "Économie",
            "EG" => "Économie, gestion",
            "FV" => "Frontières du vivant et de l'apprendre",
            "GA" => "Géographie et Aménagement",
            "HI" => "Histoire",
            "HD" => "Histoire/Allemand",
            "HA" => "Histoire/Anglais",
            "HG" => "Histoire/géographie",
            "IF" => "Informatique",
            "IQ" => "Informatique et Application",
            "LE" => "Langues étrangères appliquées",
            "LC" => "Langues, littératures et civilisations étrangères et régionales",
            "LT" => "Lettres",
            "LA" => "Lettres/Anglais",
            "LH" => "Lettres/histoire",
            "LL" => "Lettres/Sciences du langage",
            "MT" => "Mathématiques",
            "MQ" => "Mathématiques et Application",
            "MI" => "Mathématiques et Informatique", 
            "MA" => "Mathématiques et informatiques appliquées aux sciences humaines et sociales",
            "PH" => "Physique",
            "PY" => "Psychologie",
            "ST" => "Sciences de la terre",
            "SV" => "Sciences de la vie",
            "VT" => "Sciences de la Vie et de la Terre",
            "SE" => "Sciences de l'éducation",
            "SL" => "Sciences du langage",
            "SA" => "Sciences pour la santé",
            "SB" => "Sciences Biomédicales",
            "SS" => "Sciences sociales",
            "SO" => "Sociologie",
            "SP" => "STAPS",
            "MP" => "Mesures physique",
            "MS" => "Matériaux et structures : Gestion conception et industrialisation",
            "IG" => "Diplôme d'ingénieur",
            "CA" => "Chimie analytique, contrôle, qualité, environnement",
            "CP" => "Chimie et physique des matériaux",
        ],
        "LP" => [
            "AS" => "Assistance sociale",
            "CC" => "Assurance Banque Finance : chargé de clientèle",
            "SO" => "Assurance Banque Finance : supports opérationnels",
            "AB" => "Assurance, banque, finance",
            "BB" => "Bio-industries et biotechnologies",
            "CA" => "Chimie analytique, contrôle, qualité, environnement",
            "PM" => "Chimie et physique des matériaux",
            "EC" => "E-commerce et marketing numérique",
            "GS" => "Gestion des structures sanitaires sociales",
            "IP" => "Industries pharmaceutiques, cosmétologiques et de santé : gestion, production et valorisation",
            "IS" => "Intervention sociale : Accompagnement de publics spécifiques",
            "TM" => "Maintenance et technologie : Technologie médicale et biomédicale ",
            "AC" => "Management des activités commerciales",
            "MS" => "Matériaux et structures : Gestion conception et industrialisation",
            "CH" => "Métiers de la communication : chargé de communication",
            "QL" => "Métiers de l’informatique : test et qualité des logiciels",
            "CP" => "Métiers de la communication : chef de projet communication",
            "MF" => "Métiers de la forme",
            "RH" => "Métiers de la gestion des ressources humaines : assistant",
            "GC" => "Métiers de la gestion et de la comptabilité : révision comptable",
            "PG" => "Métiers de la protection et de la gestion de l'environnement",
            "TH" => "Métiers de la santé : technologies",
            "AA" => "Métiers de l'animation sociale, socioéducative et socioculturelle",
            "EE" => "Métiers de l'électricité et de l'énergie",
            "IM" => "Métiers de l'instrumentation, de la mesure et du contrôle qualité",
            "CI" => "Métiers du commerce international",
            "DS" => "Métiers du décisionnel et de la statistique",
            "DB" => "Métiers du livre : documentation et bibliothèques",
            "CL" => "Métiers du livre : édition et commerce du livre",
            "MT" => "Métiers du tourisme : commercialisation des produits touristiques",
            "QH" => "Qualité, hygiène, sécurité, santé, environnement",
            "SB" => "Sécurité des biens et des personnes",
            "SP" => "Services à la personne",
        ],
        "MA" => [
            "CH" => "Chimie",
            "CR" => "Création Artistique",
            "CV" => "Chimie et science du vivant",
            "DA" => "Droit des affaires",
            "DB" => "Droit bancaire et financier",
            "DC" => "Droit comparé",
            "DI" => "Droit international",
            "DL" => "Didactique des langues",
            "DN" => "Droit du numérique",
            "DP" => "Droit public",
            "DQ" => "Didactique des sciences",
            "DS" => "Droit de la santé",
            "DT" => "Droit notarial",
            "DV" => "Droit privé",
            "EA" => "Économie appliquée",
            "EE" => "Métiers de l'enseignement, de l'éducation et de la formation - Encadrement éducatif",
            "EN" => "Energie",
            "EO" => "STAPS : Entraînement et optimisation de la performance sportive",
            "ER" => "Ergonomie",
            "ES" => "Economie de la santé",
            "ET" => "Ethique",
            "GA" => "Géographie, aménagement, environnement et développement",
            "GN" => "Génétique",
            "HC" => "Histoire, civilisations, patrimoine",
            "HD" => "Histoire du droit",
            "IA" => "MIAGE",
            "IF" => "Informatique",
            "IS" => "Ingénierie de la santé",
            "JP" => "Justice, procès et procédure",
            "LC" => "Langues, littératures et civilisations étrangères et régionales",
            "LE" => "Langues étrangères appliquées",
            "LG" => "Littérature générale et comparée",
            "LH" => "Lettres et humanités",
            "MA" => "Mathématiques et applications",
            "MB" => "Monnaie, banque, finance, assurance",
            "MD" => "Sciences du médicament",
            "ME" => "Métiers des Etudes, du Conseil et de l'Intervention",
            "MG" => "Management",
            "MT" => "Mathématiques",
            "MV" => "Mathématiques, données et apprentissage",
            "NS" => "Neurosciences",
            "OT" => "SPACE (Observation de la terre, astrophysique, Ingénierie des satellites)",
            "PC" => "Psychologie clinique, psychopathologie et psychologie de la santé",
            "PE" => "Psychologie de l'éducation et de la formation",
            "PF" => "Physique fondamentale et applications",
            "PI" => "Pratiques et ingénierie de la formation",
            "PP" => "Psychopathologie clinique psychanalytique",
            "PS" => "Histoire et philosophie des sciences",
            "PT" => "Psychologie sociale du travail et des organisations",
            "PY" => "Psychologie",
            "RE" => "Risques et Environnement",
            "RH" => "Gestion des RH",
            "SC" => "Sciences cognitives",
            "SD" => "Métiers de l'enseignement, de l'éducation et de la formation - Second degré",
            "SE" => "Sciences de l'éducation",
            "SL" => "Sciences du langage",
            "SM" => "Science des matériaux avancés et nanotechnologie",
            "SO" => "Sociologie",
            "SP" => "Santé publique",
            "SS" => "Sciences sociales",
            "TI" => "Traduction, interprétation",
            "TP" => "Sciences de la Terre et des Planètes, Environnement",
            "TX" => "Toxicologie et éco toxicologie",
        ],
    
    ];
    
    public Array $semestreList = [
        "0" => 1,  // pour le AIPC
        "1" => 1,
        "2" => 2,
        "3" => 1,
        "4" => 2,
        "5" => 1,
        "6" => 2,
        "A" => 1,
        "B" => 2,
        "C" => 1,
        "D" => 2,
        "G" => 0,
        "H" => 0,
        "I" => 0,
        "0" => 0,
        "7" => 0,
        "E" => 0,
        "F" => 0,
        "P" => 0, // pour ISUPFERE
    ];

    public function __construct($year = null, $variant = ""){
        $app = App::get();

        $this->year = $year ?? $app::$currentYear;

        $plus = $app::$pathList['plus'];
        $log = $app::$pathList['log'];

        $fname = "";
        switch ($app::$structure){
            case 'UPCite':
                $fname ="config";
                break;
            case 'Saclay':
                $fname = "config_saclay";
        }
        
        $this->structurePath = "{$plus}/structure";
        $this->configFile = $this->structurePath."/generated-conf/{$fname}{$variant}.php";
        require_once $this->configFile;  
        
        if (is_null(self::$defaultLogger)){
            self::$defaultLogger = new Logger('defaultLogger');
            self::$defaultLogger->pushHandler(new StreamHandler("{$log}/propel.log", Level::Debug ));
            $serviceContainer->setLogger('defaultLogger', self::$defaultLogger);    
        }
    }
    
    public function getStructureList(){
        $structureList = StructureQuery::create()
        ->orderBy('nom')
        ->find();

        return $structureList;
    }

    public function getStructureByNom($nom){
        $structure = StructureQuery::create()
        ->filterByNom($nom)
        ->findOne();

        return $structure;
    }

    public function getEtapeByCode($codeEtape){
        $etape = EtapeQuery::create()
        ->filterByCode($codeEtape)
        ->findOne();

        return $etape;
    }

    public function getECUEByCode($codeECUE){
        $ecue = ECUEQuery::create()
        ->filterByCode($codeECUE)
        ->findOne();

        return $ecue;
    }

    public static function getCursusList(){
        return [
            (object)['id'=> 1, 'nom'=> 'L1', 'filter' => ['cursus' => 'LG1']],            
            (object)['id'=> 2, 'nom'=> 'L2', 'filter' => ['cursus' => 'LG2' ]],            
            (object)['id'=> 3, 'nom'=> 'L3', 'filter' => ['cursus' => 'LG3']],            
            (object)['id'=> 4, 'nom'=> 'L3Pro', 'filter' => ['type' => 'LP']],            
            (object)['id'=> 5, 'nom'=> 'M1', 'filter' => ['cursus' => 'MA1']],            
            (object)['id'=> 6, 'nom'=> 'M2', 'filter' => ['cursus' => 'MA2']],         
            //(object)['id'=> 7, 'nom'=> 'MEEF', 'filter' => ['type' => 'Master Enseignement']],
            (object)['id'=> 7, 'nom'=> 'Agreg', 'filter' => ['type' => 'AG']],
            (object)['id'=> 8, 'nom'=> 'IG1', 'filter' => ['cursus' => 'IG3']],
            (object)['id'=> 9, 'nom'=> 'IG2', 'filter' => ['cursus' => 'IG4']],
            (object)['id'=> 10, 'nom'=> 'IG3', 'filter' => ['cursus' => 'IG5']],
            (object)['id'=> 11, 'nom'=> 'PACES', 'filter' => ['type' => 'PA']],
        ];
    }

    public static function getCursusFromfilter($niveau, $type){
        $cursusList = self::getCursusList();
        $cursus = $type.$niveau;
        foreach($cursusList as $curs){
            $find = true;
            foreach($curs->filter as $key => $filter){
                if (${$key} !== $filter){
                    $find = false;
                }
            }
            if ($find) return $curs->nom;
        }
        return $cursus;
    }

    public static function getSemestreList(){
        return [
            (object)['id'=> 1, 'nom'=> 'semestre 1', 'filter' =>['periode' => '1']],
            (object)['id'=> 2, 'nom'=> 'semestre 2', 'filter' =>['periode' => '2']],
            (object)['id'=> 3, 'nom'=> 'annulalisé', 'filter' =>['periode' => '0']],
        ];        
    }

    public static function getModaliteList(){
        return [
            (object)['id'=> 1, 'nom'=> 'CM'],
            (object)['id'=> 2, 'nom'=> 'CMTD'],
            (object)['id'=> 3, 'nom'=> 'TD'],
            (object)['id'=> 4, 'nom'=> 'TP'],
            (object)['id'=> 5, 'nom'=> 'Extra'],
        ];        
    }

    public function parseFilter(Array $filter): Array{
        $app = \TDS\App::get();
        $where = [];
        $inDB = $filter['inDB'] ?? true;
        $outDB = $filter['outDB'] ?? false;
        $what = $filter['what'] ?? "";
        $structure = $filter['structure'] ?? [];
        $niveau = $filter['niveau'] ?? [];
        $type = $filter['type'] ?? [];
        $periode = $filter['periode'] ?? [];
        $cursus = $filter['cursus'] ?? [];
        $etape = $filter['etape'] ?? [];

        if (array_key_exists('actif', $filter )){
            $actif = $filter['actif']; // true (uniquement les actifs); false (uniquement les inactifs); null (actifs + inactifs) 
        } else {
            $actif = true;
        }


        if ($inDB){
            $where[] = "(EC.code IN {$this->getCodeList($actif)})";
        }

        if ($outDB){
            $where[] = "(EC.code NOT IN {$this->getCodeList($actif)})";
        }

        if (! empty($what)){
            $where[] = "((EC.search LIKE '%{$what}%') OR (EC.code LIKE '%{$what}%'))";
        }
/************************************************************************************************* */
        if (count($structure)>0){
            $structureList = [];
            foreach($structure as $s){
                $structureList[] = pg_escape_string($app::$db->conn, $s);
            }
            $joinStructure = join( "', '", $structureList);
            $where[] = "ST.nom in ('{$joinStructure}')";
        }

        if (count($niveau)>0){
            $joinNiveau = join( "', '", $niveau);
            $where[] = "ET.niveau in ('{$joinNiveau}')";
        }

        if (count($type)>0){
            $joinType = join( "', '", $type);
            $where[] = "ET.type in ('{$joinType}')";
        }

        if (count($periode)>0){
            $joinPeriode = join( "', '", $periode);
            $where[] = "EC.periode in ('{$joinPeriode}')";
        }

        if (count($cursus)>0){
            $joinCursus = join( "', '", $cursus);
            $where[] = "ET.type ||  ET.niveau in ('{$joinCursus}')";
        }

        if (count($etape)>0){
            $joinEtape = join( "', '", $etape);
            $where[] = "ET.nom in ('{$joinEtape}')";
        }
        return $where;
    }

    public function convertStrucureToFilter(Array &$filter, Array $cList, ?Array $idList){
        if (is_null($idList)) return [];

        if (!isset($filter['structure'])){
            $filter['structure'] = [];
        }    
        foreach($idList as $id){
            if(isset($cList[$id])){
                $filter['structure'][] = $cList[$id];
            }
        }
    }

    public function convertEtapeToFilter(Array &$filter, Array $cList, ?Array $idList){
        if (is_null($idList)) return [];

        if (!isset($filter['etape'])){
            $filter['etape'] = [];
        }    
        foreach($idList as $id){
            $filter['etape'][] = $cList[$id];
        }
    }


    public function convertIdToFilter(Array &$filter, String $what, ?Array $idList){
        if (is_null($idList)) return [];

        $fn = "get{$what}List";
        $cList = self::$fn();
        foreach($idList as $id){
            foreach($cList as $c){
                if ($c->id == $id){
                    foreach($c->filter as $k => $f){
                        if (!isset($filter[$k])){
                            $filter[$k] = [];
                        }    
                        $filter[$k][] = $f;
                    }
                }
            }
        }
    }

    /**
     * permet de construire le tableau des filtres à partir des sélecteurs du formulaire de recherche
     */
    public function buildFilterFromSelectors(Array $selectors): Array {
        $filter = [
            'actif' => $selectors['actif'],
        ];
        self::convertIdToFilter($filter, 'Cursus', $selectors['cursus']);
        self::convertIdToFilter($filter, 'Semestre', $selectors['semestre']);
        $structureList = $this->getUsefulStructureList($filter);
        self::convertStrucureToFilter($filter, $structureList, $selectors['structure']);
        $etapeList = $this->getUsefulEtapeList($filter);
        self::convertEtapeToFilter($filter, $etapeList, $selectors['etape']);
        return $filter;
    }

    public function getCodeList($actif=true){
        if (is_null($this->codeList)){
            $app = \TDS\App::get();
                
            // récupération des ECUE qui sont modélisés dans la foire
            $withActif = $actif?"AND E.actif\n":"";
            $withActif = "";
            if ( ! is_null($actif )){
                $withActif = "AND ".($actif?"":"NOT ")."E.actif ";
            }
            
            $sql = "
                SELECT DISTINCT
                    E.code
                FROM enseignement as E
                WHERE E.code is not NULL
                {$withActif}
                ORDER BY E.code            
            ";

            $ecueList = $app::$db->getAll($sql);


            $codeList = [];
            foreach($ecueList as $ecue){
                $cList = explode('|', $ecue->code);
                foreach($cList as $code){
                    $codeList[]=$code;
                } 
            }
            $codeList = array_unique($codeList);
            $codeListJoin = join("', '", $codeList);
            $this->codeList = "('{$codeListJoin}')";    
        }
        return $this->codeList;
    }
    

    public function getUsefulFilter(String $what, Array $filter, ?String $withId =  null): Array  {
        $con = \Propel\Runtime\Propel::getReadConnection(\Map\ecue_etapeTableMap::DATABASE_NAME);

        $where = $this->parseFilter($filter);
        $ordre = $filter['ordre'] ?? [];
        $withId = is_null($withId) ? "":"$withId as id, "; 

        $sql = "
            SELECT DISTINCT 
                {$withId}{$what}
            FROM ecue_etape as EE
            LEFT JOIN ecue as EC on EC.id = EE.ecue_id
            LEFT JOIN etape as ET on ET.id = EE.etape_id
            LEFT JOIN structure as ST on ST.id = ET.structure_id
            WHERE EC.id > 0
        ";
        $withId = $withId !== "";
        
        foreach($where as $W){
            $sql .=" AND {$W}\n";
        }
//var_dump($sql);
        if (count($ordre) > 0){
            $ordre = join(', ', $ordre);
            $sql .= "ORDER BY {$ordre}";
        }

if ($this->explain) {
    echo "<pre>";
    print_r([
        'filter' => $filter, 
        'where' => $where,
        "sql" => $sql,

    ], false);
    echo "</pre>";
}

        $stmt = $con->prepare($sql);
        $stmt->execute();
        $filterList = [];
        foreach($stmt->fetchAll() as $filter){
            if ($withId){
                $filterList[intval($filter['0'])] = $filter['1'];
            } else {
                $filterList[] = $filter['0'];
            }
        };
        return $filterList;
    }

    public function getUsefulTypeList(Array $filter): Array {
        $filter['ordre'] = ['ET.type'];
        return $this->getUsefulFilter('ET.type', $filter);
    }

    public function getUsefulNiveauList(Array $filter): Array {
        $filter['ordre'] = ['ET.niveau'];
        return $this->getUsefulFilter('ET.niveau', $filter);
    }

    public function getUsefulStructureList(Array $filter): Array {
        $filter['ordre'] = ['ST.nom'];
        return $this->getUsefulFilter('ST.nom', $filter, 'ST.id');
    }

    public function getUsefulPeriodeList(Array $filter): Array {
        $filter['ordre'] = ['EC.periode'];
        return $this->getUsefulFilter('EC.periode', $filter);
    }

    public function getUsefulEtapeList(Array $filter): Array {
        $filter['ordre'] = ['ET.nom'];
        return $this->getUsefulFilter('ET.nom', $filter, 'ET.id');
    }

    public function getECUEByFilter(Array $filter){
        $con = \Propel\Runtime\Propel::getReadConnection(\Map\ecue_etapeTableMap::DATABASE_NAME);
        $filter['ordre'] = ['EC.nom'];
        $idList = $this->getUsefulFilter('EC.id', $filter);
        $ecueList = ECUEQuery::create()->findPKs($idList);
        return $ecueList;
    }

    public function parseFilter_new(Array $filter): Array{
        $app = \TDS\App::get();
        $where = [];
        $structure = $filter['structure'] ?? [];
        $niveau = $filter['niveau'] ?? [];
        $type = $filter['type'] ?? [];
        $periode = $filter['periode'] ?? [];
        $cursus = $filter['cursus'] ?? [];
        $etape = $filter['etape'] ?? [];
        if (count($structure)>0){
            $structureList = [];
            foreach($structure as $s){
                $structureList[] = pg_escape_string($app::$db->conn, $s);
            }
            $joinStructure = join( "', '", $structureList);
            $where[] = "ST.nom in ('{$joinStructure}')";
        }

        if (count($niveau)>0){
            $joinNiveau = join( "', '", $niveau);
            $where[] = "ET.niveau in ('{$joinNiveau}')";
        }

        if (count($type)>0){
            $joinType = join( "', '", $type);
            $where[] = "ET.type in ('{$joinType}')";
        }

        if (count($periode)>0){
            $joinPeriode = join( "', '", $periode);
            $where[] = "EC.periode in ('{$joinPeriode}')";
        }

        if (count($cursus)>0){ // attention est-ce que cela fonctionne vraiment avec les licences pro par exemple (pas de niveau)
            $joinCursus = join( "', '", $cursus);
            $where[] = "ET.type ||  ET.niveau in ('{$joinCursus}')";
        }

        if (count($etape)>0){
            $etapeList = [];
            foreach($etape as $e){
                $etapeList[] = pg_escape_string($app::$db->conn, $e);
            }
            $joinEtape = join( "', '", $etapeList);
            $where[] = "ET.nom in ('{$joinEtape}')";
        }
        return $where;
    }


    // On essaye ici de faire le filtrage en prenant en compte 
    public function filter(Array $filter, Array $codeVarianteList): Array  {
        $con = \Propel\Runtime\Propel::getReadConnection(\Map\ecue_etapeTableMap::DATABASE_NAME);

//var_dump($codeVarianteList);

        $where = $this->parseFilter_new($filter);
        if (count($where) == 0){
            return $codeVarianteList;
        }
        
        $varianteList =  [];
        $codeList = [];
        foreach($codeVarianteList as $codeVariante){
            $code = $codeVariante['code'];
            $variante = $codeVariante['variante'];
            if ($code == $variante){
                $codeList[] = $code;
            } else {
                $varianteList[] = str_replace("'", "", $variante); // oui c'est moche mais si on ne supprime pas les apostrophe cela ne fonctionne pas.
            }
        }
        $codeSQL = "true";
        if (count($codeList) > 0 ){
            $joinCode = join( "', '", $codeList);
            $jcodeSQL = "EC.code in ('{$joinCode}')";    /****      ??????  pourquoi ce n'est pas utilisé ?*/
        }
        $varianteSQL = "true";
        if (count($varianteList) > 0 ){
            $joinVariante = join( "', '", $varianteList);
            $varianteSQL = "variante in ('{$joinVariante}')";    
        }
        $where[] = "( {$codeSQL} OR  {$varianteSQL} )";

        $sql = "
            SELECT DISTINCT
                EC.code,
                EC.code || ET.code as variante
            FROM ecue_etape as EE
            LEFT JOIN ecue as EC on EC.id = EE.ecue_id
            LEFT JOIN etape as ET on ET.id = EE.etape_id
            LEFT JOIN structure as ST on ST.id = ET.structure_id
            WHERE TRUE
        ";
        foreach($where as $W){
            $sql .=" AND {$W}\n";
        }

        if ($this->explain) {
            echo "<pre>";
            print_r([
                'filter' => $filter, 
                'where' => $where,
                "sql" => $sql,
            ], false);
            echo "</pre>";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute();


        $filterList = [];
        foreach($stmt->fetchAll() as $filter){

            if (isset($codeVarianteList[$filter['variante']])){
                $cv = $codeVarianteList[$filter['variante']];
                $filterList[$cv['id']]=$cv;
            }
            if (isset($codeVarianteList[$filter['code']])){
                $cv = $codeVarianteList[$filter['code']];
                $filterList[$cv['id']]=$cv;
            }
        };
// /Var_dump($filterList);        
        return $filterList;
    }

    /**
     * recherche dans les structures de ce qui contient le champ de recherche
     *  en cherchant dans l'ordre : 
     * - les structures
     * - les étapes
     * - les ecues
     * 
     */
    public function search($what){
        $app = App::get();
        $structureList = StructureQuery::create()
        ->filterByNom("%{$what}%", Criteria::LIKE)
        ->find();
        
        $what = $app::normalizeText($what);
        $etapeList = EtapeQuery::create()
        ->filterBySearch("%{$what}%", Criteria::LIKE)
        ->find();

        $ecueList = ECUEQuery::create()
        ->filterBySearch("%{$what}%", Criteria::LIKE)
        ->find();

        // on fait le tri pour savoir ce qui est dans la base de données et ce qui n'y ait pas.
        $codeList = [];
        foreach($ecueList as $ecue){
            $code = $ecue->getCode();
            $codeList[] = $code;
        }

        //WHERE title ~~ ANY('{%foo%,%bar%,%baz%}');

        //$codeList = "'".join("', '", $codeList)."'";
        //$inBase = [];
        //foreach(($app::NS('Enseignement'))::loadWhere(" code in ({$codeList}) ") as $E){
        $codeList = "'{%".join("%,%", $codeList)."%}'";
        $inBase = [];
        foreach(($app::NS('Enseignement'))::loadWhere(" code ~~ ANY ({$codeList}) ") as $E){
            foreach(explode('|', $E->code) as $code ){
                $code = trim($code);
                if (!isset($inBase[$code])){
                    $inBase[$code]=[];
                }
                $inBase[$code][]= $E; 
            }
        }

        $in = [];
        $out = [];
        foreach($ecueList as $ecue){
            $code = $ecue->getCode();
            if (isset($inBase[$code])){
                foreach($inBase[$code] as $E){
                    $in[] = ['ecue' => $ecue, 'enseignement' => $E];
                }
            } else {
                $out[] = $ecue;
            }
        }
        return [
            'sList' => $structureList,
            'eList' => $etapeList,
            'ecueInList' => $in,
            'ecueOutList' => $out,
        ];
    }


    /**
     * 
     * Ici on filtre les enseignements trouvés dans la base de données
     * à partir de ce qu'il y a dans les tables de structures.
     * - par cursus
     * - semestre
     * - structure
     * - etape
     * 
     * Il serait bien de pourvoir faire la distinction pour les étapes en prenant en compte 
     * la variante d'étape pour l'enseignement, mais pour cela il faut réfléchir
     * 
     */ 
    
    public function filterIdCodeList($filter, $idCodeVarianteList){
        /*
        $codeList = [];
        foreach($idCodeVarianteList as $idCode){
            $codeList[]= $idCode['code'];
        }
        */
        $idCodeVarianteList = $this->filter($filter, $idCodeVarianteList);
        return $idCodeVarianteList;
    }

    /**
     *   list($type, $domaine, $niveau, $annee, $semestre) = $struct->decodeCodeEtape($codeEtape);
     * 
     *   $niveau :  
     *   0 : pour la préparation à l'agrégation
     *   4 : pour les trucs avec sciences po
     *
     *  $domaine:
     *      LG, LP, MA ou ...
     *  $type :
     *      Licence générale, Licence professionelle, Master ou ... 
     * 
     *    sinon : 1 2 ou 3 pour les L et 1 ou 2 pour les M 
     *
     *  $semestre vaut 0, 1 ou 2 : 0 si l'ECUE est annulalisée 1 ou 2 sinon
     *  $annee vaut LG1, LG2 ou LG3 pour les licences générales, MA1, MA2 pour les Masters, LP3 pour les L3Pro  et sans doute d'autres choses vont sortir
     *  $sem est le semestre dans le diplome cela va de 1 à 6 pour les licences et de 1 à 4 pour les masters et sans doute d'autres choses vont sortir
     * 
     * 
     */
    public function decodeCodes($codeEtape, $codeECUE){
    
        $type = substr($codeEtape,0,2);
        $niveau = intval(substr($codeEtape, 5,1));

        $semestre = substr($codeECUE,3,1);
        $annee = $type.$niveau;
        $domaine = $this->typeDiplomes[$type]??$type;


        $semestre = $this->semestreList[$semestre]??-1;

        return [$type, $domaine, $niveau, $annee, $semestre];
    }

    protected function getComposante(Object $arr, bool $inMem): Structure|null{
        if ($inMem){
            $composante = & $this->structureList[$arr->codeComposante] ?? null;
        } else {
            $composante = StructureQuery::create()
            ->filterByNom($arr->libelleComposante)
            ->findOne();
        }
        return $composante;
    }

    protected  function getComposanteOrCreate(Object $arr, bool $inMem): Structure{
        $composante = $this->getComposante($arr, $inMem);
        $toCreate = is_null($composante);

        if ($toCreate){
            $composante = new Structure();
            $composante->setNom($arr->libelleComposante);
            $composante->setOseId($arr->codeComposante);
            $composante->setOseNom($arr->libelleComposante);
            $composante->save();
            if ($this->explain){
                var_dump("Création Composate : {$arr->libelleComposante}"); flush();
            }
            if ($inMem){
                $this->structureList[$arr->codeComposante] = $composante;
            }
        }
        return $composante;
    }

    protected function getEtape(Object $arr, bool $inMem): Etape|null{
        if ($inMem){
            $etape = & $this->etapeList[$arr->codeEtape] ?? null;
        } else {
            $etape = EtapeQuery::create()
            ->filterByCode($arr->codeEtape)
            ->findOne();
        }
        return $etape;
    }

    protected  function getEtapeOrCreate(Object $arr, bool $inMem): Etape{
        $etape = $this->getEtape($arr, $inMem);
        $toCreate = is_null($etape);

        $composante = $this->getComposanteOrCreate($arr, $inMem);

        list($type, $domaine, $niveau, $annee, $semestre) = $this->decodeCodes($arr->codeEtape, $arr->codeElm);

        if ($toCreate){
            var_dump("Création étape : {$arr->libelleComposante} ->  {$arr->codeElm} - {$arr->libelleEtape}");   flush(); 
            $etape = new Etape();
            $etape->setCode($arr->codeEtape);
            $etape->setNom($arr->libelleEtape);
            $etape->setType($type);
            $etape->setNiveau($niveau);
            $etape->setDomaine($domaine);
            $etape->setEffectif(0);
            $etape->setStructure($composante);
            $etape->setOseId($arr->codeEtape);
            $etape->setOseNom($arr->libelleEtape);
            $etape->save();
            if ($inMem){
                $this->etapeList[$arr->codeEtape] = $etape;
            }
        }
        return $etape;
    }

    protected function getEcueEtape(ECUE $ecue, Etape $etape): ecue_etape|null{
        // il faudrait quand même vérifier quelle n'existe pas !
        $EE = ecue_etapeQuery::create()
            ->filterByEtape($etape)
            ->filterByECUE($ecue)
            ->findOne();
        return $EE;
    }
    
    protected function getEcueEtapeOrCreate(ECUE $ecue, Etape $etape, string $source="Indéfini"): ecue_etape{

        $EE = $this->getEcueEtape($ecue, $etape);
        if (is_null($EE)){
            if ($this->explain){
                var_dump("Création Association Ecue Etape : {$ecue->getCode()} - {$ecue->getNom()} <-> {$etape->getCode()} - {$etape->getNom()}"); flush();
            }
            $EE = new ecue_etape();
            $EE->setECUE($ecue);                    
            $EE->setEtape($etape);
            $EE->setEffectif(0);
            $EE->setSource($source);
            $EE->save();    
        }
        return $EE;
    }

    protected function getEcue(Object $arr, bool $inMem): ECUE|null{
        if ($inMem){
            $ecue = & $this->ecueList[$arr->codeElm] ?? null;
        } else {
            $ecue = EcueQuery::create()
            ->filterByCode($arr->codeElm)
            ->findOne();
        }
        return $ecue;
    }
    
    protected function getEcueOrCreate(Object $arr, bool $inMem, bool $isMainEtape, string $source='Indéfini'): ECUE{
        $ecue = $this->getEcue($arr, $inMem);
        $toCreate = is_null($ecue);

        list($type, $domaine, $niveau, $annee, $semestre) = $this->decodeCodes($arr->codeEtape, $arr->codeElm);
        $etape = $this->getEtapeOrCreate($arr, $inMem);

        if ($toCreate){
            if ($this->explain){
                var_dump("Création de l'ecue : {$arr->codeElm} - {$arr->libelleElm}"); flush();
            }
            $ecue = new ECUE();
            $ecue->setCode($arr->codeElm);
            $ecue->setNom($arr->libelleElm);
            $ecue->setPeriode($semestre);
            $ecue->setEtape($etape);
            $ecue->setOseNom($arr->nomElm);
            $ecue->setEffectif($arr->nbEtudiant);
            $besoins = [];
            $ecue->setBesoinsArray($besoins);
            $ecue->save();
            if ($inMem){
                $this->etapeList[$arr->codeElm] = $ecue;
            }
            if ($isMainEtape){
                // $this->getEcueEtapeOrCreate($ecue, $etape, true, $source);
                $this->getEcueEtapeOrCreate($ecue, $etape, $source);
            }
        } else {
            $besoins = $ecue->getBesoinsArray();
        }
        // "COD_TYP_HEU" => 'typeHeure',
        // "LIC_TYP_HEU" => 'libelleTypeHeure',
        // "COEFF_HETD" => 'coeffTypeHeure',
        // "NBR_HEU_ELP" => 'heure',
        // "NRM_ELP" => 'nbEtudiant',
        // "NBR_MIN_ETU_GRP_SUPP" => 'maxEtuGrp',
        if ($arr->typeHeure != "MUT"){
            $b = $besoins[$arr->typeHeure] ?? ['coeffTypeHeure' => (float)$arr->coeffTypeHeure, "heures" => 0, 'nbEtudiant' => 0];
            $b['heures'] = (float)$arr->heure;
            $b['nbEtudiant'] += (float)$arr->nbEtudiant;
            $besoins[$arr->typeHeure] = $b;
        }
        $ecue->setBesoinsArray($besoins);

        if (!$inMem){
            $ecue->save();
        }
        return $ecue;
    }


    public function canCreateEnseignementInDatabase(ECUE $ecue){
        $app = \TDS\App::get();
        
        if (!$app::$auth->isAuth) return false; // si pas d'authentification alors non
        if (!$app::$auth->user->actif)  return false; // si pas actif alors non
        // Par défaut seuls les administrateurs peuvent ajouter un enseignement
//        return $app::$auth->isAdmin;
        return $app::$auth->hasRole('Admin');

    }

    public function importExtractionLine(Array $row, bool $inMem){
        
        $importArray = (object)array_combine($this->importExtractionColumns, $row);
        
        $composante = $this->getComposanteOrCreate($importArray, $inMem);
        $etape = $this->getEtapeOrCreate($importArray, $inMem);
        $ecue = $this->getEcueOrCreate($importArray, $inMem, true, 'Extraction');
    }

    protected function importNormalMutualisationLine(Object $arr, bool $inMem, bool $force, string  $source='Indéfini'){
        $app = \TDS\App::get();

        // Dans cette version on ajoute la ligne uniquement si l'étape porteuse exsite déjà
        // dans la base de données sauf si on force (c'est le cas lorsqu'il s'agit d'un ajout)

        if (! $force){
            $etape = EtapeQuery::create()
                ->filterByCode($arr->codeEtapePorteuse)
                ->findOne();
            if (is_null($etape)){
                return;
            }
        }
        $composante = $this->getComposanteOrCreate($arr, $inMem);
        $etape = $this->getEtapeOrCreate($arr, $inMem);
        $ecue = $this->getEcueOrCreate($arr, $inMem, false);
        $EE = $this->getEcueEtapeOrCreate($ecue, $etape, $source);

    }

    protected function suppMutualisationLine(Object $arr, bool $inMem){
        $app = \TDS\App::get();

        $ecue = $this->getEcue($arr, $inMem);
        $etape = $this->getEtape($arr, $inMem);
        $EE = $this->getEcueEtape($ecue, $etape);
var_dump([
    "ecue" => $ecue,
    'etape' =>$etape,
    'EE' => $EE,
]);
        if (!is_null($EE)){
var_dump(["suppression" => $EE]);            
            $EE->delete();
        }
        if ($ecue->getEtapeId() == $etape->getId()){ // si l'étape était l'étape principale alors il faut changer l'étape principale
            $etapeList = $ecue->getecue_etapes();
            if (count($etapeList)>0 ){
                $ecue->setEtape($etapeList[0]->getEtape());
                $ecue->save();
            } else { // si il n'y a plus d'enseignment rattaché à l'étape alors on supprime l'ecue
                if ($inMem){
                    unset($this->ecueList[$ecue->getCode()]);
                }
                $ecue->delete();
            }
        }
    }

    public function importMutualisationLine(Array $row, bool $inMem, bool $force=false, string $source='Indéfini'){
        $app = \TDS\App::get();
        $importArray = (object)array_combine($this->importMutualisationColumns, $row);

        if ($importArray->year != $app::$currentYear) return;

        if (str_starts_with($importArray->libelleEtape ,'ERASMUS')) return;

        $importArray->codeComposante = substr($importArray->codeEtape, 0,4);
        $importArray->nomElm = $importArray->libelleElm;
        $importArray->nbEtudiant = 0;
        $importArray->coeffTypeHeure = $this->coeffType[$importArray->typeHeure];

        $this->importNormalMutualisationLine($importArray, $inMem, $force, $source);
    }


    public function importAjoutLine(Array $row, bool $inMem, bool $force=false, string $source='Indéfini'){
        $app = \TDS\App::get();
        /*
        $importArray = (object)array_combine($this->importExtractionColumns, $row);
        
        $composante = $this->getComposanteOrCreate($importArray, $inMem);
        $etape = $this->getEtapeOrCreate($importArray, $inMem);
        $ecue = $this->getEcueOrCreate($importArray, $inMem);
        */
        $importArray = (object)array_combine($this->importAjoutColumns, $row);
        $importArray->codeComposante = substr($importArray->codeEtape, 0,4);
        $importArray->nomElm = $importArray->libelleElm;
        $importArray->coeffTypeHeure = 1;
        $importArray->nbEtudiant = 0;
        $importArray->typeHeure = 'Extra';
        $importArray->heure = 0;

        switch ($importArray->year) {
            case "year":
            case "":
            case "Année universitaire":
                break;
            case "SUPP":
                $this->suppMutualisationLine($importArray, $inMem);
                break;
            case $app::$currentYear:
                $this->importNormalMutualisationLine($importArray, $inMem, $force, $source);
            default:
                break;
        } 
    }

    public function importExtraction(){

        $inMem = false;

        $app = \TDS\App::get();
        //  M27; M30; S08; S30; S31; S34; S36; IPG; S56; S76; M00
        // M27; M30; S30; S31; S32; S34; S36; IPG; H41; H43; H44; H46; H48; H49; S51; H50; P55; S56; S75; S76; H71; H78; H77; P89; P94; 097; H16; S08; M11; S05; H14; H25; IFD; USI; UNI 
       
        ini_set( 'max_execution_time', 0 ); 
        
        $file = new \SplFileObject("{$this->structurePath}/extraction{$app::$currentYear}.csv");
        $file->setFlags(\SplFileObject::READ_CSV);
        $file->setCsvControl(';');


        $this->structureList = [];
        $this->etapeList = [];
        $this->ecueList = [];

        $first = true;
        $len = 0;
        foreach ($file as $row) {
            if ($first){
                $len = count($row);
                $first = false;
                continue;
            }
            if( count($row) < $len ) {
                continue;
            };
            $this->importExtractionLine($row, $inMem);
        }
        foreach ($this->ecueList as $ecue) {
            $ecue->save();
        }
    }

    public function importMutualisation(){
        // Importation du fichier mutualisationNNNN.csv 
        // grep '^"2025"' Rapport\ 2.csv > mutualisation2025.csv

        // à partir de l'extration BO dans laquelle on a conservé que les colonnes de 1 à 11 et supprimé les doublons : 
        // `F=mutualisation2025.csv; head -n 1 $F >new_$F; tail -n +2 $F | cut -d\; -f2-11 | sort | uniq >>new_$F;`
        $app = \TDS\App::get();

        ini_set( 'max_execution_time', 0 ); 
        
        $file = new \SplFileObject("{$this->structurePath}/mutualisation{$app::$currentYear}.csv");
        $file->setFlags(\SplFileObject::READ_CSV);
        $file->setCsvControl(';');

        $first = true;
        $len = 0;
        foreach ($file as $row) {
            if ($first){
                $len = count($row);
                $first = false;
                continue;
            }
            if( count($row) < $len ) {
                continue;
            };
//var_dump($row);
            $this->importMutualisationLine($row, false, false, 'Mutualisation');   
        }
    }

    public function importAjout(){
        // Importation du fichier ajoutNNNN.csv 
        // construit quasiment sur le même modèle que mutualisation2025.csv
var_dump("Import Ajout");
        $this->explain = true;

        $app = \TDS\App::get();

        ini_set( 'max_execution_time', 0 ); 
        
        $file = new \SplFileObject("{$this->structurePath}/ajout{$app::$currentYear}.csv");
        $file->setFlags(\SplFileObject::READ_CSV);
        $file->setCsvControl(';');

        $first = true;
        $len = 0;
        foreach ($file as $row) {
            if ($first){
                $len = count($row);
                $first = false;
                continue;
            }
            if( count($row) < $len ) {
                continue;
            };
            $this->importAjoutLine($row, false, true, 'Ajout');   
        }
    }

    public function correctionEtape(){
var_dump("correction des étapes");
        $this->explain = true;

        $app = \TDS\App::get();

        ini_set( 'max_execution_time', 0 ); 
        
        $file = new \SplFileObject("{$this->structurePath}/correctionEtape.csv");
        $file->setFlags(\SplFileObject::READ_CSV);
        $file->setCsvControl(';');
var_dump($file);
        $first = true;
        $len = 0;
        foreach ($file as $row) {
var_dump($row);
            if ($first){
                $len = count($row);
                $first = false;
                continue;
            }
            if( count($row) < $len ) {
                continue;
            };
            $codeEtape = $row[0];
            $nom = $row[1];

            $etape = EtapeQuery::create()
                ->filterByCode($codeEtape)
                ->findOne();

            if ($etape) {
                $etape->setNom($nom);
                $etape->save();
                var_dump("Étape mise à jour : {$etape->getNom()}");
            } else {
                var_dump("Aucune étape trouvée avec le code $codeEtape.");
            }
        }

    }

    public function getEtapeList($structureId, $cursusId){
        $con = \Propel\Runtime\Propel::getReadConnection(\Map\ecue_etapeTableMap::DATABASE_NAME);
       

        $cursusList = $this->getCursusList();
        foreach($cursusList as $cursus){
            if ($cursus->id == $cursusId){
                break;
            }
        }

//var_dump($cursusId, $cursus);

        $filter = is_null($cursus)?[]:['cursus' => $cursus->filter];
        $filter['inDB'] = false;
        $filter['outDB'] = false;
//var_dump($filter);

        $where = $this->parseFilter($filter);

//var_dump($where);


        $sql = "
        SELECT DISTINCT
            id,
            nom
        FROM Etape as ET
        WHERE TRUE
        AND ET.structure_id = {$structureId}
        ";
        foreach($where as $W){
            $sql .=" AND {$W}\n";
        }
        $sql .="ORDER BY nom";

//var_dump($sql);

        $stmt = $con->prepare($sql);
        $stmt->execute();

        $etapeList = [];
        foreach($stmt->fetchAll() as $etape){
            // var_dump($etape);
            $etapeList[intval($etape['0'])] = $etape['1'];
        };
        // var_dump($etapeList);
        return $etapeList;

    }

    public function getEcueList($structureId, $cursusId, $semestreId, $etapeId){
        $con = \Propel\Runtime\Propel::getReadConnection(\Map\ecue_etapeTableMap::DATABASE_NAME);
        $cursusList = $this->getCursusList();
        foreach($cursusList as $cursus){
            if ($cursus->id == $cursusId){
                break;
            }
        }

        $semestreList = $this->getSemestreList();
        foreach($semestreList as $semestre){
            if ($semestre->id == $semestreId){
                break;
            }
        }
/*
var_dump([
    'structureId' => $structureId, 
    'cursusId' => $cursusId,
    'cursus' => $cursus, 
    'semestreId' => $semestreId,
    'semestre' => $semestre, 
    'etapeId' => $etapeId
]);
*/
        $filter = [];
        $filter['inDB'] = false;
        $filter['outDB'] = false;
        if (is_int($cursusId)){
            self::convertIdToFilter($filter, 'Cursus', [$cursusId]);
            // $filter['cursus'] = $cursus->filter;
        }
//var_dump($filter, $semestreId);        
        if (is_int($semestreId)){
            self::convertIdToFilter($filter, 'Semestre',[$semestreId]);
//var_dump($filter);        
            //$filter['periode'] = $semestre->filter;
        }

        if (is_int($structureId)){
            $tmp =  new StructureQuery();
            $filter['structure'] = [$tmp->findPK($structureId)->getNom()];
        }
        if (is_int($etapeId)){
            $tmp =  new EtapeQuery();
            $filter['etape'] = [$tmp->findPK($etapeId)->getNom()];
        }
        $where = $this->parseFilter($filter);

        $sql = "
            SELECT DISTINCT
                EC.id
            FROM ecue_etape as EE
            LEFT JOIN ecue as EC on EC.id = EE.ecue_id
            LEFT JOIN etape as ET on ET.id = EE.etape_id
            LEFT JOIN structure as ST on ST.id = ET.structure_id
            WHERE TRUE
        ";
        foreach($where as $W){
            $sql .=" AND {$W}\n";
        }
        $sql .="ORDER BY EC.code";

//var_dump($sql);

        $stmt = $con->prepare($sql);
        $stmt->execute();

        $ecueList = [];
        foreach($stmt->fetchAll() as $ecue){
            //var_dump($ecue);
            $ecueList[] = $ecue['0'];
        };
//var_dump($ecueList);
        $tmp = new ECUEQuery();
        $ecueList = $tmp->findPks($ecueList);
//var_dump($ecueList);
        return $ecueList;

    }

    public static function cmpECUE($a, $b){
        if ($a['c'] == $b['c']) {
            return 0;
        }
        return ($a['c'] < $b['c']) ? -1 : 1;
    }


    public function getInOutFromEcueList($ecueList, $inBase){
        $in = [];
        $out = [];
        foreach($ecueList as $ecue){
            $code = $ecue->getCode();
            if (isset($inBase[$code])){
                foreach($inBase[$code] as $E){
                    $in[] = ['ecue' => $ecue, 'enseignement' => $E, 'besoins' => $E->getBesoins()];
                }
            } else {
                $out[] = $ecue;
            }
        }
            
        // pour ordonner tout cela par le nombre d'étapes qui sont rattachées à l'ecue
        $tmp = [];
        foreach($out as $ecue){
            $nb = count($ecue->getecue_etapes());
            $tmp[] = ['c' => $nb, 'ecue' => $ecue];
        }
        usort($tmp, [self::class, "cmpECUE"] );
        $out = $tmp;

        // recherche pour voir si on trouve des choses du côté des vacations
        // pour les vacation out
        foreach($out as $key => $elm){
            $app = \TDS\App::get();            
            $code = $elm['ecue']->getCode();
            $vacationList = \base\Vacation::getVacationFromEcueCode($code);
            $coutIn = 0;
            $coutOut = 0;
            foreach($vacationList as $vacation){
                if ($vacation->inDB == 'out'){
                    $coutOut += $vacation->hEQTD;
                } else {
                    $coutIn += $vacation->hEQTD;  // normalement c'est toujours 0 ça !
                }
            }
            $out[$key]['vacIn'] = $coutIn;
            $out[$key]['vacOut'] = $coutOut;
        }
        return [$in, $out];
    }

    public function addVacation( & $tab){
        foreach($tab as $key => $elm){
            $app = \TDS\App::get();            
            $code = $elm['ecue']->getCode();
            $vacationList = \base\Vacation::getVacationFromEcueCode($code);
            $coutIn = 0;
            $coutOut = 0;
            foreach($vacationList as $vacation){
                if ($vacation->inDB == 'out'){
                    $coutOut += $vacation->hEQTD;
                } else {
                    $coutIn += $vacation->hEQTD;  // normalement c'est toujours 0 ça !
                }
            }
            $tab[$key]['vacIn'] = $coutIn;
            $tab[$key]['vacOut'] = $coutOut;
        }
        return $tab;
    }
    


    /**
     * Undocumented function
     *
     * @param [string] $codeList
     * @return [\foire\Enseignement]
     */
    public function getInBaseEnseignementFromEcueList(array $ecueList): array {
        $app = \TDS\App::get();

        $codeList = [];
        foreach($ecueList as $ecue){
            $codeList[] = $ecue->getCode();
        }
        $codeList = "'{%".join("%,%", $codeList)."%}'";

        $inBase = [];
        foreach(($app::NS('Enseignement'))::loadWhere(" code ~~ ANY ({$codeList}) ") as $E){
            foreach(explode('|', $E->code) as $code ){
                $code = trim($code);
                if (!isset($inBase[$code])){
                    $inBase[$code]=[];
                }
                $inBase[$code][]= $E; 
            }
        }
        return $inBase;
    }

}

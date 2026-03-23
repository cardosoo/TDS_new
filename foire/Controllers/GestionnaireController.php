<?php
namespace foire\Controllers;

use stdClass;

class GestionnaireController extends \base\Controllers\GestionnaireController {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('gestionnaire/index.html.twig');
    }


    public static function getNbPersonnels($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName.$year;
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost');

        return $db->getAll("
            SELECT
                CASE 
                    WHEN S.obligation  = 32 
                        THEN '32 - ' || S.nom
                    WHEN S.obligation  = 64 
                        THEN '64 - ' || S.nom
                    ELSE S.nom
                END as nom,
                sum(1) as nb
            FROM personne as P
            LEFT JOIN statut as S on S.id= P.statut
            WHERE S.nom not ilike 'Ancien%' 
            GROUP BY S.nom, S.obligation
            ORDER BY S.nom
        ");
    }


    public static function getRatioHF($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName.$year;
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost');
        $id = 'num';
        $personne = 'enseignant';
        $nom_statut = 'nom_court';
        $civilite = 'civilite';
        $nom_civilite = 'nom_long';

        return $db->getAll("
            SELECT
            S.{$nom_statut} || ' - ' || C.{$nom_civilite} as civilite,
                sum(1) as nb
            FROM {$personne} as P
            LEFT JOIN statut as S on S.{$id}= P.statut
            LEFT JOIN civilite as C on C.{$id}=P.{$civilite}
            WHERE S.{$nom_statut} in ('MCF', 'PROF') 
            GROUP BY S.{$nom_statut}, C.{$nom_civilite}
            ORDER BY S.{$nom_statut}
        ");
    }

    public static function getRepartitionNiveauOld($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName.$year;
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost');
        $id = 'num';
        $personne = 'enseignant';
        $nom_statut = 'nom_court';
        $civilite = 'civilite';
        $nom_civilite = 'nom_long';

        return $db->getAll("
        SELECT 
            sum(E.cours * E.s_cours * E.d_cours * E.i_cours * 1.5) as cm,
            sum(E.ctd * E.s_ctd * E.d_ctd * E.i_ctd * 1.14) as ctd,
            sum(E.td * E.s_td * E.d_td * E.i_td * 1) as td,
            sum(E.tp * E.s_tp * E.d_tp * E.i_tp * 1) as tp,
            sum(E.colle * E.s_colle * E.d_colle * E.i_colle * 1) as colle,
            sum(E.bonus) as bonus


        FROM enseignement as E
        LEFT JOIN enseignement_structure as ES
        WHERE E.num > 0;
        ");
    }


    public static function getRepartitionNiveauSE($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName.$year;
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost');
        $id = 'num';
        $personne = 'enseignant';
        $nom_statut = 'nom_court';
        $civilite = 'civilite';
        $nom_civilite = 'nom_long';

        return $db->getAll("
        SELECT
            ES.cursus as cursus,
            sum(E.cm  * E.s_cm  * E.d_cm  * E.i_cm  * 1.5
              + E.ctd * E.s_ctd * E.d_ctd * E.i_ctd * 1.14
              + E.td  * E.s_td  * E.d_td  * E.i_td  * 1
              + E.tp  * E.s_tp * E.d_tp * E.i_tp * 1
              + E.extra * E.s_extra * E.d_extra * E.i_extra * 1
              + E.bonus) as hETD

        FROM enseignement as E
        LEFT JOIN enseignement_structure as ES on ES.id=E.id
        WHERE E.id > 0
        AND E.actif
        GROUP BY ES.cursus
        ;

        ");
    }
    public static function getRepartitionNiveauStatutOld($year){
        return [];
    }

    public static function getRepartitionNiveauStatutSE($year){
        $app = \TDS\App::get();

        $baseName = $app::$appName.$year;
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost');

        return $db->getAll("
        SELECT
            ES.cursus,
            S.nom as statut,
            sum(VBL.heures) as hETD
        FROM voeu as V
        LEFT JOIN voeu_bilan_ligne as VBL on VBL.id = V.id
        LEFT JOIN enseignement as E on E.id=V.enseignement
        LEFT JOIN personne as P on P.id = V.personne
        LEFT JOIN enseignement_structure as ES on ES.id=E.id
        LEFT JOIN statut as S on S.id = P.statut
        WHERE E.actif AND P.actif
        GROUP BY ES.cursus, S.nom
        ;

        ");
    }



    public static function getRepartitionNiveau($year){
        if ($year<2020){
            return self::getRepartitionNiveauOld($year);
        }
        return self::getRepartitionNiveauSE($year);
    }


    public static function getRepartitionNiveauStatut($year){
        if ($year<2020){
            return self::getRepartitionNiveauStatutOld($year);
        }
        return self::getRepartitionNiveauStatutSE($year);
    }


    public static function getNameListOne($tab){
        $nameList = []; // on commence par mettre les noms
        foreach($tab as $data){
            $name = reset($data);
            if (! in_array($name, $nameList)){
                $nameList[]=$name;
            }
        }
        return $nameList;
    }

    /**
     * En entrée on a un tableau dont chaque élément a 
     * - une clé qui est l'année
     * - la valeur est un objet dont 
     *     - le premier élément contient le nom de l'élément
     *     - le deuxième élement contient la valeur
     * En sortie on a un tableau avec tous les noms trouvés.
     * 
     */
    public static function getNameList($tab){
        $nameList = []; // on commence par mettre les noms
        foreach($tab as $dataYear){
            foreach($dataYear as $data){
                $name = reset($data);
                if (! in_array($name, $nameList)){
                    $nameList[]=$name;
                }
            }
        }
        return $nameList;
    }

    public static function reformatOne($tab, $nameList){
        $obj = new \stdClass;
        foreach($nameList as $name){
            $obj->$name = null;
        }
        foreach($tab as $data){
            $obj->{reset($data)} = next($data);
        }
        return $obj;
    }

    public static function reformat($tab, $nameList){
        $res = [];
        foreach($tab as $year => $dataYear){
            $res[$year]=self::reformatOne($dataYear, $nameList);
        }
        return $res;
    }

/*
    public static function preformatEffectifsGlobaux($tab){
        $res = [];
        foreach($tab as $year => $dataYear){
            $elm = [];
            foreach($dataYear as $data){
                if ( 'Missio' == substr($data->nom, 0, 6)){
                    $data->nom = 'Missionnaire';
                    $data->nb *= $data->obligation/64;
                }
                $elm[]=$data;
            }
            $res[$year] = $elm;
        }
        return $res;
    }
*/


    public static function effectifsGlobaux(){
        $app = \TDS\App::get();

        $yearList = [2022, 2021, 2020, 2019, 2018, 2017, 2016, 2015];
        
        $effGL = [];
        foreach($yearList as $year){
            $effGL[$year] = self::getNbPersonnels($year);
        }
        //$effGL = self::preformatEffectifsGlobaux($effGL);
        $nameList = self::getNameList($effGL);
        sort($nameList);        
        $effGL = self::reformat($effGL, $nameList);

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('admin/standardListByYear.html.twig',['title' => 'Effectifs Globaux', 'nameList' => $nameList, 'data'=> $effGL]);
    }

    public static function ratioHF(){
        $app = \TDS\App::get();

        $yearList = [2020, 2019, 2018, 2017, 2016, 2015];
        
        $ratioHF = [];
        foreach($yearList as $year){
            $ratioHF[$year] = self::getRatioHF($year);
        }
        $nameList = self::getNameList($ratioHF);
        sort($nameList);        
        $ratioHF = self::reformat($ratioHF, $nameList);

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('admin/standardList.html.twig',['title' => 'Ratio Hommes/Femmes', 'nameList' => $nameList, 'data'=> $ratioHF]);
    }


    public static function repartitionNiveau(){
        $app = \TDS\App::get();

//        $yearList = [2020, 2019, 2018, 2017, 2016, 2015];
        $yearList = [2021];
        
        $repartitionNiveau = [];
        foreach($yearList as $year){
            $repartitionNiveau[$year] = self::getRepartitionNiveau($year);
        }

        $nameList = self::getNameList($repartitionNiveau);
        sort($nameList);        
        $repartitionNiveau = self::reformat($repartitionNiveau, $nameList);

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;



        echo $app::$viewer->render('admin/standardListByYear.html.twig',['title' => 'Répartition par niveau en hETD', 'nameList' => $nameList, 'data'=> $repartitionNiveau]);
    }


    public static function getList($tab, $item){
        $list = [];

        foreach($tab as $dataYear){
            foreach($dataYear as $data){
                if (! in_array($data->{$item}, $list)){
                    $list[] = $data->{$item}; 
                }
            }
        }
        return $list;

    }

    public static function repartitionNiveauStatut(){
        $app = \TDS\App::get();

//        $yearList = [2020, 2019, 2018, 2017, 2016, 2015];
        $yearList = [2023, 2022, 2021, 2020]; //, 2019, 2018, 2017, 2016, 2015];
        
        $repartitionNiveau = [];
        foreach($yearList as $year){
            $repartitionNiveau[$year] = self::getRepartitionNiveauStatut($year);
        }

        $cursusList = self::getList($repartitionNiveau, 'cursus');
        $statutList = self::getList($repartitionNiveau, 'statut');

        sort($cursusList);
        sort($statutList);

        $tab = [];

        foreach($repartitionNiveau as $year => $RN){
            $t = [];
            foreach ($cursusList as $c){
                $t[$c]=[];
            }
    
            foreach($RN as $R){
                if (!isset($t[$R->cursus][$R->statut])){
                    $t[$R->cursus][$R->statut]=0;
                }
                $t[$R->cursus][$R->statut] += $R->hetd;
            }
            $tab[$year]=$t;
        }


        $nameList = self::getNameList($repartitionNiveau);
        sort($nameList);        
        $repartitionNiveau = self::reformat($repartitionNiveau, $nameList);
     
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        echo $app::$viewer->render('gestionnaire/repartitionParNiveauParStatutByYear.html.twig',['title' => 'Répartition par niveau en hETD', 'cursusList' => $cursusList, 'statutList' => $statutList,'tab'=> $tab]);
    }

    public static function vacataires(){
        $app = \TDS\App::get();

        $statutList = $app::NS("Statut")::loadWhere("actif and nom LIKE 'Vac%'");
        $tab = [];
        foreach($statutList as $statut){
            $tab[] = $statut->id;
        }
        $statutListId = "(".join(', ', $tab).")";
        $vacataireList = $app::NS("Personne")::loadWhere("actif and statut in {$statutListId}");

        $vOSE = new \base\Controllers\voeuOSE(); // le tableau dans lequel on indique les voeux en terme de OSE pour faire les voeux... 
        $pbs = [];

        foreach($vacataireList as $P){
            foreach($P->voeuList as $V){
                $E = $V->enseignement;
                $actif = $P->actif && $V->actif && $E->actif;
                if (!$actif) {
                    continue;
                }
                $VDH = $V->voeu_detail_heures;
                $ecueList = $E->getStructEcueList(); // récupération des ECUE dans la base des structures
                if (is_null(reset($ecueList))){
                    $pbs []=[
                        'V' => $V,
                        'P' => $P,
                        'E' => $E,
                        'M' => "Pas d'ECUE trouvée dans la structure des enseignements",
                    ];
                    continue;
                }

                if (! $vOSE->add($P, $ecueList, $VDH)){
                    $pbs []=[
                        'V' => $V,
                        'P' => $P,
                        'E' => $E,
                        'M' => "Pas de volume pour l'ECUE - c'est un ajout...",
                    ];
                };
            }
        }


        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        $date = date("Y-m-d_H-i-s");
        $app::$cmpl['TITLE'] =  "listeVacataires_{$date}";

        echo $app::$viewer->render('gestionnaire/vacataireList.html.twig', ['vList'=> $vOSE->vList]);
    }

    public static function getDetailledVoeux2020() {
        $app= \TDS\App::get();
        
        $foire2020 = new \TDS\Database("foire2020", $app::$baseUser, $app::$basePwd, 'localhost');
        $voeuList = $foire2020->getAll("
            SELECT 
                V.num as id,
                P.num as personne_id,
                E.num as enseignement_id,
                P.nom || ' ' || P.prenom as nom,
                VDH.cours as cm,
                VDH.ctd as ctd,
                VDH.td as td,
                VDH.tp as tp,
                VDH.colle as extra,
                VDH.bonus as bonus,
                VBL.heures as total,
                P.emploi as siham,
                E.intitule,
                S.intitule as titre,
                S.ecue as ecue,
                S.etape as etape
            FROM voeu as V
            LEFT JOIN voeu_detail_htd as VDH on VDH.num = V.num
            LEFT JOIN voeu_bilan_ligne as VBL on VBL.num = V.num
            LEFT JOIN enseignant as P on P.num = V.enseignant
            LEFT JOIN enseignement as E on E.num = V.enseignement
            LEFT JOIN (
            SELECT 
                E.num as id,
                string_agg(DISTINCT ecue.nom, '|') AS intitule,
                string_agg(DISTINCT ecue.code, '|') AS ecue,
                string_agg(DISTINCT etape.code, '|') AS etape
            FROM enseignement as E
                LEFT JOIN ecue ON ecue.enseignement = E.num
                LEFT JOIN ue ON ecue.ue = ue.num
                LEFT JOIN semestre ON ue.semestre = semestre.num
                LEFT JOIN etape ON semestre.etape = etape.num
                LEFT JOIN diplome ON etape.diplome = diplome.num
                LEFT JOIN maquette ON diplome.maquette = maquette.num
                LEFT JOIN cursus ON etape.cursus = cursus.num
                LEFT JOIN departement ON maquette.departement = departement.num
            WHERE E.num >0
            GROUP BY E.num 
            ORDER BY E.num
            ) as S on S.id = E.num
            
            WHERE V.num > 0
            AND P.num > 0
            AND E.num > 0
            AND E.intitule != 'report'
            ORDER by P.num
        ");

        $ret = [];
        foreach($voeuList as $voeu){
            $ret[$voeu->id] = $voeu;
        }
        return $ret;
    }


    static function filterPersonne($voeuList, $siham){
        $rep = [];
        foreach($voeuList as $key => $voeu){
            if ($voeu->siham == $siham){
                $rep[$key] = $voeu;
            }
        }
        return $rep;
    }

    static function filterEnseignement($voeuList, $ecue){
        $rep = [];
        foreach($voeuList as $key => $voeu){
            $ecueList = explode('|', $voeu->ecue);
            foreach($ecueList as $ecueFoire) 
            if ($ecueFoire == $ecue){
                $rep[$key] = $voeu;
            }
        }
        return $rep;
    }

    public static function listingServices(){
        $app= \TDS\App::get();
        $voeuList = self::getDetailledVoeux2020();
        $emptyList = [];
        $lastPersonne = 0;
        $personneVoeu = [];
        $ligneFoundList = [];
        $ligneNotFoundList = [];
        $isInFoire = False;

        if (($handle = fopen("{$app::$pathList['plus']}/{$app::$appName}/OSE/listingServices.csv", "r")) !== FALSE) {
            $colList = $data = fgetcsv($handle, 1000, ";");

            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $ligne = new stdClass();
                foreach ($data as $key => $d) {
                    $ligne->{$colList[$key]} = $d;
                }

                if ($lastPersonne !== $ligne->{'Code intervenant'}){ // on a pas trouvé la personne dans la foire
                    $isInFoire = False;
                    $lastPersonne = $ligne->{'Code intervenant'};
                    $personneVoeu = self::filterPersonne($voeuList, $lastPersonne);
                    if (empty($personneVoeu)) {
                        $emptyList[] = [
                            'intervenant' => $ligne->{'Intervenant'},
                            'statut' => $ligne->{'Statut intervenant'},
                            'siham' => $ligne->{'Code intervenant'},
                        ];
                    } else {  // cette fois on a trouvé la personne dans la foire
                        $isInFoire = True;
                    }
                }

                if ($isInFoire){ 
                    $voeux = self::filterEnseignement($personneVoeu, $ligne->{'Code enseignement'});
                    if (empty($voeux)){ // on a pas trouvé la ligne dans les voeux de la foire
                        $ligneNotFoundList[] = $ligne;      
                    } else {
                        foreach($voeux as $key => $voeu){
                            $ligneFoundList[] = $ligne;
                            unset($voeuList[$key]);
                        }
                    }
                }
            }
            fclose($handle);
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('gestionnaire/listingServices.html.twig', [
            'emptyList' => $emptyList, 
            'ligneFoundList' => $ligneFoundList, 
            'ligneNotFoundList' => $ligneNotFoundList,
            'voeuList' => $voeuList,
        ]);
        
        exit();

    }

}
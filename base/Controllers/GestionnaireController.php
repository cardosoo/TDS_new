<?php

namespace base\Controllers;

use stdClass;
use TDS\Historique;

class voeuOSE {
    /*
CM  	Cours magistraux 	1,50	1,50 	Permanente 		Visible - Visible en saisie extérieure
TD  	Travaux dirigés 	1,00	1,00 	Permanente 		Visible - Visible en saisie extérieure
TP  	Travaux pratiques 	1,00	1,00 	Permanente 		Visible - Visible en saisie extérieure
PROJET  	Projet 	1,00	1,00 	Permanente 		Visible - Visible en saisie extérieure
TD2  	TD2 	1,00	1,00 	Permanente 		Visible - Visible en saisie extérieure
MD  	Moodle 	1,00	1,00 	Permanente 		Visible - Visible en saisie extérieure
CMTD7  	CMTD7 	1,16	1,16 	Permanente 		Visible - Visible en saisie extérieure
FORFAI  	Forfait 	1,00	1,00 	Permanente 		Visible - Visible en saisie extérieure
CMTD  	CMTD 	1,25	1,25 	Permanente 		Visible - Visible en saisie extérieure
TP7  	TP7 	1,00	1,00 	Permanente 		Visible - Visible en saisie extérieure
CMTP  	CMTP 	1,16	1,16 	Permanente 		Visible - Visible en saisie extérieure
*/

    private static $conv = [
        'cm'    => ['CM', 'CMTD', 'CMTD7', 'TD', 'TD2', 'TP', 'TP7', 'CMTP', 'FORFAI', 'PROJET', 'MD'],
        'ctd'   => ['CMTD', 'CMTD7', 'CM', 'TD', 'TD2', 'TP', 'TP7', 'CMTP', 'FORFAI', 'PROJET', 'MD'],
        'td'    => ['TD', 'TD2', 'CMTD', 'CMTD7', 'CM', 'TP', 'TP7', 'CMTP', 'FORFAI', 'PROJET', 'MD'],
        'tp'    => ['TP', 'TP7', 'CMTP', 'TD', 'TD2', 'CMTD', 'CMTD7', 'CM', 'FORFAI', 'PROJET', 'MD'],
        'extra' => ['FORFAI', 'PROJET', 'MD', 'TD', 'TD2', 'CM', 'CMTD', 'CMTD7', 'TP', 'TP7', 'CMTP'],
        'bonus' => ['FORFAI', 'PROJET', 'MD', 'TD', 'TD2', 'CM', 'CMTD', 'CMTD7', 'TP', 'TP7', 'CMTP'],
    ];


    public $vList = [];
    private $ecueList;
    private $VDH;
    private $P;
    private $lastModification;
    private $lastOrigin;

    private function addType($type) {
        $app = \TDS\App::get();
        if ($this->VDH->$type == 0) return true;

        $candidat = null;
        $ordre = 100;
        $typeOSE1 = null;
        $typeOSE2 = null;

        foreach ($this->ecueList as $ecue) {
            if (is_null($ecue)) {
                return true;
            }

            $besoins = $ecue->getBesoinsArray();
            $pos = 0;

            foreach (self::$conv[$type] as $typeOSE) {
                if (isset($besoins[$typeOSE])) {
                    $typeOSE1 = $typeOSE;
                    break;
                }
                $pos += 1;
            }
            if ($pos < $ordre) {
                $ordre = $pos;
                $candidat = $ecue;
                if ($typeOSE1 == "") {
                    $typeOSE2 = "???";
                    $coeff = 1;
                } else {
                    $typeOSE2 = $typeOSE1;
                    $coeff = $besoins[$typeOSE1]['coeffTypeHeure'];
                }
            }
        }
        if ($typeOSE2 == "???") {
            return false;
        }
        // normalement là on sort avec le $candidat et son ordre;
        // ...
        $code = $candidat->getCode();
        $Pid = $this->P->id;
        $heq = $this->VDH->$type * $app::$hETD[$type] / $coeff;

        //var_dump($ordre, $code, $typeOSE2);
        if (isset($this->vList[$Pid][$code])) { // Il existe déjà quelque chose avec le même code et le même type
            if (isset($this->vList[$Pid][$code]['charge'][$typeOSE2])) {
                $this->vList[$Pid][$code]['charge'][$typeOSE2] += $heq;
            } else { // Il existe déjà quelque chose avec le même code (mais pas le même type)
                $this->vList[$Pid][$code]['charge'][$typeOSE2] = $heq;
            }
        } else { // il faut créer de toute pièce
            $this->vList[$Pid][$code]['charge'][$typeOSE2] = $heq;
        }
        $this->vList[$Pid][$code]['voeuList'][$this->VDH->id] = [$this->lastModification, $this->lastOrigin];
        return true;
    }

    public function add($P, $ecueList, $VDH) {
        $this->ecueList = $ecueList;
        $this->VDH = $VDH;
        $this->P = $P;
        $history = \TDS\Historique::entity('Voeu', $VDH->id);
        $date = "???";
        $origin = "???";
        if (count($history) > 0) {
            $date = $history[0]->historique->date;
            $origin = $history[0]->historique->ip;
        }
        $this->lastModification = $date;
        $this->lastOrigin = $origin;

        //var_dump($ecueList, $VDH); 
        $ok = true;

        $ok &= $this->addType('cm');
        $ok &= $this->addType('ctd');
        $ok &= $this->addType('td');
        $ok &= $this->addType('tp');
        $ok &= $this->addType('extra');
        $ok &= $this->addType('bonus');
        return $ok;
    }
}


class GestionnaireController extends \TDS\Controller {

    public static function home() {
        $app = \TDS\App::get();

        echo $app::$viewer->render('gestionnaire/index.html.twig');
    }

    public static function utilisationServices() {
        $app = \TDS\App::get();


        // 
        $services = $app::$db->getAll('
            SELECT 
                MAX(S.nom) statut,
                COUNT(*) as Nb,
                SUM(S.obligation) as volume,
                SUM(
                    CASE 
                        WHEN S.obligation = 183 THEN 192
                        ELSE S.obligation
                    END
                ) as volumeLegal,
                SUM(SI.reduction) as reduction,
                SUM(S.obligation - SI.reduction) as volumeReduit,
                sum(VPH.heures) as realise
            FROM personne as P
            LEFT JOIN statut as S on S.id = P.statut
            LEFT JOIN situation as SI on SI.id = P.situation
            LEFT JOIN voeu_personne_heures as VPH on VPH.id = P.id
            WHERE P.id >0
            AND P.actif
            GROUP BY P.statut
            ORDER BY statut
        ');

        $besoins = $app::$db->getAll('
            SELECT
                min(TUE.nom) as typeue,
                ES.composante,
                ES.cursus,
                sum(EB.besoins) as besions
            FROM enseignement as E
            LEFT JOIN enseignement_besoins_detail as EBD on EBD.id = E.id
            LEFT JOIN enseignement_besoins as EB on EB.id = E.id
            LEFT JOIN enseignement_structure as ES on ES.id = E.id
            LEFT JOIN typeue as TUE on E.typeue = TUE.id
            WHERE E.id >0
            AND E.actif
            GROUP BY ES.cursus, ES.composante, tue.id
            ORDER BY TUE.id, ES.composante, ES.cursus
        ');



        /*********************
         * - Il faut ajouter les besoins en les enseignements
         * - Il faut faire distinguer les PCC
         * - Il faut aussi distingure les PCA
         * - on pourrait distinguer via les Bonus
         * - Il faudrait aussi faire les services attribués
         * *******************/

        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withDataTables"] = true;


        echo $app::$viewer->render('gestionnaire/utilisationServices.html.twig', [
            'services' => $services,
            'besoins' => $besoins,
        ]);
    }

    private static function filterPersonne($voeuList, $ose) {
        $rep = [];
        foreach ($voeuList as $key => $voeu) {
            if ($voeu->personne->ose == $ose) {
                $rep[$key] = $voeu;
            }
        }
        return $rep;
    }

    private static function filterEnseignement($voeuList, $ecue) {
        $rep = [];
        foreach ($voeuList as $key => $voeu) {
            $ecueList = explode('|', $voeu->ecue);
            foreach ($ecueList as $ecueFoire)
                if ($ecueFoire == $ecue) {
                    $rep[$key] = $voeu;
                }
        }
        return $rep;
    }


    /*************************************
     * comparaison entre le fichier du listing des services issus de OSE 
     * et des voeux issues de la foire
     * 
     * - à partir de OSE, on récupère les différentes personnes impliquées
     * - à partir de OSE, on récupère les différents enseignement impliqués
     * 
     */
    public static function comparaisonOSE() {

        var_dump('Je préfère ne rien proposer pour le moment');
        exit();


        $app = \TDS\App::get();
        $voeuList = $app::NS('Voeu')::loadWhere('actif');

        $personneNS = $app::NS('Personne');
        $ecueNS = $app::NS('ECUE');

        $personneOSE = [];
        $personneHorsBase = [];

        $ecueOSE = [];
        $ecueHorsBase = [];

        $voeuHorsBase = [];

        $app::$db->h_query('
            DELETE FROM voeu WHERE id>0;
        ');

        if (($handle = fopen("{$app::$pathList['plus']}/{$app::$appName}/OSE/listingServices.csv", "r")) !== FALSE) {
            $colList = fgetcsv($handle, 0, ";");
            $count = 0;
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                $count++;
                $ligne = new \stdClass();
                foreach ($data as $key => $d) { // pour mettre les éléments dans les bonnes lignes
                    $ligne->{$colList[$key]} = $d;
                }
                // on vérifie si la personne en question existe dans la base de données
                $ose = $ligne->{'Code intervenant'};
                if (isset($personneOSE[$ose]) || isset($personneHorsBase[$ose])) {
                    $personne = $personneOSE[$ose] ?? $personneHorsBase[$ose];
                } else {
                    $personne = $personneNS::loadOneWhere("ose = '{$ose}'");
                    if (empty($personne)) { // alors la personne n'est pas la base de données
                        $personneHorsBase[$ose] = $ligne->{'Intervenant'};
                    } else {
                        $personneOSE[$ose] = $personne;
                    }
                }


                // on vérifie si la personne en question existe dans la base de données
                $code = $ligne->{'Code enseignement'};
                if (isset($ecueOSE[$code]) || isset($ecueHorsBase[$code])) {
                    $ecue = $ecueOSE[$code] ?? $ecueHorsBase[$code];
                } else {
                    $ecue = $ecueNS::loadOneWhere("code = '{$code}'");
                    if (empty($ecue)) { // alors la personne n'est pas la base de données
                        $ecueHorsBase[$code] = $ligne->{'Enseignement ou fonction référentielle'};
                    } else {
                        $ecueOSE[$code] = $ecue;
                    }
                }

                if (is_a($personne, $personneNS) and is_a($ecue, $ecueNS)) {
                    if (! $ecue->enseignement) {
                        var_dump("{$count} - «{$ecue->code}» - «{$ecue->nom}» - «{$ligne->{'Code enseignement'}}» - «{$ligne->{'Enseignement ou fonction référentielle'}}» ");
                    } else {
                        $voeuNS = $app::NS('Voeu');
                        $voeu = new $voeuNS;
                        $voeu->personne = $personne->id;
                        $voeu->enseignement = $ecue->enseignement->id;
                        $voeu->cm = $ligne->{'CM'};
                        $voeu->td = $ligne->{'TD'};
                        $voeu->tp = floatval($ligne->{'TP'}) + floatval($ligne->{'TP7'});
                        $voeu->td = $ligne->{'TD'};
                        $voeu->bonus = $ligne->{'Référentiel'};
                        $voeu->save();
                    }
                } else {
                    $voeuHorsBase[] = $data;
                }
            }
            fclose($handle);
        }


        var_dump([
            // "ecueOSE" => $ecueOSE,
            "ecueHorsBase" => $ecueHorsBase,
            // "personneOSE" => $personneOSE,
            "personneHorsBase" => $personneHorsBase,
            //"voeuHorsBase" => $voeuHorsBase,
        ]);

        //        var_dump($ecueOSE);
        var_dump($personneHorsBase);
        //        var_dump($personneOSE);
        //        var_dump($voeuHorsBase);


        exit();
        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withDataTables"] = true;

        echo $app::$viewer->render('gestionnaire/comparaisonOSE.html.twig', [
            'emptyList' => $emptyList,
            'ligneFoundList' => $ligneFoundList,
            'ligneNotFoundList' => $ligneNotFoundList,
            'voeuList' => $voeuList,
        ]);

        exit();
    }


    public static function compareEcueFromFoireToOse() {
        $app = \TDS\App::get();
        $pl = $app::$pathList['plus'];
        $match = [];
        $unmatch = [];

        $enseignementList = $app::NS('Enseignement')::loadWhere('actif AND typeUE=1');
        foreach ($enseignementList as $enseignement) {
            $tmp = [];
            $codeList = explode('|', $enseignement->enseignement_structure->code);
            $find = false;
            foreach ($codeList as $code) {
                $co = explode('_', $code);
                $cmd = "grep {$co[1]} {$pl}/codeOSE.csv";
                $rep = `{$cmd}`;

                $csv = is_null($rep) ? [] : str_getcsv($rep, ";",  "\"");
                if (count($csv) > 10) {
                    $tmp[$co[1]] = [
                        'composante' => $csv[0],
                        'etape' => $csv[1],
                        'cursus' => $csv[2],
                        'ecue' => $csv[4],
                        'intitule' => $csv[5],
                        'semestre' => $csv[8],
                        'effectif' => intval($csv[13]) + intval($csv[14]) + intval($csv[15]),
                        'CM' => $csv[16],
                        'gCM' => $csv[17],
                        'TD' => $csv[18],
                        'gTD' => $csv[19],
                        'TP' => $csv[20],
                        'gTP' => $csv[21],
                    ];
                    $find = true;
                } else {
                    $tmp[$co[1]] = [];
                }
            }

            if ($find) {
                $match[] = ['E' => $enseignement, 'OSE' => $tmp];
            } else {
                $unmatch[] = ['E' => $enseignement];
            }
        }

        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withDataTables"] = true;

        echo $app::$viewer->render('gestionnaire/compareEcueFromFoireToOse.html.twig', [
            'match' => $match,
            'unmatch' => $unmatch,
        ]);
    }


    public static function listeSituations() {
        $app = \TDS\App::get();
        $PSNS = $app::NS('personne_situation');
        $psList = $PSNS::loadWhere('actif', ['situation']);


        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withDataTables"] = true;

        echo $app::$viewer->render('gestionnaire/listeSituations.html.twig', ['psList' => $psList]);
    }

    public static function listeReferentiel() {
        $app = \TDS\App::get();
        $PRNS = $app::NS('personne_foncRef');
        $prList = $PRNS::loadWhere('actif', ['foncref']);


        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withDataTables"] = true;

        echo $app::$viewer->render('gestionnaire/listeReferentiel.html.twig', ['prList' => $prList]);
    }

    public static function listeUtilisateursLDAP() {
        $app = \TDS\App::get();

        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withDataTables"] = true;

        $PNS = $app::NS('Personne');
        $pList = $PNS::loadWhere('actif', ['nom'],);

        $uidL = [];
        $oseL = [];


        $pL = array_chunk($pList, 50, true);

        $ldap = new \TDS\LDAPExtern();

        $repUid = [];
        $repOse = [];
        $pList = [];


        foreach ($pL as $list) {
            $uidList = [];
            $oseList = [];
            $pList = array_merge($pList, $list);
            foreach ($list as $P) {
                $uidList[$P->id] = $P->uid;
                $uidL[$P->uid] = $P->id;
                $oseList[$P->id] = $P->ose;
                $oseL[$P->ose] = $P->id;
            }
            $filterUid = "(|(uid=" . join(")(uid=", $uidList) . "))";
            $filterOse = "(|(supannempid=" . join(")(supannempid=", $oseList) . "))";

            $repUid = array_merge($repUid, $ldap->reformat($ldap->list($filterUid, ['uid', 'displayName', 'mail',  'supannAliasLogin', 'supannEmpId'], 1000)));
            $repOse = array_merge($repOse, $ldap->reformat($ldap->list($filterOse, ['uid', 'displayName', 'mail',  'supannAliasLogin', 'supannEmpId'], 1000)));
        }

        $cP = [];
        foreach ($pList as $k => $P) {
            $t = new stdClass();
            $t->P = $P;
            $t->fromUid = isset($repUid[$P->uid]) ? $repUid[$P->uid] : null;
            $t->fromOse = isset($repOse[$P->uid]) ? $repOse[$P->uid] : null;
            $cP[] = $t;
        }


        echo $app::$viewer->render('gestionnaire/listeUtilisateursLDAP.html.twig', ['pList' => $cP, 'rUid' => $repUid, 'rOse' => $repOse, 'uidL' => $uidL, 'oseL' => $oseL]);
    }


    public static function listeUtilisateursActifs() {
        $app = \TDS\App::get();

        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withDataTables"] = true;

        $PNS = $app::NS('Personne');
        $pList = $PNS::loadWhere('actif', ['nom'],);

        echo $app::$viewer->render('gestionnaire/listeUtilisateursActifs.html.twig', ['pList' => $pList]);
    }

    /*
    'SERVICE'                             => [
        'ID',
        'INTERVENANT_ID',
        'ELEMENT_PEDAGOGIQUE_ID',
        'ETABLISSEMENT_ID',
        'HISTO_CREATION',
        'HISTO_CREATEUR_ID',
        'HISTO_MODIFICATION',
        'HISTO_MODIFICATEUR_ID',
        'HISTO_DESTRUCTION',
        'HISTO_DESTRUCTEUR_ID',
        'SOURCE_ID',
        'SOURCE_CODE',
        'DESCRIPTION',
        'ETAPE_ID',
    ],


    'VOLUME_HORAIRE'                      => [
        'ID',
        'TYPE_VOLUME_HORAIRE_ID',
        'SERVICE_ID',
        'PERIODE_ID',
        'TYPE_INTERVENTION_ID',
        'HEURES',
        'MOTIF_NON_PAIEMENT_ID',
        'CONTRAT_ID',
        'HISTO_CREATION',
        'HISTO_CREATEUR_ID',
        'HISTO_MODIFICATION',
        'HISTO_MODIFICATEUR_ID',
        'HISTO_DESTRUCTION',
        'HISTO_DESTRUCTEUR_ID',
        'SOURCE_ID',
        'SOURCE_CODE',
        'AUTO_VALIDATION',
        'HORAIRE_DEBUT',
        'HORAIRE_FIN',
        'TAG_ID',
    ],
*/

    public static function exportOSEUserList() {
        $app = \TDS\App::get();

        $personneList = $app::NS('Personne')::loadWhere('actif');
        $statutList = $app::NS('Statut')::loadWhere('actif');

        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withMarkdown"] = true;

        echo $app::$viewer->render('gestionnaire/exportOSEUserList.html.twig', ['personneList' => $personneList, 'statutList' => $statutList]);
    }


    public static function convertVoeu(string $type,) {
    }

    public static function doExportOSE_SERVICE() {
        /* 
        Cette nouvelle version permet :
        - de faire en sorte qu'il n'y ait pas de doublons ()
            pour cela, pour une personne donnée, on fait un tableau avec les lignes à ajouter 
            et une fois que les enseignements de la personne sont tous passés en revue, on fait
            la sortie. 
        - De convertir à la volée les types d'enseignements de la foire en type d'enseignemnet de OSE.
            pour cela on utilise une table de conversion qui permet d'indiquer préférentiellement dans
            quelle type d'heure de OSE on range les heures de la foire
        - De gérer les différents code OSE associés à un enseignement
            Pour cela on regarde les ecue listées dans l'odre en gardant celle qui avoir l'indice
            de correspondance le plus petit.
        */

        $app = \TDS\App::get();

        $data = filter_input(INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $idList = join(", ", $data);
        $voeuList = $app::NS("Voeu")::loadWhere("personne in ({$idList})");

        $vOSE = new voeuOSE(); // le tableau dans lequel on indique les voeux en terme de OSE pour faire les voeux... 

        foreach ($voeuList as $V) {
            $P = $V->personne;
            $E = $V->enseignement;
            $actif = $P->actif && $V->actif && $E->actif;
            if (!$actif) {
                continue;
            }
            $VDH = $V->voeu_detail_heures;
            $ecueList = $E->getStructEcueList(); // récupération des ECUE dans la base des structures

            //            var_dump($E->code, $ecueList);
            if (is_null(reset($ecueList))) {
                var_dump("Ne trouve pas d'ECUE avec le code  : {$actif} {$E->code}, {$E->nom}");
                continue;
            }

            $vOSE->add($P, $ecueList, $VDH);
        }
        var_dump($vOSE->vList);

        //echo $app::$viewer->render("gestionnaire/exportOSE_SERVICE.csv.twig", ['personneList' => $pList]);
    }

    public static function exportOSE_SERVICE_old() {
        $app = \TDS\App::get();

        $data = filter_input(INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $idList = join(", ", $data);
        $personneList = $app::NS("Personne")::loadWhere("id in ({$idList})");

        $pList = [];
        foreach ($personneList as $P) {
            $V = $P->voeuList;
            if (count($V) > 0) {
                $pList[] = $P;
            }
        }

        echo $app::$viewer->render("gestionnaire/exportOSE_SERVICE.csv.twig", ['personneList' => $pList]);
    }
    public static function exportOSE_VOLUME_HORAIRE() {
        $app = \TDS\App::get();

        $data = filter_input(INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $idList = join(", ", $data);
        $personneList = $app::NS("Personne")::loadWhere("id in ({$idList})");

        $pList = [];
        foreach ($personneList as $P) {
            $V = $P->voeuList;
            if (count($V) > 0) {
                $pList[] = $P;
            }
        }

        echo $app::$viewer->render("gestionnaire/exportOSE_VOLUME_HORAIRE.csv.twig", ['personneList' => $pList]);
    }


    public static function exportOSE() {
        $app = \TDS\App::get();
        $data = filter_input(INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $idList = join(", ", $data);
        $voeuList = $app::NS("Voeu")::loadWhere("personne in ({$idList})");
        $pbs = [];

        $vOSE = new voeuOSE(); // le tableau dans lequel on indique les voeux en terme de OSE pour faire les voeux... 

        foreach ($voeuList as $V) {
            $P = $V->personne;
            $E = $V->enseignement;
            $actif = $P->actif && $V->actif && $E->actif;
            if (!$actif) {
                continue;
            }
            /*
            if (in_array($P->ose, ["UDP000165845", "UDP000169427", "UDP000165846"] )){
                $pbs []=[
                    'V' => $V,
                    'P' => $P,
                    'E' => $E,
                    'M' => "Code UDP pas encore dans OSE (dev)",
                ];
                continue;
            }
*/
            $VDH = $V->voeu_detail_heures;
            $ecueList = $E->getStructEcueList(); // récupération des ECUE dans la base des structures

            if (is_null(reset($ecueList))) {
                $pbs[] = [
                    'V' => $V,
                    'P' => $P,
                    'E' => $E,
                    'M' => "Pas d'ECUE trouvée dans la structure des enseignements",
                ];
                continue;
            }

            if (! $vOSE->add($P, $ecueList, $VDH)) {
                $pbs[] = [
                    'V' => $V,
                    'P' => $P,
                    'E' => $E,
                    'M' => "Pas de volume pour l'ECUE - c'est un ajout...",
                ];
            };
        }
        // var_dump($vOSE->vList);

        $data = [
            'SERVICE' => $app::$viewer->render("gestionnaire/exportOSE_SERVICE.csv.twig", ['vList' => $vOSE->vList]),
            'VOLUME_HORAIRE' => $app::$viewer->render("gestionnaire/exportOSE_VOLUME_HORAIRE.csv.twig", ['vList' => $vOSE->vList]),
            'PBS' => $app::$viewer->render("gestionnaire/exportOSE_PBS.csv.twig", ['PBS' => $pbs]),
        ];

        // echo($data['VOLUME_HORAIRE']);

        echo json_encode($data);
    }
}

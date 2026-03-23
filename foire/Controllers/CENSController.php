<?php
namespace foire\Controllers;

use TDS\Query;

class CENSController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        // var_dump("pour l'instant il faut se contenter de rien...");

        echo $app::$viewer->render('CENS/index.html.twig');
    }

    public static function repFiliereAnnee($cursusID){
        $app = \TDS\App::get();

        $composanteList = $app::NS('Composante')::loadWhere('actif');
        echo $app::$viewer->render('CENS/repFiliereAnnee.html.twig', ['composanteList' => $composanteList, 'cursusID' => $cursusID]);

    }

    public static function repFiliereAnneeMenu(){
        $app = \TDS\App::get();

        $cursusList = $app::NS('Cursus')::loadWhere('actif');

        echo $app::$viewer->render('CENS/repFiliereAnneeMenu.html.twig', ['cursusList' => $cursusList]);
    }

    public static function coutFiliereAnneeBefore2019( $year){
        $app = \TDS\App::get();

        $params = [];

        $sql = "
        SELECT
            *
        FROM ancien.bilan_departement
        ";

        $rep = $app::$db->fetchAll($sql);

        var_dump($rep);
        exit();    


        $composanteList = $app::NS('Composante')::loadWhere('actif');
        $grandTotal = 0;
        foreach($composanteList as $composante){
            $p = new \stdClass();
            $p->composante = $composante;
            $p->list = [];
            $totalComposante = 0;
            foreach($composante->maquetteList as $maquette){
                foreach($maquette->diplomeList as $diplome){
                    foreach($diplome->etapeList as $etape){
                        $l = new \stdClass();
                        $l->maquette = $maquette->nom;
                        $l->diplome = $diplome->nom;
                        $l->etape = $etape;
                        $l->cout = $etape->getCout();
                        $p->list[]= $l;
                        $totalComposante += $l->cout;
                    }
                }

            }
            $p->coutComposante = $totalComposante;
            $grandTotal += $totalComposante;
            $params[] = $p;
        }


//        $etapeList = $app::NS('Etape')::loadWhere('actif');
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render("CENS/coutFiliereAnnee.{$format}.twig", ['params' => $params, 'total' => $grandTotal]);
    }

    public static function coutFiliereAnneeAfter2025($format){
        $app = \TDS\App::get();
        $year = $app::$currentYear;



        $struct = new \base\Struct($year);
        //$typeList = $struct->getUsefulTypeList([]);        
        //$niveauList = $struct->getUsefulNiveauList([]);
        $structureList = \base\Controllers\RechercheController::convertForJS($struct->getUsefulStructureList(['inDB' => true]));        
        //$periodeList = $struct->getUsefulPeriodeList([]);
        
        $filter['ordre'] = ['ET.nom'];
        $etapeList =  $struct->getUsefulFilter('ET.code', $filter, 'ET.id');
  
        $f['cursusList'] =  \Base\Struct::getCursusList();
        $f['semestreList'] = \Base\Struct::getSemestreList();
        $f['modaliteList'] = \Base\Struct::getModaliteList();
        
        $f['structureList'] = $structureList;
        $f['maquetteList'] = $etapeList; 

        $table = [];        
        foreach($etapeList as $codeEtape){
            $etape = $struct->getEtapeByCode($codeEtape);

            $ecueList = $etape->getEcueList();
            $inBase = $struct->getInBaseEnseignementFromEcueList($ecueList);

            list($in, $out) = $struct->getInOutFromEcueList($ecueList, $inBase);
            $struct->addVacation($out);
            $struct->addVacation($in);

            $cout = 0;
            $coutVac = 0;
            $coutVacIn = 0;
            foreach($in as $elm){
                $cout += $elm['besoins'];
                $coutVac += $elm['vacOut'];
                $coutVacIn += $elm['vacIn'];
            }
            foreach($out as $elm){
                $coutVac += $elm['vacOut'];
                $coutVacIn += $elm['vacIn'];
            }

            $ligne = (object)[
                'composante' => $etape->getStructure()->getNom(),
                "code_etape" => $codeEtape,
                "nom_etape" => $etape->getNom(),
                "nom_cursus" => $etape->getCursusName(),
                "cout" => $cout,
                "cout_vac" => $coutVac,
                "cout_vac_in" => $coutVacIn,
            ];
            $table[]=$ligne;
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        echo $app::$viewer->render("CENS/coutFiliereAnnee2025.html.twig", ['LList' => $table]);
    }

    public static function coutTotal(){
        $app = \TDS\App::get();

        $besoins = $app::$db->getOne("
            SELECT
                sum(EB.besoins) as \"sommeBesoins\"
            FROM enseignement as E
            LEFT JOIN enseignement_besoins as EB on EB.id = E.id
            WHERE E.actif

        ");

        $potentiel = self::calculPotentielEnseignantAfter2021();

        $total = 0;
        $totalReport = 0;
        $totalSituation = 0;
        $totalReferentiel = 0;

        foreach($potentiel as $P){
            $total += $P->total;
            $totalReport += $P->totalReport;
            $totalSituation += $P->totalSituation;
            $totalReferentiel += $P->totalReferentiel;
        }

        $params =[
            'besoins' => $besoins->sommeBesoins,
            'totals' => [
                "potentielTheorique" => $total,
                "potentielReel" => $total - $totalSituation - $totalReferentiel,
                "totalReport" => $totalReport,
                "totalSituation" => $totalSituation,
                "totalReferentiel" => $totalReferentiel,
            ],
        ];
        echo $app::$viewer->render("CENS/coutTotal.html.twig", ['Params' => $params]);

    }

    public static function coutFiliereAnnee( $format = "html"){
        $app = \TDS\App::get();

        if ($app::$currentYear>=2025){
            self::coutFiliereAnneeAfter2025($format);
            return;
        }


        $params = [];
        $composanteList = $app::NS('Composante')::loadWhere('actif');
        $grandTotal = 0;
        foreach($composanteList as $composante){
            $p = new \stdClass();
            $p->composante = $composante;
            $p->list = [];
            $totalComposante = 0;
            foreach($composante->maquetteList as $maquette){
                foreach($maquette->diplomeList as $diplome){
                    foreach($diplome->etapeList as $etape){
                        $l = new \stdClass();
                        $l->maquette = $maquette->nom;
                        $l->diplome = $diplome->nom;
                        $l->etape = $etape;
                        $l->cout = $etape->getCout();
                        $p->list[]= $l;
                        $totalComposante += $l->cout;
                    }
                }
            }
            $p->coutComposante = $totalComposante;
            $grandTotal += $totalComposante;
            $params[] = $p;
        }


//        $etapeList = $app::NS('Etape')::loadWhere('actif');
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render("CENS/coutFiliereAnnee.{$format}.twig", ['params' => $params, 'total' => $grandTotal]);
    }

    public static function APIcoutFiliereAnnee($year){
        $app = \TDS\App::get();
        $app::setCurrentYear($year);
        $app::openDatabase();
        if ($year>=2019){
            self::coutFiliereAnnee('csv');
            return;
        }
        self::coutFiliereAnneeBefore2019('csv');
    }

    public static function hEtuFiliereAnnee( $format = "html"){
        $app = \TDS\App::get();

        $params = [];
        $composanteList = $app::NS('Composante')::loadWhere('actif');
        foreach($composanteList as $composante){
            $p = new \stdClass();
            $p->composante = $composante;
            $p->list = [];
            foreach($composante->maquetteList as $maquette){
                foreach($maquette->diplomeList as $diplome){
                    foreach($diplome->etapeList as $etape){
                        $l = new \stdClass();
                        $l->maquette = $maquette->nom;
                        $l->diplome = $diplome->nom;
                        $l->etape = $etape;
                        $l->hEtu = $etape->getHetu();
                        $p->list[]= $l;
                    }
                }
            }
            $params[] = $p;
        }

//        $etapeList = $app::NS('Etape')::loadWhere('actif');
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render("CENS/hEtuFiliereAnnee.{$format}.twig", ['params' => $params]);
    }

    public static function APIhEtuFiliereAnnee($year){
        $app = \TDS\App::get();
        $app::setCurrentYear($year);
        $app::openDatabase();
        self::hEtuFiliereAnnee('csv');
        return;
    }



    // Je pense que cette version n'est valable 
    // que pour le pour les PCC et PCA 
    // comptées comme enseignement (avant 2021 ?)
    static function potentielEnseignantBefore2021(){
        $app = \TDS\App::get();

        $params = [];
        $statutList = $app::NS('Statut')::loadWhere('actif');
        $grandTotal = 0;
        foreach($statutList as $S){
            $p = new \stdClass();
            $p->statut = $S;
            $p->situation = [];
            $p->PCC = [];
            $p->PCA = [];

            $p->total = 0;
            $p->count = 0;
            $p->totalReport = 0;
            $p->totalSituation = 0;
            $p->totalPCC = 0;
            $p->totalPCA = 0;

            foreach($S->personneList as $P){
                if ($P->actif ){
                    $p->count +=1 ;
                    $p->total += $S->obligation;
                    if ($P->__situation>0){
                        if (!isset($p->situation[$P->situation->nom])){
                            $p->situation[$P->situation->nom]=0;
                        }
                        $p->situation[$P->situation->nom] += $P->situation->reduction;
                        $p->totalSituation += $P->situation->reduction;
                    }
                    foreach($P->voeuList as $V){
                        if ($V->__enseignement == 942) {
                            $p->totalReport += $V->voeu_bilan_ligne->heures;
                        }
                        if (is_null($V->enseignement->typeue)){
                            // var_dump($V);
                            // var_dump($V->enseignement);
                        } else {
                            if ($V->enseignement->typeue->nom =='PCC'){
                                $p->totalPCC += $V->voeu_bilan_ligne->heures;
                            }
                            if ($V->enseignement->typeue->nom =='PCA'){
                                $p->totalPCA += $V->voeu_bilan_ligne->heures;
                            }
                        }
                    }

                }
            }
            $params[] = $p;
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('CENS/potentielEnseignantBefore2021.html.twig', ['PList' => $params]);
    }

    static function calculPotentielEnseignantAfter2021(){
       $app = \TDS\App::get();

        $params = [];
        $statutList = $app::NS('Statut')::loadWhere('actif');
        $grandTotal = 0;
        foreach($statutList as $S){
            $p = new \stdClass();
            $p->statut = $S;
            $p->situation = [];
            $p->referentiel = [];

            $p->total = 0;
            $p->count = 0;
            $p->totalReport = 0;
            $p->totalSituation = 0;
            $p->totalReferentiel = 0;

            foreach($S->personneList as $P){
                if ($P->actif ){
                    $p->count +=1 ;
                    $p->total += $S->obligation;
                    foreach($P->personne_situationList as $PS){
                        $label = $PS->situation->nom;
                        if (!isset($p->situation[$label])){
                            $p->situation[$label]=0;
                        }
                        $p->situation[$label] += $PS->reduction;
                        // $p->totalSituation += $PS->situation->reduction;
                        $p->totalSituation += $PS->reduction;
                        if ( substr( $label, 0, 6 ) === "Report" ){
                            $p->totalReport += $PS->reduction;
                        }
                    }
                    foreach($P->personne_foncrefList as $PF){
                        if ($PF->foncref->id == 4) { // c'est un stage 
                            $label = "Stages";
                        } else {
                            $label = "{$PF->foncref->intitule} | {$PF->commentaire}";
                        }
                        if (!isset($p->referentiel[$label])){
                            $p->referentiel[$label]=0;
                        }
                        $p->referentiel[$label] += $PF->volume;
                        $p->totalReferentiel += $PF->volume;
                    }
                }
            }
            $params[] = $p;
        }
        return $params;
    }

    static function potentielEnseignantAfter2021(){
        $app = \TDS\App::get();

        $params = self::calculPotentielEnseignantAfter2021();
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('CENS/potentielEnseignantAfter2021.html.twig', ['PList' => $params]);
    }

    static function potentielEnseignant(){
        $app = \TDS\App::get();
        if ($app::$currentYear <2021){
            self::potentielEnseignantBefore2021();
        } else {
            self::potentielEnseignantAfter2021();
        }
    }



    public static function listeStages(){
        $app = \TDS\App::get();

        $SL = $app::NS('personne_foncRef')::loadWhere("actif and foncref = 4", ['personne']);

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        echo $app::$viewer->render('CENS/listeStages.html.twig', ['SL' => $SL]);

    }

    //public static function 


}

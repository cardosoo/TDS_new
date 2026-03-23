<?php

namespace base\Controllers;

use base\Struct;
use \base\VacationDocument as VacationDocument;
use \base\Vacation as Vacation;

use StructureQuery;
use Etape;
use EtapeQuery;
use ECUE;
use ECUEQuery;
use ecue_etape;
use ecue_etapeQuery;



function val($st){
    return floatval(str_replace(',', '.', $st));
}

class VacationController extends \TDS\Controller {

    public static function convertForJS($list){
        $n = [];
        foreach($list as $key => $val){
            $n[] = (object)[ 'id' => $key, 'nom' => $val];
        }
        return $n;
    }

    public static function fiche($id){

        $app = \TDS\App::get();


        $vacation = new Vacation($id);
//var_dump($_POST);
        $isPOST = !is_null(filter_input(INPUT_POST, 'codeECUE', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        if (!$isPOST){
            $update = false;
        } else {
            $update = true;
            $vacation->data->codeECUE = self::input('codeECUE');
            $vacation->data->nom = self::input('nom');
            $vacation->data->prenom = self::input('prenom');
            $vacation->data->niveau = self::input('niveau');
            $vacation->data->hCM = val(self::input('hCM'));
            $vacation->data->hCMTD = val(self::input('hCMTD'));
            $vacation->data->hTD = val(self::input('hTD'));
            $vacation->data->hTP = val(self::input('hTP'));
            $vacation->data->typeFormation = self::input('typeFormation');
            $vacation->data->formation = self::input('formation');
            $vacation->data->semestre = self::input('semestre');
            $vacation->data->dateDebut = self::input('dateDebut');
            $vacation->data->dateFin = self::input('dateFin');
            $vacation->data->intituleEnseignement = self::input('intituleEnseignement');
            $vacation->data->EOTP = self::input('EOTP');
            $vacation->data->gestionnaireScol = self::input('gestionnaireScol');
            $vacation->data->uid = self::input('uid');
            $vacation->data->signature =  self::input('signature');
            $vacation->data->commentaire = self::input('commentaire');
            $vacation->data->etat = 'EDITION';

            $validate = self::input('validate');
            if (!is_null($validate)){
                $vacation->data->etat = 'VALIDATED';
            }
            $vacation->save();
            $app::$pub->info[]="Les données ont été enregistrées";
        }

        $year = \base\App::$vacationYear;
        $P = $app::NS('Personne')::loadOneWhere("actif and uid='{$vacation->data->uid}'");
        $struct = new Struct($year);
        $E = $struct->getECUEByCode($vacation->data->codeECUE);


        if ($vacation->data->etat != 'VALIDATED'){
            $structureList = \base\Controllers\VacationController::convertForJS($struct->getUsefulStructureList([]));
            $cursusList = $struct->getCursusList();
            $semestreList = $struct->getSemestreList();
            $options = [
                'D' => $vacation->data,
                'P' => $P,
                'E' => $E,
                'year' => $year,
                'struct' => $struct, 
                'structureList' => $structureList, 
                'cursusList' => $cursusList, 
                'semestreList' => $semestreList,
            ];
            if ($app::$auth->hasRole('Gestionnaire')){
                $mcf = $app::NS('Statut')::loadOneWhere("nom = 'MCF'");
                $prof = $app::NS('Statut')::loadOneWhere("nom = 'PROF'");
                $PList = $app::NS('Personne')::loadWhere("statut in ({$mcf->id}, {$prof->id})");
                $options['PList'] = $PList;
            }

            $app::$cmpl["withJQuery"]=true;
            //$app::$cmpl['withCKEditor'] = true;
            $app::$cmpl["withKnockout"]=true;
            $app::$cmpl["withDataTables"]=true;

            echo $app::$viewer->render('vacation/fiche.html.twig', $options);
            exit();
        }

        $vacation->buildPDF();

    }

    public static function devalide($id){
        $vacation = new Vacation($id);
        $vacation->devalide();
    }

    public static function archive($id){
        $vacation = new Vacation($id);
        $vacation->archive();
    }


    public static function dearchive($id){
        $vacation = new Vacation($id);
        $vacation->dearchive();
    }


    public static function doDownloadValide(bool $archive=false){
        $app = \TDS\App::get();
        $uid = $app::$auth->uid;

        if ( ( $app::$auth->isAdmin() ) || ($app::$auth->hasRole('Gestionnaire')) ){
            $glob = '*_VALIDATED_*/*.pdf';
        } else {
            return;
        }

        $baseDir = Vacation::getBaseDir();

        $glob = "{$baseDir}/{$glob}";
        $fileList = glob($glob);
        $zip_path = "/tmp";
        $date = date("Y_m_d-H_i_s");
        $zip_file_name = "fiches_validation_{$date}.zip";
        $zip_file = "{$zip_path}/{$zip_file_name}";

        $dirnameList = [];
        chdir( $zip_path );
        $zip = new \ZipArchive();
        $zip->open( $zip_file_name, \ZipArchive::CREATE );
        foreach($fileList as $file){
            $dirname = array_slice(explode('/',$file),-2,1)[0];
            chdir( $zip_path );
            $zip->addFile($file, "/".basename($file));
            $dirnameList[] = $dirname;
        }
        $zip->close();

        if ($archive){
            foreach($dirnameList as $dirname){
                list($uid, $codeEcue, $etat, $dateModification, $id) = explode('_', $dirname);            
                self::archive($id);
            }
        }
        ob_clean();
        ob_end_flush();
        header( "Content-Type: application/zip" );
        header( "Content-disposition: attachment; filename={$zip_file_name}" );
        readfile( $zip_file );
    }

    public static function downloadValide(){
        self::doDownloadValide(false);
    }

    public static function downloadValideAndArchive(){
        self::doDownloadValide(true);
    }

    public static function getDoc($id){
        $app = \TDS\App::get();

        $vacation = new Vacation($id);
        $vacation->downloadPDF();
    }

    private static function input($var, $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS){
        $tmp = filter_input(INPUT_POST, $var, $filter);
        if (is_null($tmp)) {return $tmp;}
        return str_replace("&#039;", "'", html_entity_decode($tmp));
    } 

    public static function liste(){
        $app = \TDS\App::get();
        $uid = $app::$auth->uid;

        if ( ( $app::$auth->isAdmin() ) || ($app::$auth->hasRole('Gestionnaire')) ){
            $glob = '*';
        } else {
            $glob = "{$uid}_*";
        }

        $struct = new Struct(\base\App::$vacationYear);
        $baseDir = Vacation::getBaseDir();

        $dirList = glob("{$baseDir}/{$glob}", GLOB_ONLYDIR);
    
    
        $creationList = [];
        $editionList = [];
        $validationList = [];
        $archivedList = [];
        $autreList = [];
        $deletedList = [];
        foreach($dirList as $dir){
            $tmp = explode('/', $dir);
            $dirname = end($tmp);
            list($uid, $codeEcue, $etat, $dateModification, $id) = explode('_', $dirname);
            $P = $app::NS('Personne')::loadOneWhere("actif and uid='{$uid}'");
            $E = $struct->getECUEByCode($codeEcue);

            $fiche = [
                'P' => $P,
                'E' => $E,
                'id' => $id,
                'etat' => $etat,
                'F' => new Vacation($id),
            ];

            switch ($etat){
                case 'CREATION':
                    $fiche['tool'] = "<a href='/{$app::$appName}/vacation/fiche/{$id}' target='_blank'>edition</a>";
                    $creationList[] = $fiche;
                    break;
                case 'EDITION':
                    $fiche['tool'] = "<a href='/{$app::$appName}/vacation/fiche/{$id}' target='_blank'>edition</a>";
                    $editionList[] = $fiche;
                    break;
                case 'VALIDATED':
                    $fiche['tool'] = "<a href='/{$app::$appName}/vacation/getDoc/{$id}' target='_blank'>chargement</a>";
                    if ($app::$auth->hasRole("respAdmin") ){
                        $fiche['tool'] .= " <a href='/{$app::$appName}/vacation/devalide/{$id}' target='_blank'>Devalide</a>";
                    }
                    $validationList[] = $fiche;
                    break;
                case 'ARCHIVED':
                    $fiche['tool'] = "<a href='/{$app::$appName}/vacation/getDoc/{$id}' target='_blank'>chargement</a>";
                    if ($app::$auth->hasRole("respAdmin") ){
                        $fiche['tool'] .= " <a href='/{$app::$appName}/vacation/dearchive/{$id}'>Dearchive</a>";
                    }
                    $archivedList[] = $fiche;
                    break;
                case 'DELETED':
                    $fiche['tool'] = "<a href='/{$app::$appName}/vacation/fiche/{$id}' target='_blank'>edition</a>";
                    $deletedList[] = $fiche;
                    break;
                default:
                    $fiche['tool'] = "<a href='/{$app::$appName}/vacation/fiche/{$id}' target='_blank'>???</a>";
                    $autreList[] = $fiche;
                    break;
            }
        }

        $data = [
            'year' => \base\App::$vacationYear,
            'creationList' => $creationList,
            'editionList' => $editionList,
            'validationList' => $validationList,
            'autreList' => $autreList,
            'archivedList' => $archivedList,
            'deletedList' => $deletedList,
        ];

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;
        echo $app::$viewer->render('vacation/liste.html.twig', ['D' => $data]);
    }

    public static function createFiche(){
        $app = \TDS\App::get();
        $vacation = new Vacation();
        $app::$router->redirect("/{$app::$appName}/vacation/liste");
    }

    public static function deleteFiche($id){
        $app = \TDS\App::get();
        $vacation = new Vacation($id);
        $vacation->delete();
        echo $app::$viewer->render('vacation/deleteFiche.html.twig', ['V' => $vacation]);
    }

    public static function deleteFichePermanent($id){
        $app = \TDS\App::get();
        $vacation = new Vacation($id);
        $vacation->deletePermanent();
        echo $app::$viewer->render('vacation/deleteFiche.html.twig', ['V' => $vacation]);
    }
    

    public static function getCursusList(){
        $struct = new \base\Struct(\base\App::$vacationYear);
        $structureID = filter_input(INPUT_POST,'structure', FILTER_VALIDATE_INT);
    }


    public static function getEtapeList(int|null $structureId, int|null $cursusId){
        $struct = new \base\Struct(\base\App::$vacationYear);

        if (is_null($cursusId) || is_null($structureId)) return;
        $etapeList = $struct->getEtapeList($structureId, $cursusId);
        echo json_encode(self::convertForJS($etapeList));

    }

    public static function getEtapeListJSON(){
        $structureId = filter_input(INPUT_POST,'structure', FILTER_VALIDATE_INT );
        $cursusId = filter_input(INPUT_POST,'cursus', FILTER_VALIDATE_INT );
        self::getEtapeList($structureId, $cursusId);        
    }

    public static function search(){
        $app = \base\App::get();

        $struct = new \base\Struct(\base\App::$vacationYear);

        $structureId = filter_input(INPUT_POST,'structure', FILTER_VALIDATE_INT);
        $cursusId = filter_input(INPUT_POST,'cursus', FILTER_VALIDATE_INT);
        $semestreId = filter_input(INPUT_POST,'semestre', FILTER_VALIDATE_INT);
        $etapeId = filter_input(INPUT_POST,'etape', FILTER_VALIDATE_INT);
        $ecueList =  $struct->getEcueList($structureId, $cursusId, $semestreId, $etapeId);
        echo $app::$viewer->render('vacation/search.html.twig', ['ecueList' => $ecueList]);
    }

    public static function getInfoECUE($code){
        $year = \base\App::$vacationYear;

        $struct = new \base\Struct($year);

        $ecue = $struct->getECUEByCode($code);
        if (is_null($ecue)){
            $info = [
                "code" => $code,
                "nom" => "HS",
                "structure" => "HS",
                "etape" => "HS",
                "cursus" => "HS",
                "semestre" => "HS",
                "EOTP" => "HS",
            ];
            return $info;
        }
        $etape = $ecue->getEtape();
        $structure = $etape->getStructure();

        $info = [
            "code" => $ecue->getCode(),
            "nom" => $ecue->getNom(),
            "structure" => $structure->getNom(),
            "etape" => $etape->getNom(),
            "cursus" => $etape->getCursusName(),
            "semestre" => $ecue->getPeriode(),
            "EOTP" => $etape->getEOTP(),
        ];
        return $info;

    }


    public static function infoECUE($code){
        $info = self::getInfoECUE($code);
        echo json_encode($info);
    }


    public static function importOne($inDB, $codeEcue, $nom, $prenom, $uid, $hCM, $hCMTD, $hTD, $hTP, $gestionnaire){
        $app = \TDS\App::get();
        
        $year  = (int)\base\App::$vacationYear;
        $yearP = $year+1;
        $vacation = new Vacation();

        $info = self::getInfoECUE($codeEcue);
        $vacation->data->etat     = 'EDITION';
        $vacation->data->inDB     = $inDB;
        $vacation->data->codeECUE = $codeEcue;
        $vacation->data->nom      = $nom;
        $vacation->data->prenom   = $prenom;
        $vacation->data->uid      = $uid;
        $vacation->data->hCM      = $hCM;
        $vacation->data->hCMTD    = $hCMTD;
        $vacation->data->hTD      = $hTD;
        $vacation->data->hTP      = $hTP;
        $vacation->data->niveau   = $info['cursus'];
        $vacation->data->formation= $info['etape'];
        $vacation->data->semestre = $info['semestre'];
        $vacation->data->niveau   = $info['cursus'];
        $vacation->data->intituleEnseignement = $info['nom'];;
        $vacation->data->EOTP = $info['EOTP'];;
        $vacation->data->gestionnaireScol = $gestionnaire;
        $vacation->data->commentaire = '';
        $vacation->data->dateDebut = "{$year}-09-01";
        $vacation->data->dateFin = "{$yearP}-06-30";
        if ($vacation->data->semestre=='1'){
            $vacation->data->dateFin = "{$year}-12-30";
        }
        if ($vacation->data->semestre=='2'){
            $vacation->data->dateDebut = "{$yearP}-01-01";
        }
        $vacation->save();
        return $vacation;
    }

    public static function importFromText(){
        $app = \TDS\App::get();

        $data = filter_input(INPUT_POST,'data');
        if (is_null($data)){
            echo $app::$viewer->render('vacation/importFromText.html.twig');
            exit();
        }

        $added = 0;

        $data = explode("\n", $data);

        foreach($data as $line){
            $d = explode("\t", $line);

            if (count($d)<10) continue;
            if (trim($d[0])=="inDB") continue;
            if (trim($d[2])=="Nom") continue;
            if (trim($d[2])=="") continue;

            $inDB = trim($d[0]);
            $codeEcue = trim($d[1]);
            $nom = trim($d[2]);
            $prenom = trim($d[3]);
            $uid = trim($d[4]);
            $hCM = str_replace(',','.', trim($d[5]));      $hCM = empty($hCM)?"0":$hCM;
            $hCMTD = str_replace(',','.', trim($d[6]));    $hCMTD = empty($hCMTD)?"0":$hCMTD;
            $hTD = str_replace(',','.', trim($d[7]));      $hTD = empty($hTD)?"0":$hTD;
            $hTP = str_replace(',','.', trim($d[8]));      $hTP = empty($hTP)?"0":$hTP;
            $gestionnaire = trim($d[9]);
            $vacation = self::importOne($inDB, $codeEcue, $nom, $prenom, $uid, $hCM, $hCMTD, $hTD, $hTP, $gestionnaire);
            $added++;
        }

        echo $app::$viewer->render('vacation/importFromText.html.twig', [
            'added' => $added,
            'data' => $data,
        ]);
    }

    public static function listeInDB(){
        $app = \TDS\App::get();

        //    public static function importOne($inDB, $codeEcue, $nom, $prenom, $uid, $hCM, $hCMTD, $hTD, $hTP, $gestionnaire){
        $year = \base\App::$vacationYear;

        $struct = new \base\Struct($year);
        $baseName = $app::$appName."{$year}";
        $db = new \TDS\Database($baseName, $app::$baseUser, $app::$basePwd, 'localhost' );
        pg_set_client_encoding($db->conn, "UNICODE");

        $vacationList = $db->getAll("
        SELECT
            E.id as id, 
            E.intitule as intitule,
            E.code as code,
            P.nom as nom,
            P.prenom as prenom,
            VDH.cm as hCM,
            VDH.ctd as hCMTD,
            VDH.td as hTD,
            VDH.tp as hTP
            FROM voeu AS V
            LEFT JOIN personne as P on P.id = V.personne
            LEFT JOIN enseignement as E on E.id = V.enseignement
            LEFT JOIN voeu_detail_heures as VDH on VDH.id = V.id
            LEFT JOIN statut as S on P.statut = S.id
            WHERE P.id>0 and E.id>0 and V.id>0
            AND V.actif AND P.actif AND E.actif
            AND S.nom LIKE 'Vac%'
            ORDER BY E.code
        ");

        $validationVacataireList = [];
        foreach($vacationList as $vacation){

            $ecue = $struct->getECUEByCode($vacation->code);
            if (is_null($ecue)){
            $semestre = 'HS';
            $intitule = 'HS';
            } else {
                $semestre = $ecue->getPeriode();
                $intitule = $ecue->getNom();
            }

            $responsableList = $db->getAll("
            SELECT
                P.uid
            FROM voeu as V
            LEFT JOIN personne P on P.id = V.personne
            LEFT JOIN statut as S on S.id = P.statut
            WHERE V.enseignement = {$vacation->id}
            AND V.correspondant
            AND V.actif
            AND P.actif
            AND S.nom in ('MCF', 'PROF')
            ");

            $rList  = [];
            foreach($responsableList as $r){
                $rList[]  = $r->uid;
            }
            $uid = join(',', $rList);

            $validationVacataireList[] = [
                'inDB' => 'in',
                'code' => $vacation->code,
                'nom' => $vacation->nom,
                'prenom' => $vacation->prenom,
                'uid' => $uid,
                'hCM' => $vacation->hcm,
                'hCMTD' => $vacation->hcmtd,
                'hTD' => $vacation->htd,
                'hTP' => $vacation->htp,
                'gestionnaire' => 'XXX',
                'intituleStruct' => $intitule,
                'semestre' => $semestre,
                'intituleDB' => $vacation->intitule,
            ];
        }
        $nameList = [];
        $v = $validationVacataireList[0];
        foreach($v as $key => $value){
            $nameList[]= $key;
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        echo $app::$viewer->render('admin/standardList.html.twig', ['title' => 'Liste pour import fiches Vacataire inDB', 'nameList' => $nameList, 'data'=> $validationVacataireList]);
    }


    public static function getInfoFromCodeECUEList(){
        $app = \TDS\App::get();

        $data = filter_input(INPUT_POST,'data');
        if (is_null($data)){
            echo $app::$viewer->render('vacation/getInfoFromCodeECUEList.html.twig');
            exit();
        }

        $data = explode("\n", $data);
        $L = [];
        foreach($data as $line){
            $d = explode("\t", $line);
            $codeEcue = trim($d[0]);
            $L[] = self::getInfoECUE($codeEcue);
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('vacation/getInfoFromCodeECUEList.html.twig', ['D' => $data, 'L' => $L]);
    }

}


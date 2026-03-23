<?php
namespace foire\Controllers;

use base\Controllers\VacationController;
use base\Struct;
use foire\Model\commentaire_enseignement;
use foire\Model\Enseignement;
use foire\Model\Heritage;
use foire\Model\Personne;
use foire\OSE;
use TDS\Query;
use TDS\App;

use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use TDS\Document;
use base\Vacation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Knp\Snappy\Pdf;

use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;

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


use Olivier\Tempconv\TemperatureConverter;


function toInt($val){
    if (empty($val)){
        return 0;
    }
    return intval($val);
}



class TestController extends \base\Controllers\TestController {


/*
    public static function test1(){
        //var_dump('désactivation de /foire/test/test1'); exit();
        // pour faire un formulaire de recherche d'une ECUE

        $app = \TDS\App::get();

        $options = [];
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl['withKnockout'] = true;
        $app::$cmpl['withDataTables'] = true;


        $struct = new Struct(2024);

        $structureList = \base\Controllers\VacationController::convertForJS($struct->getUsefulStructureList([]));
        $cursusList = $struct->getCursusList();
        $semestreList = $struct->getSemestreList();
        $options = ['struct' => $struct, 'structureList' => $structureList, 'cursusList' => $cursusList, 'semestreList' => $semestreList];

        echo $app::$viewer->render("test/test1.html.twig", $options);
    }
*/



    public static function test1(){
        //var_dump('désactivation de /foire/test/test1'); exit();

        $app = \TDS\App::get();
        $ecue = "PFFAE025";
        $vacationList = \base\Vacation::getVacationFromEcueCode($ecue);
        var_dump($vacationList);
        exit();



        $cursusName = 'L2';
        $structureName = 'UFR de Physique';

        $cursusList = Struct::getCursusList();
        $cursusID = 0;
        foreach($cursusList as $cursus){
            if ($cursus->nom == $cursusName){
                $cursusID = $cursus->id;
            }
        }

        $struct = new Struct();
        $structure = $struct->getStructureByNom($structureName);
        $structureID = $structure->getId();

        $modalite  = [];
        $selectors = [
            'cursus'    => [$cursusID],
            'semestre'  => [2],
            'structure' => [$structureID], // [35],
            'etape'     => [],
            'modalite'  => [],
           'actif'      => true
        ];

        $with = new \stdClass;
        $with->withInactif        = false;
        $with->onlyInactif        = false;
        $with->withSousEffectif   = false;
        $with->nonPrioritaire     = false;
        $with->withStructure      = false;
        $with->withEtape          = false;
        $with->withEquipe         = false;
        $with->withCorrespondant  = false;
        $with->withDomaine        = false;


        $searchValue="";

        $enseignementList = $app::NS('Enseignement')::search($searchValue, $selectors , $modalite, $with);
// var_dump($enseignementList);
        foreach($enseignementList as $Ens){
            $E = $Ens['enseignement'];
            $ECUE = $Ens['ecue'];
            $ET = $ECUE->getEtape();
            $periode = $ECUE->getPeriodeName();
            $ETL = $ECUE->getEtapes();
// var_dump($ET);
            echo "<p>{$E->code} {$E->nom} {$ET->getNom()} </p>";
            echo "<ul>";
            foreach($ETL as $etape){
                echo "<li>{$etape->getNom()}</li>";
            }
            echo "</ul>";
        }
    }

    public static function test2(){
        var_dump('désactivation de /foire/test/test2'); exit();

        $app = \TDS\App::get();
        $year  = (int)\base\App::$vacationYear;
        $yearP = $year+1;

        $glob = '*';

        $baseDir = Vacation::getBaseDir();
        $dirList = glob("{$baseDir}/{$glob}", GLOB_ONLYDIR);
    
        foreach($dirList as $dir){
            $tmp = explode('/', $dir);
            $dirname = end($tmp);
            list($uid, $codeEcue, $etat, $dateModification, $id) = explode('_', $dirname);
            switch ($etat){
                case 'VALIDATED':
                case 'ARCHIVED':
                    break;
                default:
                    $vacation = new Vacation($id);
                    $vacation->data->dateDebut = "{$year}-09-01";
                    $vacation->data->dateFin = "{$yearP}-06-30";
                    if ($vacation->data->semestre == '1'){
var_dump('Cest un semestre 1');
                        $vacation->data->dateFin = "{$year}-12-30";
                    } elseif ($vacation->data->semestre == '2'){
var_dump('Cest un semestre 2');
                        $vacation->data->dateDebut = "{$yearP}-01-01";
                    } else {
var_dump("Ni l'un ni l'autre");
                    }
                    $vacation->save();
                    var_dump($vacation->data);
                    break;
            }
        }     
    }

    public static function test3(){
        var_dump('désactivation de /foire/test/test3'); exit();
        // pour créer un pdf à partir du html (en ajouatnt des images...
        $app = \TDS\App::get();
        $year  = (int)\base\App::$vacationYear;
        $yearP = $year+1;

        $glob = '*';

        $baseDir = Vacation::getBaseDir();
        $dirList = glob("{$baseDir}/{$glob}", GLOB_ONLYDIR);
    
        foreach($dirList as $dir){
            $tmp = explode('/', $dir);
            $dirname = end($tmp);
            list($uid, $codeEcue, $etat, $dateModification, $id) = explode('_', $dirname);
            switch ($etat){
                case 'VALIDATED':
                case 'ARCHIVED':
                    break;
                default:
                    $vacation = new Vacation($id);
                    var_dump($vacation->data);
                    break;
            }
        }     
    }

    public static function test4(){
        var_dump('désactivation de /foire/test/test4'); exit();
        $app = \TDS\App::get();
        $options = [
            'app' => new $app(),
        ];

        $personne = $app::$auth->user;
        $code = "PH45E015";

        $struct = new Struct();
        $ecue = $struct->getECUEByCode($code);  
        var_dump($ecue->canCreateEnseignementInDatabase());
      
        $EList = $app::NS('Enseignement')::loadWhere(" code LIKE '%{$code}%'");
        var_dump($ecue);
        var_dump(count($EList));
        

        //echo $app::$viewer->render("test/test4.html.twig", $options);
    }

}

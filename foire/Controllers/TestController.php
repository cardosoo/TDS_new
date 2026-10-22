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


function toInt(string $val){
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
        
        /*
        $ecue = "PFFAE025";
        $vacationList = \base\Vacation::getVacationFromEcueCode($ecue);
        var_dump($vacationList);
        exit();
        */


        $cursusName = 'L2';
        $structureName = 'UFR de Physique';

        $cursusList = Struct::getCursusList();
        var_dump($cursusList);
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
        phpinfo();
        
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
        // var_dump('désactivation de /foire/test/test4'); exit();


        $codeList = [
            'PFDAE055',
            'PFGAE015',
            'PFSBE055',
            'PFDAE075',
            'PFDBE045',
            'PFSAE115',
            'PFSAE015',
            'PFSBE015',
            'PFPAE035',
            'PFGAE185',
            'PFDBE025',
            'PFGAE115',
            'PFGBE015',
            'PFDAE065',
            'PFBAE025',
            'PFQAE025',
            'PFKAE025',
            'PFKAE015',
            'PFQAE045',
            'PFQAE015',
            'PFOAE095',
            'PFOAE225',
            'PFSAE095',
            'PFOAE035',
            'PFBAE035',
            'PFDAE015',
            'PFDAE085',
            'PFKBE055',
            'PFBAT015',
            'PFBAE015',
            'PFDAE025',
            'PFGBE125',
            'PFOAE145',
            'PFPAE015',
            'PFQAE055',
            'PFQAE035',
            'PFPAE125',
            'PFDBE015',
            'PFPBE025',
            'PFKBE015',
            'PFOAE115',
            'PFSAE105',
            'PFBAE045',
            'PFLAE015',
            'PFGBE145',
            'PFLAE145',
            'PFBAE095',
            'PFLAE135',
            'PFDAE045',
            'PFOAE015',
            'PFGAE035',
            'PFGAE345',
            'PFGAE275',
            'PFGAE245',
            'PFGBE105',
            'PFKBE035',
            'PFGAE145',
            'PFLAE105',
            'PFLAE025',
            'PFPAE105',
            'PFGAE335',
            'PFSAE065',
            'PFDAE035',
        ];
        
        $app = \TDS\App::get();
        $structNSC = $app::NSC('Struct');
        $struct = new $structNSC(2025);

        $ecueList = [];
        $etapeList = [];

        foreach($codeList as $code){
            $ecue = $struct->getECUEByCode($code);
            if (!is_null($ecue)){
                $etape = $ecue->getEtape();
                if (!is_null($etape)){
                    $ecueList[$code] = (object)[
                        'code' => $code,
                        'ecue' => $ecue,
                        'etape' => $etape->getCode(),
                    ];
                    $etapeList[$etape->getCode()] = $etape;
                } else {
                    echo "<p>{$code} => Aucune étape trouvée</p>";
                }
            } else {
                echo "<p>{$code} => Aucune ECUE trouvée</p>";
            }
        }

        $syncId = 27;

        foreach($etapeList as $codeEtape => $etape){
            var_dump($codeEtape);

            $syncId++;


            $command = <<<END
    curl --url 'https://mondossierweb.app.u-pariscite.fr/UIDL/?v-uiId=0' \
    -H 'accept: */*' \
    -H 'accept-language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7' \
    -H 'cache-control: no-cache' \
    -H 'content-type: application/json; charset=UTF-8' \
    -b 'JSESSIONID=1ABF81C6D9F8B85E2F10F109C01B69E7.mdw2; UPLBID=mdw2' \
    -H 'origin: https://mondossierweb.app.u-pariscite.fr' \
    -H 'pragma: no-cache' \
    -H 'priority: u=1, i' \
    -H 'referer: https://mondossierweb.app.u-pariscite.fr/?continue' \
    -H 'sec-ch-ua: "Not A(Brand";v="99", "Chromium";v="154"' \
    -H 'sec-ch-ua-mobile: ?0' \
    -H 'sec-ch-ua-platform: "Linux"' \
    -H 'sec-fetch-dest: empty' \
    -H 'sec-fetch-mode: cors' \
    -H 'sec-fetch-site: same-origin' \
    -H 'user-agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36' \
    --data-raw '{"csrfToken":"22a09ccc-b391-4e11-ab2c-341a3a26257d","rpc":[["13","v","v",["c",["i",6]]],["13","v","v",["curText",["s","{$codeEtape}"]]]],"syncId":{$syncId},"clientId":{$syncId}}'
END;

                    

            $output = shell_exec($command);
            $output = substr($output, 8);
            $output = json_decode($output, true);
            //var_dump($output);
            $state = $output[0]['state'];
            //var_dump($state);
            foreach($state as $key => $value){
                if (isset($value['caption'])){
                    /* var_dump([
                        'key' => $key,
                        'caption' => $value['caption'],
                        'value' => $code,
                    ]); */
                    if (str_contains($value['caption'], $codeEtape)){
                        var_dump("{$key} => {$value['caption']}");
                    }
                }
            }
        }

        /*




        $app = \TDS\App::get();

        $enseignementList = $app::NS('Enseignement')::loadWhere('actif and id>0');
        $L = [];
        foreach ($enseignementList as $E) {            
            $code = $E->code;
            $etapeL = $E->getStructEtapeList();
            $mainEtape = null;
            $etapeList = [];
            if (!isset($etapeL[$code])){
                continue;
            }
            $etapeL = $E->getStructEtapeList()[$code];
            foreach($etapeL as $e){
                if (!is_null($e)){
                    if (is_null($mainEtape)){
                        $mainEtape = $e;
                    } else {
                        $etapeList[] = $e;
                    }
                }
            }
            $L[] = ['E' => $E, 'ET' => $mainEtape, 'ETL' => $etapeList];
        }            

    
        //var_dump($L);
        //exit();
        $app::$cmpl["withJQuery"] = true;
        $app::$cmpl["withDataTables"] = true;

        echo $app::$viewer->render('gestionnaire/repartitionParNiveau.html.twig', ['L' => $L]);
*/
    }

}

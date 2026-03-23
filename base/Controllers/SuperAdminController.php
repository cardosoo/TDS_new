<?php
namespace base\Controllers;

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

use \Propel\Runtime\ActiveQuery\Criteria;


class SuperAdminController extends \TDS\Controller {

    private static $typeUEList = [];
    private static $payeurList = [];
    
    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('superAdmin/index.html.twig');
    }

    public static function searchLDAP(){
        $app = \TDS\App::get();

        $args = [
            'uid'   => FILTER_UNSAFE_RAW,
            'name'  => FILTER_UNSAFE_RAW,
        ];
        $propList = filter_input_array(INPUT_POST, $args);
        
        if (is_null($propList)){
            $uid = "";
            $name = "";
        } else {
            $uid  = $propList['uid']?$propList['uid']:"";
            $name = $propList['name']?$propList['name']:"";
        }

        $uidFilter  =  $uid==''?'':"(uid=*{$uid}*)(supannAliasLogin=*{$uid}*)(supannEmpId={$uid})";
        $nameFilter = $name==''?'':"(cn=*{$name}*)(displayName=*{$name}*)";
        
        $filter = "";
        $count = 0;
        if ('' !== $uidFilter){
            $filter.=$uidFilter;
            $count+=2;
        }
        if ('' !== $nameFilter){
            $filter.=$nameFilter;
            $count+=2;
        }
        
        if ($count>1){ // comme on a construit les 2 filtres c'est toujour plus grand que 2
            $filter = "(|{$filter})";
        }
        $rep = null;
        if ($count>0){
            $ldap = new \TDS\LDAPExtern();
            $rep = $ldap->list($filter, ['uid', 'displayName', 'mail', 'eduPersonPrimaryAffiliation', 'eduPersonAffiliation', 'supannAliasLogin', 'supannEmpId']);
            $rep = $ldap->reformat($rep);

            // Ici on récupère la liste des uid qui sont présents dans la base de données
            // sous forme d'un tableau 
            $uidList = [];
            foreach($rep as $r){
                $uidList[]=$r->uid;
            }
            $uidL = join("', '",$uidList);

            if (!empty($uidL)){
                $q = new \TDS\Query($app::NS('Personne'), 'P', ['uid']);
                $q->addSQL("WHERE {$q->P_uid} in ('{$uidL}')");
                $r = $q->exec();
                // pour les uid qui sont dans la base on ajoute un elem inBase qui contient l'id de la personne
                foreach($r as $elm){
                    $rep[$elm['p']->uid]->inBase = $elm['p']->id;
                }
            }

        }

        // Ici il faut remettre en forme les enregistrements et voir si ils existe dans la base de données...

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('admin/searchLDAP.html.twig', ['ldapList' => $rep, 'search' => ['uid' => $uid, 'name' => $name ] ]);
    }

    public static function addFromLDAP($uid){
        $app = \TDS\App::get();

        $ldap = new \TDS\LDAPExtern();
        $rep = $ldap->list( "(uid={$uid})", ['uid', 'sn', 'givenname', 'mail', 'supannEmpId']);
        $rep = $ldap->reformat($rep);
        $count = count($rep);
        if ($count != 1){ // cela ne devrait jamais se produire...
            $app::$pub->error[] = "Il y a {$count} entrée(s) dans le LDAP pour l'uid {$uid}";
            $NS = "{$app::$appName}\Controllers\AdminController";
            $NS::home();
            exit();
        }
        $personne = $app::loadFromUid($uid);
        if (!is_null($personne)){
            $app::$pub->info[] = "l'uid {$uid} est déjà présent dans la base de données";
        } else {
            $personneNS = $app::NS("Personne");
            $personne = new $personneNS();
            $personne->uid = $uid;
            
            if (isset($rep[$uid]->supannempid)){
                $personne->ose = $rep[$uid]->supannempid;
            } else {
                $app::$pub->info[] = "On a pas trouvé de code OSE dans le LDAP our l'uid {$uid}";
            }

            $personne->nom = $rep[$uid]->sn;
            $personne->prenom = $rep[$uid]->givenname;
            $personne->email = $rep[$uid]->mail;
            $personne->save();
            $app::$pub->success[] = "l'uid {$uid} a été ajouté dans la base de données avec {$personne->prenom} {$personne->nom}";
        }
        $app::$router->redirect("/{$app::$appName}/CRUD/Personne/{$personne->id}");
        exit();
    }


    private static function updateOSEFromLDAP($personne){
        $ldap = new \TDS\LDAPExtern();
        
        $uid = $personne->uid;
        $rep = $ldap->list( "(uid={$uid})", ['uid', 'sn', 'givenname', 'mail', 'supannEmpId']);
        $rep = $ldap->reformat($rep);  
        
        if (empty($rep) ) return 'noLDAP'; // Pas trouvé dans le LDAP
        if (!isset($rep[$uid]->supannempid) ) return 'noEmpId'; 
        
        $personne->ose = $rep[$uid]->supannempid;
        $personne->save();
        return 'Ok';
    }

    public static function updateOSE_FromLDAP(){
        $app = \TDS\App::get();
/*
        $personne = $app::NS('Personne')::loadFromUid('larthu22');   // présent dans le LDAP mais sans supannempid
        $personne = $app::NS('Personne')::loadFromUid('--sdececco'); // absent du LDAP
        $personne = $app::NS('Personne')::loadFromUid('ocardoso'); // présent dans le LDAP avec supannempid
*/


        $personneList = $app::NS('Personne')::loadWhere('actif'); // on récupère tous les actifs

        $absentLDAP = [];
        $sansOSE = [];
        $ok = [];

        foreach($personneList as $personne){
            $found = self::updateOSEFromLDAP($personne);
            switch ($found) {
                case 'noLDAP':
                    $absentLDAP[]=$personne;
                    break;
                case 'Ok':
                    $ok[]=$personne;
                    break;
                case 'noEmpId':
                    $sansOSE[]=$personne;
                    break;
            }
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('admin/updateOSE.html.twig', ['sansOSE' => $sansOSE, 'absentLDAP' => $absentLDAP, 'ok' => $ok ]);
    }


    public static function getColList($handle, $isUTF8 = true){
        $tmp = fgetcsv($handle, 0, ";");
        $colList=[];

        foreach($tmp as $col){
            $colList[] = $isUTF8?$col:utf8_encode($col);            
        }
        return $colList;
    }

    public static function float($st){
        return floatval(str_replace(',','.', $st));
    }

    public static function importReferentielOSE(){
        $app= \TDS\App::get();
        if (($handle = fopen("{$app::$pathList['plus']}/{$app::$appName}/OSE/listingServices.csv", "r")) !== FALSE) {
            $colList = self::getColList($handle);
            $count = 0;
            $countRef = 100;
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                $ligne = new \stdClass();
                foreach ($data as $key => $d) { // pour mettre les éléments dans les bonnes lignes
                    $ligne->{$colList[$key]} = $d;
                }    
                $code = $ligne->{'Code enseignement'};
                if (empty($code) ) { // alors c'est normalement dans le réfentiel et on le considère
                    $count++;
                    $codeUE = trim($ligne->{'Enseignement ou fonction référentielle'});
                    $ueNS = $app::NS('UE');
                    $codeUE_ = pg_escape_string($app::$db->conn, $codeUE);
                    $UE = $ueNS::loadOneWhere("trim(nom) = '{$codeUE_}'"); // on récupère l'UE correspondant à l'élément du référentiel
                    if (is_null($UE)){ // j'ai l'impression que si on trouve pas l'UE, il faut la créer.
                        var_dump($codeUE_);
                        $UE = new $ueNS();
                        $UE->nom = $codeUE_;
                        $UE->save();
                    } 
                    $description = trim(str_replace(["\n", "\r", "\l"]," ",$ligne->{'Commentaires'}));
                    if ($description == ''){
                        $description = $ligne->{'Enseignement ou fonction référentielle'};
                    }
                    $descriptionShort = substr($description, 0, 98);
                    $found = false;
                    foreach($UE->ecueList as $ecue){
                        if (trim($ecue->nom) == trim($descriptionShort) ){ // alors il y a déjà une ECUE qui va bien
                            $found = true;
                        }
                    }
                    if (!$found){ // l'ECUE n'existe pas -> il faut créer l'ECUE et l'enseignement correspondant
                        $countRef++;

                        $ecueNS = $app::NS('ECUE');
                        $enseignementNS = $app::NS('Enseignement');

                        $enseignement = new $enseignementNS();
                        $enseignement->intitule = $descriptionShort;
                        $enseignement->nuac = "REF_{$countRef}";
                        $enseignement->syllabus = $description;
                        $enseignement->save();

                        $ECUE = new $ecueNS();
                        $ECUE->nom = $descriptionShort;
                        $ECUE->ue = $UE->id;
                        $ECUE->code = "REF_{$countRef}";
                        $ECUE->enseignement = $enseignement->id;

                        $ECUE->save();
                        
                        // C'est môche mais c'est pour permettre d'ajouter l'ECUE à la liste des ECUE des l'UE.
                        $tmp = $UE->ecueList;
                        $tmp[] = $ECUE;
                        $UE->ecueList = $tmp;

                    }
                // on cherche pour voir si il y a déjà une ECUE qui a pour nom le Commentaire;
                }
//                if ($count>3){
//                    exit();
//                }
            }
        }
        var_dump('Done');
        exit();
    }

    /*************************************
     *  importation des services à parir du fichier des listings de service de OSE
     * 
     * - à partir de OSE, on récupère les différentes personnes impliquées
     * - à partir de OSE, on récupère les différents enseignement impliqués
     * 
     */
    public static function importServicesOSE(){

        $app= \TDS\App::get();
        $voeuList = $app::NS('Voeu')::loadWhere('actif');

        $personneNS = $app::NS('Personne');
        $ecueNS = $app::NS('ECUE');

        $personneOSE = [];        
        $personneHorsBase = [];

        $ecueOSE = [];
        $ecueHorsBase = [];

        $voeuHorsBase = [];


// suppression des voeux
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
                if (  isset($personneOSE[$ose]) || isset($personneHorsBase[$ose]) ) {
                    $personne = $personneOSE[$ose] ?? $personneHorsBase[$ose];
                } else {
                    $personne = $personneNS::loadOneWhere("ose = '{$ose}'");
                    if (empty($personne)) { // alors la personne n'est pas la base de données
                        $personneHorsBase[$ose]= $ligne->{'Intervenant'};
                    } else {
                        $personneOSE[$ose]= $personne;
                    }    
                }

                
                // on vérifie si l'enseignement en question existe dans la base de données
                $code = $ligne->{'Code enseignement'};

                if (!empty($code)){ //alors c'est une enseignement normal
                    if (  isset($ecueOSE[$code]) || isset($ecueHorsBase[$code]) ) { // on déjà traité cet enseignement
                        $ecue = $ecueOSE[$code] ?? $ecueHorsBase[$code];
                    } else {
                        $ecue = $ecueNS::loadOneWhere("code = '{$code}'");
                        if (empty($ecue)) { // alors l'enseignement n'est pas la base de données
                            $ecueOSE[$code]= trim($ligne->{'Enseignement ou fonction référentielle'});
                        }
                    }    
                    if ( is_a($personne, $personneNS) and is_a($ecue, $ecueNS)){ // On a bien la personne et l'ECUE dans la base  
                        if ( ! $ecue->enseignement ){ // mais il n'y a pas d'enseignement associé
                            var_dump("Pas d'enseignement associé {$count} - «{$ecue->code}» - «{$ecue->nom}» - «{$ligne->{'Code enseignement'}}» - «{$ligne->{'Enseignement ou fonction référentielle'}}» ");
                        } else {
                            $voeuNS = $app::NS('Voeu');
                            $voeu = new $voeuNS;
                            $voeu->personne = $personne->id;
                            $voeu->enseignement = $ecue->enseignement->id;
                            $voeu->cm = self::float($ligne->{'CM'});
                            $voeu->td = self::float($ligne->{'TD'}) + self::float($ligne->{'MD'});
                            $voeu->tp = self::float($ligne->{'TP'}) + self::float($ligne->{'TP7'});
                            $voeu->bonus = self::float($ligne->{'Référentiel'});
                            $voeu-> save();
                            // if ($personne->nom == 'Nadal'){
                            //     var_dump([
                            //         'Enseignement' => $voeu->enseignement->intitule,
                            //         'Ligne' => $ligne,
                            //         'MD' => $ligne->{'MD'},
                            //         'fMD' => self::float($ligne->{'MD'}),
                            //     ]);
                            // }
                        }
                    } else {
                        $voeuHorsBase[] = $data;
                    }
                } else { // alors c'est un élément du référentiel. Ici on suppose que tous les enseignements et ecue necessaires ont déjà été créés.
                    if ( is_a($personne, $personneNS) ){
                        $description = trim(str_replace(["\n", "\r", "\l"]," ",$ligne->{'Commentaires'}));
                        if ($description == ''){
                            $description = $ligne->{'Enseignement ou fonction référentielle'};
                        }
                        $descriptionShort = substr($description, 0, 98);

                        $syllabus = pg_escape_string($app::$db->conn, $description);
                        $enseignementNS = $app::NS("Enseignement");
                        $enseignement = $enseignementNS::loadOneWhere("syllabus = '{$syllabus}'");
                        if (is_null($enseignement)){
                            
                            var_dump($syllabus);
                            var_dump($ligne);
                        }
                        $voeuNS = $app::NS('Voeu');
                        $voeu = new $voeuNS;
                        $voeu->personne = $personne->id;
                        $voeu->enseignement = $enseignement->id;
                        $voeu->bonus = self::float($ligne->{'Référentiel'});
                        $voeu-> save();    
                    } 
                }
    //            if ($count>100) break;
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

        var_dump('Done');
        exit();
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;

        echo $app::$viewer->render('admin/rien.html.twig');

        exit();
    }


    private static function getSEFromECUECode($ecueCode){
        $app= \TDS\App::get();
        $structureEnseignementNS = $app::NS("structure_enseignement");
        return $structureEnseignementNS::loadOneWhere("code_ecue = '{$ecueCode}'");
    }

    private static function getEnseignementFromECUECode($ecueCode){
        $app= \TDS\App::get();
        $enseignementNS = $app::NS("Enseignement");
        $se = self::getSEFromECUECode($ecueCode);
        if (is_null($se)) return null;
// var_dump($ecueCode, $se->id);       
        return  $enseignementNS::load($se->id);
    }

    private static function getEnseignementFromNUAC($nuac){
        $app= \TDS\App::get();
        $enseignementNS = $app::NS("Enseignement");
        return $enseignementNS::loadOneWhere("nuac = '{$nuac}'");
    }

    private static function getOrCreateTypeUE($typeUE){
        $app= \TDS\App::get();
        $typeUENS = $app::NS("TypeUE");
//        var_dump($typeUE);

        $rep = null;
        foreach(self::$typeUEList as $tUE){
//            var_dump($tUE);
            if ($tUE->nom == $typeUE){
                $rep = $tUE;
            }
        }

//        var_dump($rep);
        if (is_null($rep)){ // alors il faut le créer
            $tUE = new $typeUENS();
            $tUE->actif = true;
            $tUE->nom = $typeUE;
            $tUE->save();
//            var_dump($tUE);
            self::$typeUEList[] = $tUE;
            $rep = $tUE;
        }
        return $rep;
    }

    private static function getOrCreatePayeur($payeur){
        $app= \TDS\App::get();
        $payeurNS = $app::NS("Payeur");
        
        $rep = null;
        foreach(self::$payeurList as $pay){
            if ($pay->nom == $payeur){
                $rep = $pay;
            }
        }
        if (is_null($rep)){ // alors il faut le créer
            $pay = new $payeurNS();
            $pay->actif = true;
            $pay->nom = $payeur;
            $pay->save();
//            var_dump($pay);
            self::$payeurList[] = $pay;
            $rep = $pay;
        }
        return $rep;
    }

    private static function chooseOneInList($voeuList, $who){
        if (empty($who)) return null;

        $words = preg_split("/[\s.-]+/", $who); // permet de séparer en mots  (en coupant sur les séparateurs classiques, mais aussi les . et les -)
        $wordList = [];
        foreach($words as $word){
            if (strlen($word)> 1){
                $wordList[] = $word;
            }
        }

//        var_dump($wordList);
        foreach($voeuList as $voeu){
            $nom = $voeu->personne->nom;
            foreach($wordList as $word)
            if (false !== stripos($nom, $word) ){
                return $voeu;
            }
        }
        return null;
    }

    private static function getResponsableUEList($enseignement, $responsableUEList){
        $rep = [];
        foreach($responsableUEList as $responsableUE){
            $tmp = self::chooseOneInList($enseignement->voeuList, $responsableUE);
            if (! is_null($tmp)){
                $rep[] = $tmp;
            }
        }
        return $rep;
    }

    /***************
     * Il faut mettre comme correspondant les responsables d'UE. Pour cela il va falloir faire une recherche textuelle parmi les enseignants qui participent à l'équipe.
     * - [ ] On fait une troisième procédure qui reprend la liste des enseignements
     *   - On efface en première étape les différents types d'UE et payeurs qui existent
     *   - fait une mise à jour sur le type d'UE (colonne `Type d'UE`)
     *       - on crée les différents types d'UE au fur et à mesure qu'on les découvre dans le fichier
     *   - payeur (colonne `Origine UE`)
     *       - on crée les différents payeurs au fur et à mesure qu'ils apparaissent dans le fichier
     *   - le responsable UE (colonne `Responsables UE`)
     *       - on cherche les meilleurs correspondances parmi le membres de l'équipe d'enseignement
     */
    public static function importRespUE(){

        $app= \TDS\App::get();
 
        // suppression des reférences à typeUE et payeur 
        $app::$db->h_query('
            UPDATE enseignement set payeur=0, typeue=0;structure2025.sqlite3
        ');
 
        // suppression des types d'UE
        $app::$db->h_query('
            DELETE FROM typeue WHERE id>0;
        ');

        // suppression des payeurs
        $app::$db->h_query('
            DELETE FROM payeur WHERE id>0;
        ');

 
        // Lecture des lignes du fichier des enseignements
        if (($handle = fopen("{$app::$pathList['plus']}/{$app::$appName}/OSE/enseignements.csv", "r")) !== FALSE) {
            $colList = fgetcsv($handle, 0, ",");

            $count = 0;
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $count++;
                $ligne = new \stdClass();
                foreach ($data as $key => $d) { // pour mettre les éléments dans les bonnes lignes
                    $ligne->{$colList[$key]} = $d;
                }
                $codeECUE = $ligne->{'Code ECUE APOGEE'};

                if ($codeECUE == '') continue; // Alors je ne sais pas quoi faire il n'y a pas de code ECUE ! (je ne sais pas bien ce que j'ai fait du coup...)
                $payeur = $ligne->{'Origine UE'};
                $typeUE = $ligne->{'Type d\'UE'};

                $responsableUEList = explode('/',$ligne->{'Responsables UE'});

                // onrécupère l'enseignement correspondant
                $enseignement = self::getEnseignementFromECUECode($codeECUE);
                if (is_null($enseignement)){ // lorsque l'enseignement n'est pas lié à la structure il faut faire le récupérer directement
                    $enseignement = self::getEnseignementFromNUAC($codeECUE);
                    var_dump("{$codeECUE} -> n'est pas lié à la structure des enseignements" );
                    if (is_null($enseignement)){
                        var_dump($codeECUE, "n'a pas l'air lié à un enseignement...");
                        continue;
                    }
                }

                $typeUE = self::getOrCreateTypeUE($typeUE);
/*
                var_dump([
                    'codeECUE' => $codeECUE,
                    'enseignement' => $enseignement,
                    'typeue' => $typeUE,
                ]);
*/
                if (!is_null($typeUE)){
                    $enseignement->typeue = $typeUE->id;
                }
                
                $payeur = self::getOrCreatePayeur($payeur);
                if (!is_null($payeur)){
                    $enseignement->payeur = $payeur->id;
                }
                $enseignement->save();
//                var_dump($enseignement->intitule);
                $responsableUEList = self::getResponsableUEList($enseignement, $responsableUEList);
//                var_dump($responsableUEList);                
                foreach($responsableUEList as $voeu){
                    $voeu->correspondant = true;
                    $voeu->save();
                }

                
                if ($count >2000){
                    exit();
                }    
            }
        }
        var_dump(self::$typeUEList);
        var_dump(self::$payeurList);
    }


    /*****************************************************************************************************
     * 
     * pour l'importation de la structure des enseignements à partir de la requête qui va bien 
     * 
     * version 0.0 - on commence par faire une importation initiale sans se préocuper du fait qu'il
     *               y a sans doute des importations ultérieures à faire
     * 
     * L'importation se fait à partir du fichier extraction.csv qui est une
     * extratction effectuée à la date du 5 mai 2025 au matin et qui contient la plupart des structures
     * mais pas toutes !.
     *   
     *****************************************************************************************************/


    public static function importStructure(){
        ini_set( 'max_execution_time', 0 ); 

        $app = \TDS\App::get();
        $plus = $app::$pathList['plus'];
        $log = $app::$pathList['log'];

        $struct = new \base\Struct($app::$currentYear);
        require_once $struct->structurePath."/generated-conf/config.php"; 

        $defaultLogger = new Logger('defaultLogger');
        $defaultLogger->pushHandler(new StreamHandler("{$log}/propel.log", Level::Debug ));
        //$serviceContainer->setLogger('defaultLogger', $defaultLogger);
        

        var_dump("On est dans l'importation");

        exit();


        $ecueList = [];

        foreach($structureList as $struct){

            $structure = new Structure();
            $structure->setNom($struct->nom);
            $structure->setOseId($struct->id);
            $structure->setOseNom($struct->nom);
var_dump($struct->nom);
            $structure->save();
            foreach($struct->etapeList as $eta){
var_dump($eta);
                $etape = new Etape();
                $etape->setStructure($structure);
                $etape->setNom($eta->nom);
                $etape->setCode($eta->code);
                $etape->setEffectif(0);

                if ($eta->code != ""){
                    $etapeOSEList = json_decode(file_get_contents("https://foire.cardoso.cloudns.cl/foire/api/structOSEEtape/{$eta->code}"));

                    if (count($etapeOSEList) >= 1) {
                        $etapeOSE = $etapeOSEList[0];
                        $etape->setType($etapeOSE->type);
                        $etape->setNiveau($etapeOSE->niveau); // il me faut récupérer le niveau !
                        $etape->setDomaine($etapeOSE->domaine);                        
                        $etape->setOseId($etapeOSE->ose_id);
                        $etape->setOseNom($etapeOSE->ose_nom);
                        } else {
                            $etape->setType("");
                            $etape->setNiveau(0); // il me faut récupérer le niveau !
                            $etape->setDomaine("");                        
                            $etape->setOseId(0);
                            $etape->setOseNom("");    
                        }
                    $etape->save();
                    foreach($eta->ecueList as $ec){
                        if (strlen($ec->code)<5){
                            continue;
                        }

                        if (substr($ec->code,0,1) == "#"){
                            continue;
                        }
                        
                        
                        if (! isset($ecueList[$ec->code])){
                            $url = "https://foire.cardoso.cloudns.cl/foire/api/structOSEEcue/{$ec->code}";
                            $json = file_get_contents($url);
                            $ecueOSEList = json_decode($json);
                            $ecue = new ECUE();
                            $ecue->setNom($ec->nom);
                            $ecue->setCode($ec->code);
                            $ecue->setEtape($etape);
                            $ecue->setEffectif(0);
                            
                            if ( count($ecueOSEList)>0 ){
                                $ecueOSE = $ecueOSEList[0];
                                $ecue->setPeriode($ecueOSE->periode);
                                $ecue->setOseNom($ecueOSE->ose_nom);
                                $ecue->sethCM($ecueOSE->hCM);
                                $ecue->setgCM($ecueOSE->gCM);
                                $ecue->sethTD($ecueOSE->hTD);
                                $ecue->setgTD($ecueOSE->gTD);
                                $ecue->sethTP($ecueOSE->hTP);
                                $ecue->setgTP($ecueOSE->gTP);
                                $ecue->sethCMTD($ecueOSE->hCMTD);
                                $ecue->setgCMTD($ecueOSE->gCMTD);
                                $ecue->sethExtra($ecueOSE->hExtra);
                                $ecue->setgExtra($ecueOSE->gExtra);
                            } else {
                                $ecue->setPeriode(0);
                                $ecue->setOseNom("");
                                $ecue->sethCM(0);
                                $ecue->setgCM(0);
                                $ecue->sethTD(0);
                                $ecue->setgTD(0);
                                $ecue->sethTP(0);
                                $ecue->setgTP(0);
                                $ecue->sethCMTD(0);
                                $ecue->setgCMTD(0);
                                $ecue->sethExtra(0);
                                $ecue->setgExtra(0);
                            }
                            $ecue->save();
                            $ecueList[$ec->code] = $ecue;
                        } else {
                            $ecue = $ecueList[$ec->code];
                        }
                        
                        $EE = new ecue_etape();
                        $EE->setECUE($ecue);                    
                        $EE->setEtape($etape);
                        $EE->setEffectif(0);
                        $EE->save();

                    } 
                } 
            }
        }
    }

}
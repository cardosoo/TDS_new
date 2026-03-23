<?php
namespace tssdv\Controllers;

use stdClass;

class bureauRHEController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();

        echo $app::$viewer->render('bureauRHE/index.html.twig');
    }

    public static function listeMaquettes(){
        $app = \TDS\App::get();

        $maquetteNS = $app::NS('Maquette');
        $maquetteList = $maquetteNS::loadWhere('actif');

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('bureauRHE/listeMaquettes.html.twig', ['maquetteList' => $maquetteList ]);
    }

    public static function ListeDomaines(){
        $app = \TDS\App::get();

        $domaineNS = $app::NS('Domaine');
        $domaineList = $domaineNS::loadWhere('actif', ['acronyme']);

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('bureauRHE/listeDomaines.html.twig', ['domaineList' => $domaineList ]);
    }

    public static function listeDetailsCharges(){
        $app = \TDS\App::get();

        $EL = $app::NS('Enseignement')::loadWhere('actif');

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('bureauRHE/listeDetailsCharges.html.twig', ['EL' => $EL ]);
    }


    public static function listeSansAttache(){
        $app = \TDS\App::get();

        $sansAttacheList = $app::$db->getAll("
            SELECT
              E.id,
              E.nuac,
              E.intitule
            FROM enseignement_structure as ES
            LEFT JOIN enseignement as E on ES.id = E.id
            WHERE ES.maquette is NULL
            AND E.actif
        ");


        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('bureauRHE/listeSansAttache.html.twig', ['sansAttacheList' => $sansAttacheList ]);
    }

    public static function servicesEnseignant(){
        $app = \TDS\App::get();

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

// var_dump($services);
// exit();
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

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;


        echo $app::$viewer->render('gestionnaire/utilisationServices.html.twig', [
            'services' => $services,
            'besoins' => $besoins,
        ]);
    }


    public static function listResponsables(){
        AdminController::listCorrespondants();
    }

    public static function TousVoeux(){
        AdminController::allVOeux2();
    }

    public static function maquettePlus($id){
        $app = \TDS\App::get();

        $maquette = $app::NS('Maquette')::load($id);

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;


        echo $app::$viewer->render('bureauRHE/maquettePlus.html.twig', [
            'M' => $maquette,
        ]);
    }

    public static function majResponsables(){
        $app = \TDS\App::get();

        $data = filter_input(INPUT_POST,'data');
        if (is_null($data)){
            echo $app::$viewer->render('bureauRHE/majResponsables.html.twig');
            exit();
        }

        $done = [];
        $notECUE = [];
        $notOSE = [];

        $kept = 0;
        $deleted =0;
        $added = 0;

        $data = explode("\n", $data);

        foreach($data as $line){
            $d = explode("\t", $line);

            if (trim($d[0])=="UE intitulé") continue;
            if (!isset($d[2])) continue;

            $codeECUE = trim($d[2]);

            // recheche du code ECUE
            $rep = $app::$db->getOne("
            SELECT DISTINCT
               enseignement as id
            FROM structure_enseignement
            WHERE trim(code_ecue) = '{$codeECUE}'
            ");

            if (is_null($rep)){  // l'enseignement n'est pas trouvé
                $notECUE[] = $line;
                continue;
            }



            $id = $rep->id;
            if (isset($done[$id])){
                continue;
            }
            $done[$id]= $line;

            $E = $app::NS('Enseignement')::load($id);


            // construction du tableau $idRes qui contient les id des responsables qui sont dans la base de données
            $idResp = [];
            $index = 4;
            while (isset($d[$index])){
                $ose = trim($d[$index]);
                $index+=2;

                if (empty($ose)) continue;

                $r = $app::NS('Personne')::loadOneWhere("trim(ose) = '{$ose}'");
                if (is_null($r)){
                    $notOSE[$ose]=$d[$index-3];
                    continue;
                }

                $idResp[] = $r->id;
            }

            // passage en revue des voeux de l'enseignement pour voir ce que l'on doit faire à partir de là -> conserver ou supprimmer
            foreach($E->voeuList as $V){
                $idCible = $V->personne->id;
                if (in_array($idCible, $idResp)) { // le voeu doit correspondre à un correspondant
                    if ($V->correspondant) { // alors c'est bon !
                        $kept++;
                    } else {
                        $V->correspondant = true;
                        $V-> save();
                        $added++;
                    }
                    // on le supprime alors du tableau des $idResp
                    foreach($idResp as $k => $id){
                        if ($id == $idCible){
                            unset($idResp[$k]);
                        }
                    }
                } else { // le voeu ne doit pas correspondre à un correspondant
                    if ($V->correspondant) { // alors c'est pas bon !
                        $V->correspondant = false;
                        if ($V->voeu_bilan_ligne->heures >0){
                            $V->save();
                        } else {
                            $V->delete();
                        }
                        $deleted++;
                    } else { // ici c'est bon, il n'y a rien à faire
                    }
                }
            }
            // passage en revue des de ce qu'il reste des responsables pour lesquels il faut alors ajouter un voeu
            foreach($idResp as $idCible){
                $voeuNS =$app::NS('Voeu');
                $V = new $voeuNS;
                $V->personne = $idCible;
                $V->enseignement = $E->id;
                $V->correspondant = true;
                $V->save();
                $added++;
            }

        }

        echo $app::$viewer->render('bureauRHE/majResponsables.html.twig', [
            'notECUE' => $notECUE,
            'notOSE' => $notOSE,
            'done'  => $done,
            'added' => $added,
            'deleted' => $deleted,
            'kept' => $kept,
            'data' => $data,
        ]);
    }

    public static function majAttributairesReferentiel(){
      var_dump('on y est majAttributairesReferentiel');
    }

    public static function listeAssociationsEnseignementDomaines(){
        $app = \TDS\App::NSC('App');

        $enseignementList = $app::NS('Enseignement')::loadWhere('actif');

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        
        echo $app::$viewer->render('bureauRHE/listeAssociationsEnseignementDomaines.html.twig', ['EList' => $enseignementList]);
    }

    public static function majAssociationsEnseignementDomaines(){
        $app = \TDS\App::get();
        $domaineEnseignementNS = $app::NS('domaine_enseignement');

        $data = filter_input(INPUT_POST,'data');
        if (is_null($data)){
            echo $app::$viewer->render('bureauRHE/majAssociationsEnseignementDomaines.html.twig');
            exit();
        }

        $domaineList = $app::NS('Domaine')::loadWhere("actif");
        $domaineL =[];
        foreach($domaineList as $domaine){
            $domaineL[strtolower(trim($domaine->acronyme))] = $domaine->id;
        }

        $done = [];
        $notEns = [];
        $notDomaine = [];

        $kept = 0;
        $deleted =0;
        $added = 0;

        $data = explode("\n", $data);

        foreach($data as $line){
            $d = explode("\t", $line);

            // on vérifie que c'est bien une ligne
            if ( !is_numeric(trim($d[0])) ) continue;
            if (!isset($d[2])) continue;

            $idEns = intval(trim($d[0]));
            if ($idEns<=0) continue;
            $E = $app::NS('Enseignement')::load($idEns);
            if (is_null($E )) {
                $notEns[] = $line;
                continue;
            }

            $dL = [
            ];            
            for($i = 2; $i<count($d); $i++){
                $domaine = strtolower(trim($d[$i]));
                if ( '' !== $domaine ) {

                    if (isset($domaineL[$domaine])){ 
                        $dL[] = $domaineL[$domaine];
                    } else {
                        $notDomaine[]= $line;
                    }    
                }
            }

            // on passe en revue les associations déjà existantes : 
            foreach($E->domaine_enseignementList as $DEL){ 
                $index = array_search($DEL->domaine->id, $dL);

                if (false === $index){ // si le domaine déjà associé ne fait pas partie des domaines à associer on le supprime
                    $DEL->delete();
                    $deleted += 1;
                } else { // sinon on le supprime simplement de la liste des domaines à associer à l'enseignement
                    unset($dL[$index]);
                    $kept += 1;
                }
            }

            foreach($dL as $d){
                $DE = new $domaineEnseignementNS;
                $DE->domaine = $d;
                $DE->enseignement = $E->id;
                $DE->quotite = 1;
                $DE->save();
                $added += 1;
            }
        }
        echo $app::$viewer->render('bureauRHE/majAssociationsEnseignementDomaines.html.twig', [
            'notEns' => $notEns,
            'notDomaine' => $notDomaine,
            'done'  => $done,
            'added' => $added,
            'deleted' => $deleted,
            'kept' => $kept,
            'data' => $data,
        ]);
    }

    public static function getDataFromLDAP($uid){
        $app = \TDS\App::get();
        $rep = [];
        if ($uid=='0'){
            $rep['0']=new stdClass();
            $rep['0']->uid = "";
            $rep['0']->sn = "";
            $rep['0']->givenname = "";
            $rep['0']->mail = "";
            $rep['0']->suppanempid = "";
            return $rep;
        }

        $ldap = new \TDS\LDAPExtern();
        $rep = $ldap->list( "(uid={$uid})", ['uid', 'sn', 'givenname', 'mail', 'supannEmpId']);
        $rep = $ldap->reformat($rep);
        return $rep;
    }


    public static function doAdd(){

    }

    public static function doAddPersonne($uid){
        $app = \TDS\App::get();

        if ($uid!="0"){
            $personne = $app::loadFromUid($uid);
            if (!is_null($personne)){
                $app::$pub->info[] = "L'uid {$uid} est déjà présent dans TS online";
                $app::$router->redirect("/{$app::$appName}/personne/{$personne->id}");
                exit();
            }    
        }

        $args = [
            'nom'       => FILTER_UNSAFE_RAW,
            'prenom'    => FILTER_UNSAFE_RAW,
            'email'     => FILTER_SANITIZE_EMAIL,
            'statut'      => FILTER_SANITIZE_NUMBER_INT,
            'labo'      => FILTER_SANITIZE_NUMBER_INT,
            'domaine1'  => FILTER_SANITIZE_NUMBER_INT,
            'domaine2'  => FILTER_SANITIZE_NUMBER_INT ,
            'domaine3'  => FILTER_SANITIZE_NUMBER_INT,
        ];

        $propList = filter_input_array(INPUT_POST, $args);
        if (is_null($propList)){
            var_dump("On ne devrait pas être là...");
            exit();
        }

        $personneNS = $app::NS("Personne");
        $personne = new $personneNS();


        $rep = self::getDataFromLDAP($uid);
        if (isset($rep[$uid]->supannempid)){
            $personne->ose = $rep[$uid]->supannempid;
        } else {
            if ($uid != '0'){
                $app::$pub->info[] = "On a pas trouvé de code OSE dans le LDAP pour l'uid {$uid}";
            }
        }

        $horsLDAP = $uid == '0';

        if ($horsLDAP){
            $uid = "--{$propList['prenom']}.{$propList['nom']}";
            $personne->ose = "--{$propList['prenom']}.{$propList['nom']}";
        }

        $personne->uid = $uid;
        $personne->nom = $propList['nom'];
        $personne->prenom = $propList['prenom'];
        $personne->email = $propList['email'];
        $personne->labo = $propList['labo'];
        $personne->statut = $propList['statut'];
        $personne->actif = true;
        $personne->etat_ts = true;
        
        $personne->save();


        $domaine_personneNS = $app::NS("domaine_personne");
        if ( $propList['domaine1'] >0 ){
            $dom = new $domaine_personneNS();
            $dom->personne = $personne->id;
            $dom->domaine = $propList['domaine1'];
            $dom->quotite = 1;
            $dom->save();
        }
        if ( $propList['domaine2'] >0 ){
            $dom = new $domaine_personneNS();
            $dom->personne = $personne->id;
            $dom->domaine = $propList['domaine2'];
            $dom->quotite = 1;
            $dom->save();
        }
        if ( $propList['domaine3'] >0 ){
            $dom = new $domaine_personneNS();
            $dom->personne = $personne->id;
            $dom->domaine = $propList['domaine3'];
            $dom->quotite = 1;
            $dom->save();
        }
        if ($horsLDAP){
            $app::$cmpl["withJQuery"]=true;
            echo $app::$viewer->render('bureauRHE/savedHorsLDAP.html.twig', ['P' => $personne]);    
        } else {
            $app::$router->redirect("/{$app::$appName}/personne/{$personne->id}");
        }
    }


    public static function addPersonne($uid){
        $app = \TDS\App::get();
    


        // Il faudra peut-être modifier cela si on veut rendre le truc comme
        // il faut pour que cela permette de modifier les trucs d'une personne
        if ($uid!="0"){
            $personne = $app::loadFromUid($uid);
            if (!is_null($personne)){
                $app::$pub->info[] = "L'uid {$uid} est déjà présent dans TS online";
                $app::$router->redirect("/{$app::$appName}/personne/{$personne->id}");
                exit();
            }    
        }

        $rep = self::getDataFromLDAP($uid);

        $personneNS = $app::NS("Personne");
        $personne = new $personneNS();
        $personne->uid = $uid;
            
        if (isset($rep[$uid]->supannempid)){
            $personne->ose = $rep[$uid]->supannempid;
        } else {
            if ($uid != '0'){
                $app::$pub->info[] = "On a pas trouvé de code OSE dans le LDAP pour l'uid {$uid}";
            }
        }

        $personne->nom = $rep[$uid]->sn;
        $personne->prenom = $rep[$uid]->givenname;
        $personne->email = $rep[$uid]->mail;

        $laboNS = $app::NS('Labo');
        $order = $laboNS::entityDef['nom']['dbName'];
        $laboList = $laboNS::loadWhere("actif", [ $order ]);

        $statutNS = $app::NS('Statut');
        $order = $statutNS::entityDef['nom']['dbName'];
        $statutList = $statutNS::loadWhere("actif", [ $order ]);

        $domaineNS = $app::NS('Domaine');
        $order = $domaineNS::entityDef['nom']['dbName'];
        $domaineList = $domaineNS::loadWhere("actif", [ $order ]);


        $app::$cmpl["withJQuery"]=true;
        echo $app::$viewer->render('bureauRHE/addPersonne.html.twig', ['uid' =>$uid, 'P' => $personne, 'statutL' => $statutList, 'laboL' => $laboList, 'domaineL' => $domaineList]);

        exit();
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

        echo $app::$viewer->render('bureauRHE/searchLDAP.html.twig', ['ldapList' => $rep, 'search' => ['uid' => $uid, 'name' => $name ] ]);
    }


    public static function listePersonnesParStatut($id){
        $app = \TDS\App::get();

        $S = $app::NS('Statut')::load($id);
        $PL = $app::NS('Personne')::loadWhere("actif and statut = {$id}");

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('bureauRHE/listePersonnesParStatut.html.twig', ['PL' => $PL, 'S' => $S]) ;
    }


    public static function listeStatuts(){
        $app = \TDS\App::get();

        $statutNS = $app::NS('Statut');
        $statutList = $statutNS::loadWhere('actif', ['nom']);

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;

        echo $app::$viewer->render('bureauRHE/listeStatuts.html.twig', ['SL' => $statutList ]);
    }



    public function createEnseignement(){
        echo "<h3>création d'une fiche enseignement</h3>";
        echo "<p>En cours de réalisation...</p>";
        exit();

    }

    public function createPersonne(){
        // echo "<h3>création d'une ficher personne</h3>";
        // echo "<p>En cours de réalisation...</p>";
        // exit();

        self::searchLDAP();
    }

}

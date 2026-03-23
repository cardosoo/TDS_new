<?php
namespace foire\Controllers;

class PersonneController extends \base\Controllers\PersonneController {

    public static function getOptions($id){
        $app = \TDS\App::get();

        $options = parent::getOptions($id);
        $options['visuPanier'] =  $options['canEdit']  || $app::$auth->isAdmin;
        return $options;
    }

    public static function fiche($id){
        $app = \TDS\App::get();
        $options = self::getOptions($id);

        $appName = $app::$appName;
        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl['withDataTables'] = true; 
        $app::$cmpl['withKnockout'] = true;
        $app::$toCRUD="/{$appName}/CRUD/Personne/{$id}";
        echo $app::$viewer->render("personne/fiche.html.twig", $options);
    }


    public static function ajaxPanier(){
        $app = \TDS\App::get();

        $id = filter_input(\INPUT_POST, 'id', \FILTER_VALIDATE_INT);
        $P = $app::NS('Personne')::load($id);
        echo $app::$viewer->render('personne/ajaxPanier.html.twig', ['Panier' => $P->panierList, 'P' => $P ]);        
    }

    public static function searchLDAPNumetu(){

        //echo "Je suis dans \\foire\\PersonneControlleur::searchLDAPNumetu";
        //exit();

        $app = \TDS\App::get();

        $quoi = filter_input(INPUT_POST, 'searchValue', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $quoi = trim($quoi);


        if (empty($quoi)){
            echo '';//json_encode([]);
            exit();
        }

        $filter = "(supannEtuId={$quoi})";        
        
        $rep = null;
        $ldap = new \TDS\LDAPExtern();
        $rep = $ldap->list($filter, ['uid','displayName', 'supannEtuEtape'], 1);
        $rep = $ldap->reformat($rep);

        if (count($rep) != 1){
            echo '';//json_encode([]);
            exit();
        }

        $r = reset($rep);
        $etapeL = is_array( $r->supannetuetape ) ? $r->supannetuetape : [ $r->supannetuetape ];
        
        $etapeList = [];
        
        foreach($etapeL as $et){
            $etapeList[] = (object)["code" => preg_replace('/ *{[^)]*} */', "", $et)];
        }

        // il faut ensuite ensuite faire la recherche dans le fichier etapes.csv avec l'étape qui va bien pour voir si tout va bien !
        // il faut faire attention au fait que l'étudiant peuvent être inscrits à plusieurs étapes (comment choisir la bonne ?)
        $filepath = $app::$pathList['plus']."/etapes.csv";

        $r->etape = "";
        $r->nom = "";
        $r->level = "Niveau";
        
        foreach($etapeList as $k => $et){
            $find = str_replace( ['-', '_'], [ '?', '?'], $et->code);
            $find = str_replace("?", "[-_]", $find);
            $find = explode("[", $find)[0];
//echo $find."\n";
            $cmd = "grep -e {$find} {$filepath}";
            $lignes = `{$cmd}`;
            $lignes = $str=preg_replace("@\n@","",$lignes);;
            $etapeList[$k]->lignes = $lignes;
//var_dump(['find' =>$find, 'lignes'=> $lignes]);                
            if (  (! is_null($lignes)) && (! $lignes=='') ){
                $l = explode(';', $lignes);
                if ( (in_array($l[3], ['L3', 'M1'])) || ($r->level=='Niveau') ){ // Pour faire en sorte de retenir les étapes en L3 ou en M1 seulement
                    $r->etape = $l[1];
                    $r->nom = $l[2];
                    $r->level = $l[3];    
                }
            }
        }
        echo json_encode($r);
//        echo json_encode($etapeList);
        exit();
    }

    public static function saveStages($id){
        $app = \TDS\App::get();
        
        // on récupère la fonctionRef des stages la personne considérée
        $SL = $app::NS('personne_foncRef')::loadWhere("actif and foncref = 4 and personne = {$id}");

        // on vérifie qu'il n'y a qu'une seule fonctionRef de stage. Il devrait y en avoir au plus 1
        if (count($SL)>1){
            echo "Il y a plusieurs entrées pour le ref... c'est un problème !";
            exit();
        } 
        // si c'est le premier stage alors on crée la structure fonctionRef qui va bien
        $new = count($SL)!=1;
        if (!$new){
            $S = $SL[0];
        } else {
            $frNS = $app::NS('personne_foncRef');
            $S = new $frNS;
            $S->personne = $id;
            $S->foncref = 4;
            $new = true;
        }
        // d contient les données qui ont été transmises
        // à savoir la liste des stages qu'on met dans $stages et le décompte du volume
        $d = json_decode($_POST['d']);
        $stages = $d->stages;
        $S->volume = strval($d->volume);
        $withDocument = [];

        // on parcourt chaque stage.transmis et on regarde si il comporte un fichier attaché !
        foreach($stages as $key => $stage){
            if (isset($stage->fileData->dataURL)){
                $tmp = explode(',', $stage->fileData->dataURL, 2); 
                $data = isset($tmp[1])?base64_decode($tmp[1]):"";
                $tmp = explode(';',$tmp[0]);
                $tmp = explode(':',$tmp[0]);
                $mimetype = $tmp[1];
                $dirName = $app::$pathList['plus']."/{$app::$appName}/Conventions/{$app::$currentYear}/{$id}";
                if (! is_dir($dirName)){
                    mkdir( $dirName, 0777, true);
                }
                file_put_contents($dirName."/".$stage->num,$data);
                $stages[$key]->file = $mimetype;
                $stages[$key]->withDocument = true; // new
                $withDocument[] = $stage->num;
            }
            unset($stages[$key]->fileData);
        }


        if (is_null($stages)){ // Je ne suis pas sur de comprendre comment cela fonctionne, mais il y a peut-être un truc à faire pour supprimer les conventions associées
            if (!$new ){
                $S->delete();
            }
            echo "success"; 
            exit(); 
        }
        $stagesJSON = json_encode($stages);
        $S->commentaire = $stagesJSON;
    
        $S->save();
        echo "success\n";
        foreach($withDocument as $with){
            echo $with."\n";
        }

    }

    public static function loadConventions($year, $id, $num){
        $app = \TDS\App::get();

        $SL = $app::NS('personne_foncRef')::loadWhere("actif and foncref = 4 and personne = {$id}");
        foreach($SL as $S){
            $stageL = json_decode($S->commentaire);
            foreach($stageL as $stage){
                if ($stage->num == $num){
                    if (isset($stage->file)){
                        $mime = $stage->file;
                        $ext =  array_search($mime, \TDS\Document::$mimeType);
                        if ($ext === false) $ext='txt';
    
                        header('Content-type: '.$mime);
                        header("Content-Disposition: inline; filename=\"convention{$stage->nom}.{$ext}\"");
                
                        echo file_get_contents($app::$pathList['plus']."/{$app::$appName}/Conventions/{$year}/{$id}/{$num}");
                        exit();
                    }
                }
            }
        }
        echo "Il y a comme un problème";
    }


}

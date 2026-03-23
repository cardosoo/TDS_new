<?php

namespace tssdv\Controllers;
use \tssdv\FicheECUEDocument as FicheECUEDocument;
use \tssdv\FicheECUE as FicheECUE;

function val($st){
    return floatval(str_replace(',', '.', $st));
}

function rrmdir($src): bool {
    $dir = opendir($src);
    $return = true;
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                $return &= rrmdir($full);
            }
            else {
                $return &= unlink($full);
            }
        }
    }
    closedir($dir);
    $return &= rmdir($src);
    return $return;
}

class FicheECUEController extends \TDS\Controller {

    public static function ficheECUE($uid, $name){

        $app = \TDS\App::get();

        $ficheECUE = new FicheECUE($uid, $name);

        $isPOST = !is_null(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        if (!$isPOST){
            $update = false;
        } else {
            $update = true;

            $ficheECUE->data = ['uid' => $uid, 'name' => $name];

            $ficheECUE->data['noteVersion'] = self::input('noteVersion');
            // informations personnelles
            $ficheECUE->data['diplome'] = self::input('diplome');
            $ficheECUE->data['mention'] = self::input('mention');
            $ficheECUE->data['parcours'] = self::input('parcours');
            $ficheECUE->data['intituleECUE'] = self::input('intituleECUE');
            $ficheECUE->data['codeECUE'] = self::input('codeECUE');
            $ficheECUE->data['codeUE'] = self::input('codeUE');
            $ficheECUE->data['ECTS'] = val(self::input('ECTS'));
            $ficheECUE->data['semestre'] = self::input('semestre');
            $ficheECUE->data['typeECUE'] = self::input('typeECUE');
            $ficheECUE->data['responsables'] = self::input('responsables');
            $ficheECUE->data['langue'] = self::input('langue');
            $ficheECUE->data['natureCours'] = self::input('natureCours');
            $ficheECUE->data['domaine1'] = self::input('domaine1');
            $ficheECUE->data['quotite1'] = val(self::input('quotite1'));
            $ficheECUE->data['domaine2'] = self::input('domaine2');
            $ficheECUE->data['quotite2'] = 100-$ficheECUE->data['quotite1'];

            // MutualisficheECUE->tion au sein de SDV (sans doute à remplacer par un tableau)
            $ficheECUE->data['mentionMutualisationSDV'] = self::input('mentionMutualisationSDV');
            $ficheECUE->data['parcoursMutualisationSDV'] = self::input('parcoursMutualisationSDV');

            // Mutualistion en dehors de SDV (sans doute à remplacer par un tableau)
            $ficheECUE->data['mentionMutualisationUPCite'] = self::input('mentionMutualisationUPCite');
            $ficheECUE->data['parcoursMutualisationUPCite'] = self::input('parcoursMutualisationUPCite');

            // Mutualisation avec des partenaires externes
            // Il faut voir ici...

            // volume horaire présentiel étudiant
            $ficheECUE->data['hCM'] = val(self::input('hCM'));
            $ficheECUE->data['hTD'] = val(self::input('hTD'));
            $ficheECUE->data['hTP'] = val(self::input('hTP'));

            // Effectifs
            $ficheECUE->data['capaciteTotale'] = val(self::input('capaciteTotale'));
            $ficheECUE->data['capaciteSDV'] = val(self::input('capaciteSDV'));
            $ficheECUE->data['nCM'] = val(self::input('nCM'));
            $ficheECUE->data['nTD'] = val(self::input('nTD'));
            $ficheECUE->data['nTP'] = val(self::input('nTP'));
            
            // encadrement
            $ficheECUE->data['nEnseignantsTP'] = val(self::input('nEnseignantsTP'));     
            $ficheECUE->data['cout'] = $ficheECUE->data['hCM']*$ficheECUE->data['nCM'] * 1.5 
                                + $ficheECUE->data['hTD']*$ficheECUE->data['nTD'] * 1 
                                + $ficheECUE->data['hTP']*$ficheECUE->data['nTP'] * $ficheECUE->data['nEnseignantsTP']; 
                    
            $ficheECUE->data['nEC_SDV'] = val(self::input('nEC_SDV'));
            $ficheECUE->data['nEC_UPCite'] = val(self::input('nEC_UPCite'));
            $ficheECUE->data['nEC_Ext'] = val(self::input('nEC_Ext'));
            $ficheECUE->data['nEC_ATER'] = val(self::input('nEC_ATER'));
            $ficheECUE->data['nEC_DCME'] = val(self::input('nEC_DCME'));
            $ficheECUE->data['nVacataires'] = val(self::input('nVacataires'));
            $ficheECUE->data['nBenevoles'] = val(self::input('nBenevoles'));
            $ficheECUE->data['coutSDV'] = val(self::input('coutSDV'));
            $ficheECUE->data['commentaires'] = self::input('commentaires');

            // MCC session Unique
            $ficheECUE->data['sessionUnique'] = !is_null(filter_input(INPUT_POST, 'sessionUnique')); // case à cocher             
            $ficheECUE->data['ratioCET'] = val(self::input('ratioCET')); // 0 signifie CC 1 signifie ET
            $ficheECUE->data['typeET'] = self::input('typeET');
            // MCC S1
            $ficheECUE->data['ratioCETS1'] = val(self::input('ratioCETS1')); // 0 signifie CC 1 signifie ET
            $ficheECUE->data['typeETS1'] = self::input('typeETS1');
            // MCC S2
            $ficheECUE->data['ratioCETS2'] = val(self::input('ratioCETS2')); // 0 signifie CC 1 signifie ET
            $ficheECUE->data['typeETS2'] = self::input('typeETS2');

            $ficheECUE->data['prerequis'] = self::input('prerequis');
            $ficheECUE->data['syllabus'] = self::input('syllabus');
            $ficheECUE->data['connaissances'] = self::input('connaissances');
            $ficheECUE->data['competences'] = self::input('competences');

            $ficheECUE->save();
            echo "success";
            exit();
        }

        $date = new \DateTime();
        $fmt = new \IntlDateFormatter(
            'fr-FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN
        );

        $dateFmt = $fmt->format($date);
        $docList = self::getDocumentList($uid, $name);
        

        $domaineList = $app::NS('Domaine')::loadWhere("actif and not (nom like '%hors SDV') ");


        $app::$cmpl["withJQuery"]=true;
        if ($update){
            $app::$pub->info[]="Les données ont été enregistrées le {$dateFmt}";
        }

        $app::$cmpl['withCKEditor'] = true;

        echo $app::$viewer->render('ficheECUE/ficheECUEForm.html.twig', [
            'uid' => $uid, 
            'name' => $name, 
            'data'=> $ficheECUE->data, 
            'docList' => $docList, 
            'domaineList' => $domaineList
        ]);
        exit();
           
    }

    private static function input($var, $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS){
        $tmp = filter_input(INPUT_POST, $var, $filter);
        if (is_null($tmp)) {return $tmp;}
        return str_replace("&#039;", "'", html_entity_decode($tmp));
    } 

    public static function getDocumentList($uid, $name){

        $dir = FicheECUE::getDir($uid, $name)."/Docs/*";
        $glob = glob($dir);
        $dList = [];
        foreach($glob as $g){
            if (!is_dir($g)){
                $dList[]=['basename' => basename($g), 't' => filemtime($g)];
            }
        }
        krsort($dList);
        
        $docList = [];
        foreach($dList as $d){
            $docList[] = new FicheECUEDocument($uid, $name, $d['basename'], $d['t']);
        }
        return $docList;
    }


    public static function docUpload($uid, $name){

        $app = \TDS\App::get();

        if (! isset($_FILES['file'])){
            echo json_encode([
                'error' => true,
                'message' => "Problème à l'upload (Fichier trop volumineux ?).",
            ]);
            exit();
        }


        if ($_FILES['file']['error'] !== 0 ){
            echo json_encode([
                'error' => true,
                'message' => "erreur {$_FILES['file']['error']} lors de l'upload.",
            ]);
            exit();
        }

        $dir = FicheECUE::getDir($uid, $name)."/Docs";
        if (!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        $filename = basename($_FILES['file']['name']);
        $path_parts = pathinfo($filename);
        $title = $path_parts['filename'];
        $ext = strtolower($path_parts['extension']);

        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            echo json_encode([
                'error' => true,
                'message' => "Upload pas bon.",
            ]);
            exit();            
        }

        $filepath = "{$dir}/{$_FILES['file']['name']}";

        move_uploaded_file($_FILES['file']['tmp_name'], $filepath);
        $doc = new FicheECUEDocument($uid, $name, $filename, time());

        echo json_encode([
            'error' => false,
            'doc' => $doc,
            'url' => $doc->getDocDownloadURL(),
            'title' => $title,
        ]);

        exit();
    }




    //public static function getDoc($uid, $filename){
    public static function getDoc($hex){

        $app = \TDS\App::get();

        $doc = $app::simpleDecrypt($hex);
        $document = new FicheECUEDocument($doc->uid, $doc->name, $doc->filename, $doc->timestamp);
        $document->download();
    }

    public static function renameDoc($hex){
        $app = \TDS\App::get();
        $doc = $app::simpleDecrypt($hex);
        $document = new FicheECUEDocument($doc->uid, $doc->name, $doc->filename, $doc->timestamp);
        $document->rename($_POST['title']);
        echo $document->getDocDownloadURL();
        //$app::doLog('Fin du truc on echo ça :'.$document->getDocDownloadURL());
    }

    public static function deleteDoc($hex){

        $app = \TDS\App::get();

        $doc = $app::simpleDecrypt($hex);

        $document = new FicheECUEDocument($doc->uid, $doc->name, $doc->filename, $doc->timestamp);
        $document->delete();
        echo "Done";
    }

    public static function delete($name){
        $app = \TDS\App::get();
        $uid = $app::$auth->uid;

        $rootDir = FicheECUE::getDir($uid);
        if (str_starts_with($name, '.')){
            $name=substr($name, 1);
            $done = rename("{$rootDir}.{$name}", "{$rootDir}{$name}");
        } else  {
            $done = rename("{$rootDir}{$name}", "{$rootDir}.{$name}");
        }
        echo $done?"Done":"Il y a un problème...";
    }

    public static function duplicate($name){
        $app = \TDS\App::get();
        $uid = $app::$auth->uid;

        $oldFicheECUE = new FicheECUE($uid, $name);
        $name = date("m_d_Y-h_i_s");
        $ficheECUE = new FicheECUE($uid, $name);
        $ficheECUE->data = json_decode(json_encode($oldFicheECUE->data));
        $ficheECUE->data->intituleECUE = "Copie de ".$oldFicheECUE->data->intituleECUE ;
        $ficheECUE->data->noteVersion = join("\n", ["Copier depuis ".$oldFicheECUE->data->intituleECUE, $ficheECUE->data->noteVersion]);

        $ficheECUE->save() ;

        echo "Done";
    }


    public static function destroy($name){
        $app = \TDS\App::get();
        $uid = $app::$auth->uid;

        $rootDir = FicheECUE::getDir($uid);
        if (str_starts_with($name, '.')){
            $done = rrmdir("{$rootDir}{$name}" );
            if (!$done){
                var_dump($done);
                echo "C'est le rrmdir qui pose problème: {$rootDir}{$name}";
            }
        } else  {
            $done=false;
        }
        echo $done?"Done":"Il y a un problème...";

    }

    public static function gestion(){
        $app = \TDS\App::get();
        $uid = $app::$auth->uid;

        $rootDir = FicheECUE::getDir($uid);
        $glob = glob("{$rootDir}".'{.[!.],}*', GLOB_BRACE);

        $ficheList = [];
        $ficheDeletedList = [];
        foreach($glob as $g){
            $t = explode('/', $g);
            $name = end($t);
            if (str_starts_with($name, '.')){
                $ficheDeletedList[$name] = new FicheECUE($uid, $name);
            } else {
                $ficheList[$name] = new FicheECUE($uid, $name);
            }
        }

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;
        echo $app::$viewer->render('ficheECUE/gestion.html.twig', ['FL' => $ficheList, 'FDL' => $ficheDeletedList]);
    }

    public static function liste(){
        $app = \TDS\App::get();
        $uid = $app::$auth->uid;

        $rootDir = FicheECUE::getDir('');
        $g1 = glob("{$rootDir}".'{.[!.],}*', GLOB_BRACE);
        $ficheList = [];
        foreach($g1 as $g2){
            $t = explode('/', $g2);
            $uid = end($t);

            $rootDir = FicheECUE::getDir($uid);
            $glob = glob("{$rootDir}".'{.[!.],}*', GLOB_BRACE);
            $fL = [];             
            foreach($glob as $g){
                $t = explode('/', $g);
                $name = end($t);
                if (str_starts_with($name, '.')){
                    //$ficheDeletedList[$name] = new FicheECUE($uid, $name);
                } else {
                    $fL[$name] = new FicheECUE($uid, $name);
                }
            }
            $P = $app::NS('Personne')::loadOneWhere("actif and uid='{$uid}'");

            $ficheList["{$P->prenom} {$P->nom}"]= $fL;
        }

        $app::$cmpl["withDataTables"]=true;
        $app::$cmpl['withJQuery'] = true;
        echo $app::$viewer->render('ficheECUE/liste.html.twig', ['FL' => $ficheList]);

    }
}


<?php

namespace base\Controllers;

class CandidatureController extends \TDS\Controller {

    
    public static function getDir($uid=null){
        $app = \TDS\App::get();

        $rootDir = "{$app::$pathList['plus']}/{$app::$appName}/candidatureME/{$app::$currentYear}";
        
        if (is_null($uid)){
            return $rootDir;
        }
        return "{$rootDir}/{$uid}";
    }


    public static function candidatureME($uid=null, $previousemail=false){
        $app = \TDS\App::get();
        
        
        if (! $app::$auth->isAdmin){
            var_dump("Nous ne sommes pas près... un peu de patience, il n'y en a plus pour très longtemps");
            exit();
        }
        

        if (!is_null($uid)){
            self::candidatureME2($uid);           
        }

        $email = $previousemail!==false?null:filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        
        if (is_null($email)){ // c'est le premier envoi et n'y a pas de email
            echo $app::$viewer->render('candidature/candidature.html.twig', ['email' => $previousemail]);
            exit();
        }

        $uid = uniqid('code', true);
        if ($email === filter_var($email, FILTER_VALIDATE_EMAIL)){  // l'adresse email envoyée est valide

            $url = $app::getPageURL();
            $uid = uniqid('code', true);
            $dir = self::getDir($uid);
            mkdir($dir, 0777, true );
            $data = json_encode(["email" => $email]);
            file_put_contents("{$dir}/data.dat", $data);
//            $url = $_SERVER['SCRIPT_URI'];
            $nextYear = $app::$currentYear+1;
            $message = "
Bonjour,<br>
<br>
Vous venez d'ouvrir un dossier de candidature à une mission d'enseignement à l'UFR de physique de l'université Paris Cité 
pour l'année universitaire {$app::$currentYear} - {$nextYear} <br>
En suivant [ce lien]({$url}/{$uid}), vous pourrez compléter ou modifier votre dossier.<br>
<b>Conservez ce message précieusement !</b>
<br>
Bien à vous,<br>
";

            \base\Controllers\MailerController::sendOneMail($email, '', null, $message, "[candidatureME{$app::$currentYear}]", false, $app::$mail);
            echo $app::$viewer->render('candidature/mailOuvertureCandidature.html.twig', ['email' => $email]);
            exit();
        } else {
            $app::$pub->warning[]= "l'adresse {$email} n'est pas valide";
            $_POST=[];
            self::candidatureME(null, $email);
            exit();
        }
    }


    private static function input($var, $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS){
        $tmp = filter_input(INPUT_POST, $var, $filter);
        return str_replace("&#039;", "'", html_entity_decode($tmp));
    } 

    public static function candidatureME2($uid = null){
        $app = \TDS\App::get();
        $dir = self::getDir($uid);
        if (!is_dir($dir)){
            var_dump("On ne devrait pas arriver là !");
            exit();
        }


        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if ( ($email === false) or is_null($email) ){
            $update = false;
            $data = json_decode(file_get_contents("{$dir}/data.dat"));
        } else {
            $update = true;
            $data = ['uid' => $uid];
            // informations personnelles
            $data['email'] = $email;
            $data['nom'] = self::input('nom');
            $data['prenom'] = self::input('prenom');
            $data['tel'] = self::input('tel');
            // informations administratives
            $data['anneeDoctorat'] = self::input('anneeDoctorat');
            $data['ecoleDoctorale'] = self::input('ecoleDoctorale');
            $data['EDAutre'] = self::input('EDAutre');
            $data['etablissement'] = self::input('etablissement');
            $data['EtabAutre'] = self::input('EtabAutre');
            // fincancement
            $data['origineFinancement'] = self::input('origineFinancement');
            $data['OFAutre'] = self::input('OFAutre');
            $data['organismeFinanceur'] = self::input('organismeFinanceur');
            $data['OF2Autre'] = self::input('OF2Autre');
            $data['etablissementEmployeur'] = self::input('etablissementEmployeur');
            $data['debutCD']  = self::input('debutCD');
            // thèse
            $data['SR']  = self::input('SR');
            $data['nom_lab']  = self::input('nom_lab');
            $data['adresse_lab']  = self::input('adresse_lab');
            $data['nom_DT']  = self::input('nom_DT');
            $data['mail_DT']  = self::input('mail_DT');
            $data['cotutetelle']  = self::input('cotutetelle');
            $data['sujet']  = self::input('sujet');
            // autres
            $data['autreMission'] = self::input('autreMission');
            $data['ouiAutreEtab'] = self::input('ouiAutreEtab');
            $data['nonAutreEtab'] = self::input('nonAutreEtab');
            $data['composante'] = self::input('composante');
            $data['comment']  = self::input('comment');
        }
        file_put_contents("{$dir}/data.dat", json_encode($data));
        $date = new \DateTime();
        $fmt = new \IntlDateFormatter(
            'fr-FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN
        );
        $dateFmt = $fmt->format($date);

        $docList = self::getDocumentList($uid);

        $app::$cmpl["withJQuery"]=true;
        if ($update){
            $app::$pub->info[]="Les données ont été enregistrées le {$dateFmt}";
        }
        echo $app::$viewer->render('candidature/candidatureForm.html.twig', ['uid' => $uid, 'data'=> $data, 'docList' => $docList]);
        exit();
    }


    public static function getDocumentList($uid){

        $dir = self::getDir($uid)."/Docs/*";
        $glob = glob($dir);
        // var_dump($glob);

        $dList = [];
        foreach($glob as $g){
            if (!is_dir($g)){
                $dList[]=['basename' => basename($g), 't' => filemtime($g)];
            }
        }
        krsort($dList);
        
        $docList = [];
        foreach($dList as $d){
            $docList[] = new \base\Document($uid, 0, $d['basename'], $d['t']);
        }

        // var_dump($docList);
        return $docList;
    }


    public static function docUpload($uid){
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

        $dir = self::getDir($uid)."/Docs";
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
        $doc = new \base\Document($uid, 0, $filename, time());
        //$url ="/{$app::$appName}/candidatureME/getDoc/{$uid}/{$filename}";

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
        $document = new \base\Document($doc->className, $doc->id, $doc->filename, $doc->timestamp);
        $document->download();
    }

    public static function renameDoc($hex){
        $app = \TDS\App::get();

        $doc = $app::simpleDecrypt($hex);

        $document = new \base\Document($doc->className, $doc->id, $doc->filename, $doc->timestamp);
        $document->rename($_POST['title']);
        echo $document->getDocDownloadURL();
    }

    public static function deleteDoc($hex){
        $app = \TDS\App::get();

        $doc = $app::simpleDecrypt($hex);

        $document = new \base\Document($doc->className, $doc->id, $doc->filename, $doc->timestamp);
        $document->delete();
        echo "Done";
    }


    public static function liste(){
        $app = \TDS\App::get();

        $rootDir = self::getDir();
        $glob = glob("{$rootDir}/*");

        $liste = [];
        foreach($glob as $g){
            $uid = basename($g);
            $d = json_decode(file_get_contents("{$g}/data.dat"));
            $d->uid = $uid;

            $d->docList = self::getDocumentList($uid);
            $liste[$uid] = $d;
        }

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withDataTables"]=true;
        echo $app::$viewer->render('candidature/liste.html.twig', ['liste'=> $liste]);
    }
}


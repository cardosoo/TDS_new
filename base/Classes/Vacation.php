<?php

namespace base;

use stdClass;
use base\Controllers\VacationController;

class Vacation {

    public string|null $id = null;
    public string|null $dir = null;
    public object|null $data = null;


    public static function getBaseDir(){
        $app = \TDS\App::get();
        $year = \base\App::$vacationYear;
        return "{$app::$pathList['plus']}/{$app::$appName}/vacation/{$year}";
    }

    public  function buildDir(){
        $baseDir = self::getBaseDir($this->id);
        $date = date("Y-m-d-H-i-s");
        $dir = "{$baseDir}/{$this->data->uid}_{$this->data->codeECUE}_{$this->data->etat}_{$date}_{$this->id}";
        return $dir;
    }

    // renvoie le dossier existant correspondant à l'id fournit
    public static function getDir(string $id): string {
        $baseDir = self::getBaseDir();

        $dirList = glob("{$baseDir}/*_{$id}", GLOB_ONLYDIR);
        if (count($dirList)==0){ // il n'y a pas de répertoire qui va bien
            return "{$baseDir}/UID_ECUE_CREATION_DATE_{$id}";
        }
        if (count($dirList)==1){
            return "{$dirList[0]}";
        }
        var_dump("Il y a un problème ici...");
        var_dump($dirList);
        exit();
    }

    public function load() {
        $this->data = json_decode(file_get_contents("{$this->dir}/data.json"));
    }

    public function save(){
        $oldDir = self::getDir($this->id);
        $newDir = $this->buildDir();

        rename($oldDir, $newDir);

        $this->dir = $newDir;
        $this->data->modificationDate = date("Y-m-d-H-i-s");
        file_put_contents("{$this->dir}/data.json", json_encode($this->data));
    }

    public function __construct(string|null $id = null){
        $app = \base\App::get();
        if (is_null($id)){
            $id = uniqid(date("Y-m-d-H-i-s"), true);
        }
        $this->id = $id;
        $this->dir = self::getDir($id);

        if (! file_exists($this->dir)){ // si le dossier n'existe pas le créer si on a les droits qui vont bien
            if (! $app::$auth->hasRole('Gestionnaire')){
                echo $app::$viewer->render('error404.html.twig');
                exit();
            }
            mkdir($this->dir, 0777, true );
        }

        if (! file_exists("{$this->dir}/data.json")){ // si le fichier n'existe pas le créer
            $this->data = (object)[
                'etat' => 'CREATION', // CREATION / EDITION / VALIDATED / ARCHIVED / DELETED
                'id' => $this->id,
                'inDB' => false, 
                'codeECUE' => '',
                "nom" => '',
                "prenom" => '',
                'niveau' => '',
                'hCM' => 0,
                'hCMTD' => 0,
                'hTD' => 0,
                'hTP' => 0,
                'typeFormation' => '',
                'formation' => '',
                'semestre' => '',
                'dateDebut' => '2024/09/01',
                'dateFin' => '2025/06/30',
                'intituleEnseignement' => '',
                'gestionnaireScol' => '',
                'uid' => '', // uid de la personne qui valide l'enseignement
                'signature' => '',
                'commentaire' => '',
                'EOTP' => 'T1',
                ];
            $this->save();
        }
        $this->load();
    }

    public function delete(){
        $this->data->etat = 'DELETED';
        $this->save();
    }

    public function deletePermanent(){
        return \base\App::rrmdir ($this->dir);
    }

    public static function getVacationFromEcueCode($code){
        $baseDir = self::getBaseDir();
        $dirList = glob("{$baseDir}/*_{$code}_*", GLOB_ONLYDIR);
        $res = [];
        foreach($dirList as $dir){
            $data = json_decode(file_get_contents("{$dir}/data.json"));
            $data->hEQTD = $data->hCM*1.5+$data->hCMTD*1.25+$data->hTD*1+$data->hTP*1;
            $res[] = $data; 
        }
        return $res;
    }


    public  function getPDFName(){
        $year = \base\App::$vacationYear;

        $fname="validation{$year}_{$this->data->niveau}_{$this->data->formation}_{$this->data->nom}_{$this->data->prenom}_{$this->data->codeECUE}.pdf";
        return $fname;
    }

    public function buildPDF(){
        $app = \TDS\App::get();

        $year = \base\App::$vacationYear;
        $P = $app::NS('Personne')::loadOneWhere("actif and uid='{$this->data->uid}'");
        $struct = new Struct($year);
        $E = $struct->getECUEByCode($this->data->codeECUE);
        // $codeEOTP =  $E->getEtape()->getEOTP() ?? '_____________________'; 
        //$this->data->EOTP = $codeEOTP;
        
        $options = [
            'D' => $this->data,
            'P' => $P,
            'E' => $E,
            'year' => $year,
        ];

        $html = $app::$viewer->render('vacation/buildPDF.html.twig', $options);
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor('Foire');
        $yp = $year+1;
        $pdf->setTitle("UFR de physique - année universitaire {$year}-{$yp}");
        $pdf->setSubject("Déclaration HC et vacations, CEV,ATV, intervenants occasionnels");
        $pdf->setKeywords('');

        // set default header data
        $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 
                "UFR de physique - année universitaire {$year}-{$yp}",
                "Déclaration HC et vacations, CEV,ATV, intervenants occasionnels");

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        //$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setImageScale(2.5);

        // set some language-dependent strings (optional)
        $l = Array();
        $l['a_meta_charset'] = 'UTF-8';
        $l['a_meta_dir'] = 'ltr';
        $l['a_meta_language'] = 'fr';
        $l['w_page'] = 'page';            
        $pdf->setLanguageArray($l);

        // set font
        $pdf->setFont('dejavusans', '', 10);
        // add a page
        $pdf->AddPage();

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        // reset pointer to the last page

        $pdf->lastPage();
        
        $fname=$this->getPDFName();

        array_map('unlink', glob("{$this->dir}/*.pdf"));
        $pdf->Output("{$this->dir}/{$fname}", 'FI');
    }

    public function downloadPDF(){
        $fname=$this->getPDFName();
        header("Content-type:application/pdf");
        header("Content-Disposition:attachment;filename=\"{$fname}\"");
        readfile("{$this->dir}/{$fname}");
    }

    public function getPDFFullName(){
        $fname=$this->getPDFName();
        return "{$this->dir}/{$fname}";
    }

    public function archive(){
        $this->data->etat = 'ARCHIVED';
        $this->save();
    }

    public function dearchive(){
        $this->data->etat = 'VALIDATED';
        $this->save();
    }

    public function devalide(){
        $this->data->etat = 'EDITION';
        $this->save();
    }

}
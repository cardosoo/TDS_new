<?php

namespace tssdv;

use \PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet as Worksheet;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

use ECUEQuery;


function normaliser($str) {
    $str = $str??"";
    $str = mb_strtolower($str, 'UTF-8');
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = preg_replace('/[^a-z0-9 ]/', '', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    return trim($str);
}


class ImportCCC{
    
    public Spreadsheet $newSpreadsheet;
    public Worksheet $newSheet;
    public XlsxReader $reader;
    public string $inputFilename;
    public string $sheetName;
    public int $newRow;
    public \base\FindUser $fu;
    public Worksheet $sheet;
    public array $UEdata;
    public array $ECUEdata;
    public array $header = [];
    public \base\Struct $struct;
    
    public array $goodArray = [
        'semestre' => 'Semestre',
        'nouveau code apogee' => 'Code',
        'intitule de lenseignement' => 'Intitule',
        'ects coefficient' => 'ECTS',
        'seuil de dedoublement tptd' => 'Seuil',   
        'h cm' => 'hCM',   
        'h td' => 'hTD',   
        'h cmtd' => 'hCMTD',   
        'h tp' => 'hTP',   
        'autres formes denseignement' => 'Extra',   
        'total' => 'Total',   
        'heures referentiel equiv td' => 'Ref',   
        'h equiv td' => 'hEQTD',   
        'capacite totale de lue' => 'Capacite',   
        'nombre dinscrits dans lue en n1' => 'Inscrits',   
        'nom du responsable de lue uniquement' =>'Responsables',   
        'parcours porteurvet et composante' => 'VET',   
        'nb dheure composante porteuse' => 'hComposantePoteuse',   
        'nom de lautre composante associee' => 'composanteAssociee',   
        'nb dheures equiv td composante associee' => 'hComposanteAssociee',   
        'competences visees' => 'Competences',
        'formule' => 'formule',
    ];

    public function __construct(){
        $this->newSpreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->newSheet = $this->newSpreadsheet->getActiveSheet();
        // Figer la première ligne (ligne 1)
        $this->newSheet->freezePane('E2'); // => tout au-dessus de A2 sera figé
        $this->newRow = 1; // index de la ligne dans le nouveau fichier
        $this->newSheet->fromArray($this->goodArray, null, 'C' . $this->newRow);
        $respHeader = ['id1', 'Nom1','id2', 'Nom2', 'id3', 'Nom3', 'id4', 'Nom4'];
        $this->newSheet->fromArray($respHeader, null, 'AA' . $this->newRow);
        $dbHeader = ['id', 'Nom','hCM', 'hTD', 'hCTD', 'hTP', 'hExtra'];
        $this->newSheet->fromArray($dbHeader, null, 'AJ' . $this->newRow);
        $this->fu = new \base\FindUser();
        $this->struct = new \base\Struct();

    }
    
    public function rowToArray($row){
        $max = count($this->header);
        // Récupérer toutes les cellules de la ligne
        $rowData = [];
        $i=0;

        foreach ($row->getCellIterator() as $cell) {
            $rowData[$this->header[$i]] = $cell->getCalculatedValue();
            if ($this->header[$i] == 'h equiv td'){
                $rowData['formule'] =  "'".$cell->getValue();
            }

            $i++;
            if ($i >= $max)  return $rowData;
        }
        return $rowData;
    }



    /**
     * Renvoie la cellule effective (maîtresse si fusionnée) pour une coordonnée donnée
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param string $coord e.g. 'B1'
     * @return \PhpOffice\PhpSpreadsheet\Cell\Cell
     */

    function getMasterCell(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $coord) {
        $cell = $sheet->getCell($coord);

        if ($cell->isInMergeRange()) {
            foreach ($sheet->getMergeCells() as $mergedRange) {
                if (\PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateIsInsideRange($mergedRange, $coord)) {
                    $start = explode(':', $mergedRange)[0];
                    return $sheet->getCell($start);
                }
            }
        }
        return $cell;
    }

    public function processHeader($row){
        $this->header = [];

        $debug = \str_starts_with($this->sheet->getCell("D" . $row->getRowIndex())->getValue(),'_');
        var_dump($this->sheet->getCell("D" . $row->getRowIndex())->getValue()." "."D" . $row->getRowIndex());

        $i = 0;
        foreach ($row->getCellIterator() as $cell) {
            $masterCell = $this->getMasterCell($this->sheet, $cell->getCoordinate());
            $colorObj = $masterCell->getStyle()->getFill()->getStartColor();
            $rgb = $colorObj->getRGB() ?: $colorObj->getARGB();
            if ( empty($rgb) || ($rgb == 'FFFFFF' )){
                break;
            }
            $val = $masterCell->getValue();
            $this->header[$i] = normaliser($val);
            $i++;
        }
    }
    
    public function arrayToGoodArray($array){
        $goodArray = [];
        //var_dump($this->goodArray);
        foreach($this->goodArray as $key => $val){
            //var_dump($key, $val);
            $goodArray[$val] = $array[$key]??null;
        }
        return $goodArray;
    }

    public function processUE($row){
        $this->UEdata = $this->arrayToGoodArray(self::rowToArray($row));
    }


    public function processECUE1($row){
        $ECUEdata = $this->arrayToGoodArray(self::rowToArray($row));
        if (!empty($this->UEdata)){
            if (empty($ECUEdata['ECTS'])) $ECUEdata['ECTS'] = $this->UEdata['ECTS'];
            if (empty($ECUEdata['Competences'])) $ECUEdata['Competences'] = $this->UEdata['Competences'];
            if (empty($ECUEdata['Responsables'])) $ECUEdata['Responsables'] = $this->UEdata['Responsables'];
            if (empty($ECUEdata['Seuil'])) $ECUEdata['Seuil'] = $this->UEdata['Seuil'];
            if (empty($ECUEdata['Capacite'])) $ECUEdata['Capacite'] = $this->UEdata['Capacite'];
            if (empty($ECUEdata['Inscrits'])) $ECUEdata['Inscrits'] = $this->UEdata['Inscrits'];
        }
        if (empty($ECUEdata['Seuil'])) $ECUEdata['Seuil'] = 32;

        // if (!empty($ECUEdata['Seuil'])){
        //     $ECUEdata['Seuil'] = preg_replace('/\D+/', '/', $ECUEdata['Seuil']);
        // }

        $this->newRow++;
        $this->newSheet->fromArray([ basename($this->inputFilename), $this->sheetName], null, 'A' . $this->newRow);
        $this->newSheet->fromArray($ECUEdata, null, 'C' . $this->newRow);
        return $ECUEdata;
    }

    public function processECUE2($ECUEdata){
        $app = \TDS\App::get();

        if (empty($ECUEdata['Responsables'])) return ;
        //$stList = explode('/', $ECUEdata['Responsables']);
        
        $texte = $ECUEdata['Responsables']??"";
        $texte = preg_replace('/(?:^|\b)([A-Z])\./u', '$1', " ".$texte);

        $stList = preg_split('/\s*(?:,|\/|\s-\s|\set\s|\s&\s|\.|\n|;)\s*/i', $texte);
        $rList = [];
        foreach($stList as $st){
            $id = $this->fu->bestPersonneId($st);
            if ($id === false){
                $rList[]=0;
                $rList[]='???';
            } else {
                $P = $app::NS('Personne')::load($id);
                $rList[]=$id;
                $rList[]="{$P->prenom} {$P->nom}";
            }
        }
        // var_dump([
        //     's' => $ECUEdata['Responsables'],
        //     'f' => $stList,
        //     'r' => $rList,
        // ]);
        $this->newSheet->fromArray($rList, null, 'AA' . $this->newRow);
    }

    public function processECUE3($ECUEdata){
        $app = \TDS\App::get();

        $code = $ECUEdata['Code'];
        /*
        $ecue = EcueQuery::create()
            ->filterByCode($code)
            ->findOne();
        */
        $E = $app::NS('Enseignement')::loadOneWhere("actif and code ='{$code}'");

        if (is_null($E)) return; 
        $rList = [$E->id, $E->nom, $E->d_cm, $E->d_td, $E->d_ctd, $E->d_tp, $E->d_extra];
        $this->newSheet->fromArray($rList, null, 'AJ' . $this->newRow);
    }

    public function processECUE($row){
        $app = \TDS\App::get();
        $ECUdata = $this->processECUE1($row);
        $this->processECUE2($ECUdata);
        $this->processECUE3($ECUdata);

    }

    public function processRow($row){
        // if ($this->sheet->getCell('B' . $row->getRowIndex())->getValue() == 'GN0BU015'){
        //     var_dump($this->header);
        // }
        $toKeep = false;
        $cellC = $this->sheet->getCell('C' . $row->getRowIndex())->getValue();
        if (is_null($cellC)){
            $this->UEdata = [];
            return;
        }
        if (in_array($cellC,['Type'])){
            $this->processHeader($row);
            //var_dump($this->header);
        }
        if (in_array($cellC,['UE', 'STAGE']) || str_starts_with($cellC, 'UE') ){
            $toKeep = true;
            $this->processUE($row);
            // var_dump($this->UEdata);
        }
        if (in_array($cellC,['ECUE'])){
            $toKeep = true;
            $this->processECUE($row);
            // var_dump($this->ECUEdata);
        }
        if (!$toKeep) {
            $this->UEdata = [];
        }


    }


    public function processSheet($sheetName){
        $app = \TDS\App::get();
        $this->sheetName = $sheetName;
        $this->reader->setLoadSheetsOnly($sheetName);
        $spreadsheet = $this->reader->load($this->inputFilename);
        $this->sheet = $spreadsheet->getActiveSheet();

        $ECUEdata = [];
        foreach ($this->sheet->getRowIterator() as $row) {
            $this->processRow($row);
        }
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    public function processFile($inputFilename){
        $this->inputFilename = $inputFilename;
        $this->reader = new XlsxReader();
        $this->reader->setReadDataOnly(false);
        $sheetNames = $this->reader->listWorksheetNames($this->inputFilename);
        foreach($sheetNames as $sheetName){
            if (str_contains($sheetName, 'Maquette')){ 
                $this->processSheet($sheetName);
            }
        }
    }

    public function saveSpreadsheet($outputFilename){
        $writer = new XlsxWriter($this->newSpreadsheet);
        $writer->save($outputFilename);
    }

}

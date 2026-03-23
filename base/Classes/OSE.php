<?php
namespace base;

use \GuzzleHttp\Client;
use \GuzzleHttp\RequestOptions;
use \TDS\App;

function dos2unix($s) {
    $s = str_replace("\r\n", "\n", $s);
    $s = str_replace("\r", "\n", $s);
    $s = preg_replace("/\n{2,}/", "\n\n", $s);
    return $s;
}


function csvstring_to_array($string, $separatorChar = ';', $enclosureChar = '"', $newlineChar = PHP_EOL) {
    // @author: Klemen Nagode
    $string = dos2unix($string);
    $array = array();
    $size = strlen($string);
    $columnIndex = 0;
    $rowIndex = 0;
    $fieldValue="";
    $isEnclosured = false;
    for($i=0; $i<$size;$i++) {

        // $char = $string{$i};
        $char = $string[$i];
        $addChar = "";

        if($isEnclosured) {
            if($char==$enclosureChar) {

                //if($i+1<$size && $string{$i+1}==$enclosureChar){
                if($i+1<$size && $string[$i+1]==$enclosureChar){
                    // escaped char
                    $addChar=$char;
                    $i++; // dont check next char
                }else{
                    $isEnclosured = false;
                }
            }else {
                $addChar=$char;
            }
        } else {
            if($char==$enclosureChar) {
                $isEnclosured = true;
            }else {

                if($char==$separatorChar) {

                    $array[$rowIndex][$columnIndex] = $fieldValue;
                    $fieldValue="";

                    $columnIndex++;
                }elseif($char==$newlineChar) {
                    echo $char;
                    $array[$rowIndex][$columnIndex] = $fieldValue;
                    $fieldValue="";
                    $columnIndex=0;
                    $rowIndex++;
                }else {
                    $addChar=$char;
                }
            }
        }
        if($addChar!=""){
            $fieldValue.=$addChar;
        }
    }

    if($fieldValue) { // save last field
        $array[$rowIndex][$columnIndex] = $fieldValue;
    }
    return $array;
}

class OSE  {

    protected $codePath = ''; // tous les codesOSE 
    protected $servicePath = ''; // listing des service
    public $detailsPath = ''; // details des enseignements issus de OSE

    protected $msdPath = '';  // modification du service du
    protected String|App $app;
    protected ?array $service = null;
    protected ?array $headers = null;
    protected $serviceLine = 0;

    public function __construct($year=null){
        $this->app = \TDS\App::get();

        if (is_null($year)){
            $year = $this->app::$currentYear;
        }

        $plus = $this->app::$pathList['plus'];
        $plusApp = "{$plus}/{$this->app::$appName}";
        $this->codePath = "{$plus}/OSE/{$year}/codeOSE.csv";
        $this->servicePath = "{$plusApp}/OSE/{$year}/listingServices.csv";
        $this->detailsPath = "{$plusApp}/OSE/{$year}/details.csv";
        $this->msdPath = "{$plusApp}/OSE/{$year}/MSD.csv";
        $this->headers = [];
    
        // lecture de la première ligne des services 
        if (! file_exists($this->servicePath)){return; }
        $serviceFile = fopen($this->servicePath, "r");
        $line = fgets($serviceFile);
        fclose($serviceFile);
        $line = dos2unix($line);
        $headers = csvstring_to_array($line, ';', '"', "\n")[0];
        $this->headers = [];
        $index = 0;
        foreach($headers as $header){
            $this->headers[$header]=$index;
            $index++;
        }
    }

    private function readService(){
        $st = file_get_contents($this->servicePath);
        $st = dos2unix($st);
        return csvstring_to_array($st, ';', '"', "\n") ;
    }

    private function getFromServiceLine($l, $col){
        if (!isset($this->headers[$col])) {return null; }
        return $l[$this->headers[$col]];
    } 

    private function getNumFromServiceLine($l, $col){
        $val = $this->getFromServiceLine($l,$col);
        if (is_null($val)) {return 0;}
        return self::num($val);       
    }

    private function convertFromServiceLine($l){
        $t=[];
        $t['ose'] = $l[4];
        $t['personne'] = $l[5];
        $t['composante'] = $l[15];
        $t['etape'] = $l[20];
        $t['cursus'] = $l[21];
        $t['semestre'] = $l[30];
        $t['ecue'] = $l[22];
        $t['intitule'] = $l[23];
        $t['CM']= $this->getNumFromServiceLine($l, 'CM');
        $t['TD']= $this->getNumFromServiceLine($l, 'TD');
        $t['TP']= $this->getNumFromServiceLine($l, 'TP');
        $t['TP7']= $this->getNumFromServiceLine($l, 'TP7');
        $t['MD']= $this->getNumFromServiceLine($l, 'MD');
        $t['CMTD7']= $this->getNumFromServiceLine($l, 'CMTD7');
        $t['CMTD']= $this->getNumFromServiceLine($l, 'CMTD');
        $t['Référentiel']= $this->getNumFromServiceLine($l, 'Référentiel');
        $t['hETD']= $this->getNumFromServiceLine($l, 'Total HETD');
        return $t;
    }

    public function readNextService(){

        if ($this->service === null){
            //$this->serviceFile = fopen($this->servicePath, "r");
            $this->service = $this->readService();
            $this->serviceLine = 0;
            $l = array_shift($this->service); // pour sauter la ligne avec les headers
        }
        if (! $this->service) {return false; }

        if (count($this->service) == 0){ return false; }
        $l = array_shift($this->service);
        $t= $this->convertFromServiceLine($l);
        return $t;
    }

    
    /**
     * findECUE 
     * 
     * permet de récupérer les informations d'une ECUE à partir des données du fichier codeOSE.csv
     * 
     * @param string $ecue
     * @return array|null
     * 
     */
    public function findECUE(String $ecue):array|null{
//        var_dump("grep {$ecue} {$this->codePath}");
        $rep = `grep {$ecue} {$this->codePath}`;
//var_dump($rep);        
        if (is_null($rep)){ $rep=""; }        
        // var_dump(['codePath' => $this->codePath, 'ecue' => $ecue, 'rep' => $rep]);
        $csv = str_getcsv($rep, ";", "\"");
        
        $rep = null;
        if (count($csv)>10){
            $rep = [ 
                'composante' => $csv[0],
                'etape' => $csv[1],
                'cursus' => $csv[2],
                'ecue' => $csv[4],
                'intitule' => $csv[5],
                'semestre' => $csv[8],
                'effectif' => intval($csv[13])+intval($csv[14])+intval($csv[15]),
                'CM' => $csv[16],
                'gCM' => $csv[17],
                'TD' => $csv[18],
                'gTD' => $csv[19],
                'TP' => $csv[20],
                'gTP' => $csv[21],
            ];
        }
        return $rep;
    }

    /**
     * getEquipe
     * 
     * Permet de récupérer les différentes lignes du fichier listingService.csv
     * afin de récupérer les équipes qui participent à cet enseignement
     * 
     * @param string $ecue
     * @return array
     */
    public function getEquipe(string $ecue):array{
        $rep = `grep {$ecue} {$this->servicePath}`;
        if (is_null($rep)){ $rep=""; }
        $lineList = explode("\n", $rep);
        $equipe = [];
        foreach($lineList as $line){
            if (!empty($line)){
                $l = str_getcsv($line, ";", "\"");
                $t = $this->convertFromServiceLine($l);
                $equipe[$t['ose']]=$t;
            }
        }
        return $equipe;
    }

    /**
     * getService
     * 
     * Permet de récupérer les différentes lignes du fichier listingService.csv
     * afin de récupérer les différents enseignements de l'enseignant
     * 
     * @param string $codeOSE
     * @return array
     */
    public function getService(String $codeOSE):array{

        $rep = `grep {$codeOSE} {$this->servicePath}`;
        if (is_null($rep)){ $rep=""; }
        $lineList = explode("\n", $rep);
        $service = [];
        foreach($lineList as $line){
            if (!empty($line)){
                $l = str_getcsv($line, ";", "\"");
                $t = $this->convertFromServiceLine($l);
                $service[$t['ecue']]=$t;
            }
        }
        return $service; 
    }



    /**
     * getDetails
     * 
     * Permet de récupérer les détails d'un enseignement à partir du fichier details.csv
     * 
     * @param string $ecue
     * @return array|null
     */
    public function getDetails(string $ecue):array|null{
        $rep = `grep {$ecue} {$this->detailsPath}`;

        if (is_null($rep)){
            return null;
        }
        $res = explode("\n",$rep)[0];
        $tab = explode(";", $res, 3);
        if (! isset($tab[2])){ return null; }
        return json_decode($tab[2], true);
    }


    private static function num($st){
        return \floatval(str_replace(',','.', $st));
    }

    /**
     * Renvoie un tableau qui contient les différentes lignes depuis le tableau des services OSE 
     * qui correspondent au voeu en question. Il peut y en avoir plusieurs lorsque le code UE correspond 
     * à plusieurs enseignements ce qui est le cas en particulier en MEEF
     * dans ce cas de figure, je ne sais pas très bien quoi faire de la comparaison
     * 
     * @param string $codeOSE
     * @param string $ecue
     * @return array
     */
    public function getVoeu(string $codeOSE, string $ecue):array{
        
        $rep =  `grep {$codeOSE} {$this->servicePath} | grep {$ecue}`;
        if (is_null($rep)){return []; }

        $lineList = explode("\n", $rep);
        $voeuList = [];
        foreach($lineList as $line){
            if (!empty($line)){
                $l = str_getcsv($line, ";", "\"");
                $t = $this->convertFromServiceLine($l);
                $voeuList[]=$t;
            }
        }
        return $voeuList;
    }

}

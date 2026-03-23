<?php
namespace base\Model;

use \TDS\ManyToMany;

class Voeu extends ManyToMany implements \Model\_Voeu_interface_ {
    use \Model\_Voeu_;

    const __LEFT__ = "personne";
    const __RIGHT__ = "enseignement";


    private static function equal($a, $b){
        return abs($a-$b)<0.1;
    }

    /******
     * Effectue la comparaison entre un voeu de la base de données
     * et ce que l'on trouve dans OSE.
     * 
     * renvoie true si la comparaison est exacte 
     * sinon renvoi un tableau contenant les différentes choses trouvées dans OSE 
     * concerant le voeu en question 
     * 
     */
    public function compareWithOSE(){
        
        $oseNS = (\TDS\App::$appName)."\OSE";
        $ose = new $oseNS;

        $match = false;
        
        $codeOSE = $this->__get('personne')->ose;
        $ecueList = explode('|',$this->__get('enseignement')->enseignement_structure->ecue); 

        $f = [];

        foreach($ecueList as $ecue){
            $fromOSE = $ose->getVoeu($codeOSE, $ecue);
            
            foreach($fromOSE as $OSE){
                $heures = $this->__get('voeu_detail_heures');
                if (
                         self::equal($OSE['CM'], $heures->cm)
                    && ( self::equal($OSE['CMTD'], $heures->ctd) || self::equal($OSE['CMTD7'], $heures->ctd) )
                    &&   self::equal($OSE['TD'], $heures->td)
                    && ( self::equal($OSE['TP'], $heures->tp) || self::equal($OSE['TP7'], $heures->tp) )
                    && ( self::equal($OSE['MD'], $heures->extra) || self::equal($OSE['MD'], $heures->bonus) )
                ) {
                    $match = true;
                }
            }
            $f[] = $fromOSE;
        }

        if ($match) {
            return true;
        }

        return $f;
    }

    public function compareGlobalWithOse(){
        $oseNS = (\TDS\App::$appName)."\OSE";
        $ose = new $oseNS;

        $match = false;
        
        $codeOSE = $this->__get('personne')->ose;
        $ecueList = explode('|',$this->__get('enseignement')->enseignement_structure->ecue); 

        $f = [];

        foreach($ecueList as $ecue){
            $fromOSE = $ose->getVoeu($codeOSE, $ecue);
            
            foreach($fromOSE as $OSE){
                $bilan = $this->__get('voeu_bilan_ligne');

                if (self::equal($OSE['hETD'], $bilan->heures)){
                    $match = true;
                }
            }
            $f[] = $fromOSE;
        }

        if ($match) {
            return true;
        }
        return $f;
    }

}
<?php
namespace base\Model;

use \TDS\Table;

class Etape extends Table implements \Model\_Etape_interface_ {
    use \Model\_Etape_;

    function getGenericWithLink(){
        $gen = parent::getGeneric();
        $gen2 = $this->__get('diplome')->maquette->getGenericWithLink();
        return "{$gen} <span class='searchMark'>maquette</span> {$gen2}";
    }

    function deleteWithCascade(){
        foreach($this->__get("semestreList") as $semestre){
            $semestre->deleteWithCascade();
        }
        $this->delete();
    }

    /**
     * Renvoie la liste des ecue qui compose l'étape
     * 
     */
    public function getEcueList(){
        $ecueList = [];
        
        foreach($this->__get('semestreList') as $semestre ){
            foreach($semestre->ueList as $ue){
                foreach($ue->ecueList as $ecue){
                    $ecueList[]=$ecue;
                }
            }
        }
        return $ecueList;
    }


    /**
     * Permet de renvoyer le cout en hETD pour une étape
     * en s'appuyant sur le calcul du coût pour les ECUE 
     * qui la compose 
     */

    function getCout(){
        $app = \TDS\App::get();

        $cout = 0;
        foreach($this->getEcueList() as $ecue){
            $cout += $ecue->getCout();
        }
        return $cout;
    }


    function getHetu(){
        $app = \TDS\App::get();

        $shCM = 0;
        $shCTD = 0;
        $shTD = 0;
        $shTP = 0;

        foreach($this->getEcueList() as $ecue){
            list($hCM, $hCTD, $hTD, $hTP) = $ecue->getHetu();
            $shCM += $hCM;
            $shCTD += $hCTD;
            $shTD += $hTD;
            $shTP += $hTP;
        }
        return [$shCM, $shCTD, $shTD, $shTP];
        
    }    
}        
        
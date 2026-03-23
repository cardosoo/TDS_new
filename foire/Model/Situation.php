<?php
namespace foire\Model;

class Situation extends \base\Model\Situation implements \Model\_Situation_interface_ {
    use \Model\_Situation_;


    function setReductionEffective(){
        $app=\TDS\App::get();
        
        $reduc = floatval(substr($this->reduction_legale, 0,-1)); // le numéraire de la réduction légale
        $ls= substr($this->reduction_legale,-1);         // dernier caractère de la réduction légale 'h' ou '%'
        $sl=192;                                        // service légal
        $su= $app::$chargeUFR;                          // service de référence UFR

        if ($ls == '%') {
            $reduc = round($reduc*$sl/100);
        }
        if ($this->ufr) {                               // si la réductioin légale est une réduction ufr alors pas d'ajustement
            $this->reduction = $reduc;
            return;
        }
        if ($reduc==$sl) {                              // Cela c'est pour les décharges à 100% qui font exceptions à la règle
            $this->reduction = $su; 
            return ;
        }
        $dif = ($sl-$su)/2;                             // Ici c'est la cuisine qui permet de passer de la réduction légale à la réducion UFR
        $sb = $su- $dif;
        $alpha = 1-$reduc/$sl;
        $sr = round($sb*$alpha+$dif);
        if ( ($sr<64) && ($sr>1)){
            $sr=64;
        }
        $this->reduction =  round($su-$sr);
    }

}
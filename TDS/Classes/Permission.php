<?php

namespace TDS;

use \base\Model\Personne;

class Permission {

    /* 
        propList est indexé par les propriétés que l'on veut test comme par exemple 'canAddEnseignement'
        les valeurs associées sont stockées dans un tableau et sont soit des chaines de caractères correspondant à un rôle particulier
        soit des handlers vers des fonction (méthodes) qui indiquent en retour par un booléen si l'utisateur est autorisé par la fonction.
        pour les foncti


        
    */
    private array $propList = [];

    public function setProperty(string $prop, array $value){
        $this->propList[$prop] = $value;        
    }

    public function appendProperty(string $prop, array $value){
        if (!isset($this->propList[$prop])){
            $value = array_unique(array_merge($value, $this->propList[$prop]));
        }
        $this->setProperty($prop, $value);
    }

    /*
        vérifie simplement que l'utilisateur possède l'un des roles permettant d'accéder à la fonction
    */
    public function allowed(string $prop, Personne|null $personne = null){
        $app = \TDS\App::get();

        if (is_null($personne)){
            $personne = $app::$auth->user;
        }

        

        $roleList = $personne->getRoleListArray();
        return array_intersect($roleList, $this->propList[$prop]);
    }

}
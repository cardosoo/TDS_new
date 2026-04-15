<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _personne_referentiel_heures_.php
* created : Thu, 26 Mar 2026 16:11:45 +0100 UTC
*/ 

interface _personne_referentiel_heures_interface_ {
    const dbName = 'personne_referentiel_heures';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité personne_referentiel_heures
    const entityDef = [

        'heures' => [
            'dbName' => 'heures',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'heures',
            'mode' => 'raw',
            ],

    ]; 
}

trait _personne_referentiel_heures_ {
    protected int $id;            
    protected float $heures = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _personne_charge_.php
* created : Mon, 06 Apr 2026 10:58:56 +0200 UTC
*/ 

interface _personne_charge_interface_ {
    const dbName = 'personne_charge';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité personne_charge
    const entityDef = [

        'charge' => [
            'dbName' => 'charge',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'charge',
            'mode' => 'raw',
            ],

    ]; 
}

trait _personne_charge_ {
    protected int $id;            
    protected float $charge = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

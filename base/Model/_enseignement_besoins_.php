<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _enseignement_besoins_.php
* created : Sat, 12 Apr 2025 22:55:31 +0200 UTC
*/ 

interface _enseignement_besoins_interface_ {
    const dbName = 'enseignement_besoins';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité enseignement_besoins
    const entityDef = [

        'besoins' => [
            'dbName' => 'besoins',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'besoins',
            'mode' => 'raw',
            ],

    ]; 
}

trait _enseignement_besoins_ {
    protected int $id;            
    protected float $besoins = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

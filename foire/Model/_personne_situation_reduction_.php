<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _personne_situation_reduction_.php
* created : Fri, 02 May 2025 23:33:41 +0200 UTC
*/ 

interface _personne_situation_reduction_interface_ {
    const dbName = 'personne_situation_reduction';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité personne_situation_reduction
    const entityDef = [

        'reduction' => [
            'dbName' => 'reduction',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'reduction',
            'mode' => 'raw',
            ],

    ]; 
}

trait _personne_situation_reduction_ {
    protected int $id;            
    protected int $reduction = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

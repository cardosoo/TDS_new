<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _enseignement_periode_.php
* created : Mon, 06 Apr 2026 11:00:33 +0200 UTC
*/ 

interface _enseignement_periode_interface_ {
    const dbName = 'enseignement_periode';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité enseignement_periode
    const entityDef = [

        'periode' => [
            'dbName' => 'periode',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'periode',
            'mode' => 'raw',
            ],

    ]; 
}

trait _enseignement_periode_ {
    protected int $id;            
    protected string $periode = '';

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

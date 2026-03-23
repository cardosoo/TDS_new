<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _TypeUE_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
*/ 

interface _TypeUE_interface_ {
    const dbName = 'typeue';
    const idName = 'id';
    const SEARCH = array (
  0 => 'nom',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité TypeUE
    const entityDef = [

        'actif' => [
            'dbName' => 'actif',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => TRUE,
            'nullable' => TRUE,
            'twigName' => 'actif',
            'mode' => 'raw',
            ],

        'nom' => [
            'dbName' => 'nom',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'nom',
            'mode' => 'raw',
            ],

        'enseignementList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\tssdv\Model\Enseignement',
            'mappedBy' => 'typeue',
            'inversedBy' => 'enseignementList',
            'twigName' => 'typeue',
        ],

    ]; 
}

trait _TypeUE_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nom = '';
    protected array $enseignementList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

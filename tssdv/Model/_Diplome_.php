<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Diplome_.php
* created : Mon, 06 Apr 2026 10:58:56 +0200 UTC
*/ 

interface _Diplome_interface_ {
    const dbName = 'diplome';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = array (
  0 => 'code',
  1 => 'nom',
);
    const ORDER = array (
  0 => 'ordre',
);

    // les définitions de l'entité Diplome
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

        'ordre' => [
            'dbName' => 'ordre',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'ordre',
            'mode' => 'raw',
            ],

        'code' => [
            'dbName' => 'code',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'code',
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

        'maquette' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Maquette',
            'mappedBy' => 'maquette',
            'inversedBy' => 'diplomeList',
            'twigName' => 'maquette',
            'dbName' => 'maquette', 
        ],    

        'etapeList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\tssdv\Model\Etape',
            'mappedBy' => 'diplome',
            'inversedBy' => 'etapeList',
            'twigName' => 'diplome',
        ],

    ]; 
}

trait _Diplome_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected int $ordre = 0;
    protected string $code = '';
    protected string $nom = '';
    protected ?\tssdv\Model\Maquette $maquette;
    protected int $__maquette;
    protected array $etapeList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

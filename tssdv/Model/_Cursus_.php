<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Cursus_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
*/ 

interface _Cursus_interface_ {
    const dbName = 'cursus';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = array (
  0 => 'nom',
);
    const ORDER = array (
  0 => 'nom',
);

    // les définitions de l'entité Cursus
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

        'intitule' => [
            'dbName' => 'intitule',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'intitule',
            'mode' => 'raw',
            ],

        'structure_enseignement' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\tssdv\Model\structure_enseignement',
            'dbName' => 'structure_enseignement',
            'twigName' => 'structure_enseignement',
            'targetId' => 'cursus',
        ],    

        'etapeList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\tssdv\Model\Etape',
            'mappedBy' => 'cursus',
            'inversedBy' => 'etapeList',
            'twigName' => 'cursus',
        ],

    ]; 
}

trait _Cursus_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nom = '';
    protected string $intitule = '';
    protected ?\tssdv\Model\structure_enseignement $structure_enseignement;
    protected array $etapeList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

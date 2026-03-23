<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _UE_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
*/ 

interface _UE_interface_ {
    const dbName = 'ue';
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

    // les définitions de l'entité UE
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

        'peretu' => [
            'dbName' => 'peretu',
            'type' => Field::INT,
            'size' => 100,
            'default' => 100,
            'nullable' => TRUE,
            'twigName' => 'peretu',
            'mode' => 'raw',
            ],

        'ects' => [
            'dbName' => 'ects',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'ects',
            'mode' => 'raw',
            ],

        'semestre' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Semestre',
            'mappedBy' => 'semestre',
            'inversedBy' => 'ueList',
            'twigName' => 'semestre',
            'dbName' => 'semestre', 
        ],    

        'structure_enseignement' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\tssdv\Model\structure_enseignement',
            'dbName' => 'structure_enseignement',
            'twigName' => 'structure_enseignement',
            'targetId' => 'ue',
        ],    

        'ecueList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\tssdv\Model\ECUE',
            'mappedBy' => 'ue',
            'inversedBy' => 'ecueList',
            'twigName' => 'ue',
        ],

    ]; 
}

trait _UE_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected int $ordre = 0;
    protected string $code = '';
    protected string $nom = '';
    protected int $peretu = 100;
    protected float $ects = 0;
    protected ?\tssdv\Model\Semestre $semestre;
    protected int $__semestre;
    protected ?\tssdv\Model\structure_enseignement $structure_enseignement;
    protected array $ecueList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

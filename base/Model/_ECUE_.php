<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _ECUE_.php
* created : Sat, 12 Apr 2025 22:55:31 +0200 UTC
*/ 

interface _ECUE_interface_ {
    const dbName = 'ecue';
    const idName = 'id';
    const SEARCH = array (
  0 => 'nom',
  1 => 'code',
);
    const GENERIC = array (
  0 => 'code',
  1 => 'nom',
);
    const ORDER = array (
  0 => 'ordre',
);

    // les définitions de l'entité ECUE
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

        'ue' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\base\Model\UE',
            'mappedBy' => 'ue',
            'inversedBy' => 'ecueList',
            'twigName' => 'ue',
            'dbName' => 'ue', 
        ],    

        'enseignement' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\base\Model\Enseignement',
            'mappedBy' => 'enseignement',
            'inversedBy' => 'ecueList',
            'twigName' => 'enseignement',
            'dbName' => 'enseignement', 
        ],    

        'structure_enseignement' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\structure_enseignement',
            'dbName' => 'structure_enseignement',
            'twigName' => 'structure_enseignement',
            'targetId' => 'ecue',
        ],    

    ]; 
}

trait _ECUE_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected int $ordre = 0;
    protected string $code = '';
    protected string $nom = '';
    protected int $peretu = 100;
    protected float $ects = 0;
    protected ?\base\Model\UE $ue;
    protected int $__ue;
    protected ?\base\Model\Enseignement $enseignement;
    protected int $__enseignement;
    protected ?\base\Model\structure_enseignement $structure_enseignement;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

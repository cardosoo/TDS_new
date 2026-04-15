<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Semestre_.php
* created : Thu, 26 Mar 2026 16:11:45 +0100 UTC
*/ 

interface _Semestre_interface_ {
    const dbName = 'semestre';
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

    // les définitions de l'entité Semestre
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

        'periode' => [
            'dbName' => 'periode',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'periode',
            'mode' => 'raw',
            ],

        'etape' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Etape',
            'mappedBy' => 'etape',
            'inversedBy' => 'semestreList',
            'twigName' => 'etape',
            'dbName' => 'etape', 
        ],    

        'structure_enseignement' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\services\Model\structure_enseignement',
            'dbName' => 'structure_enseignement',
            'twigName' => 'structure_enseignement',
            'targetId' => 'semestre',
        ],    

        'ueList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\services\Model\UE',
            'mappedBy' => 'semestre',
            'inversedBy' => 'ueList',
            'twigName' => 'semestre',
        ],

    ]; 
}

trait _Semestre_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected int $ordre = 0;
    protected string $code = '';
    protected string $nom = '';
    protected int $peretu = 100;
    protected int $periode = 0;
    protected ?\services\Model\Etape $etape;
    protected int $__etape;
    protected ?\services\Model\structure_enseignement $structure_enseignement;
    protected array $ueList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

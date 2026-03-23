<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _EtapeOSE_.php
* created : Thu, 03 Apr 2025 10:32:58 +0200 UTC
*/ 

interface _EtapeOSE_interface_ {
    const dbName = 'etapeose';
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
  0 => 'code',
);

    // les définitions de l'entité EtapeOSE
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

        'nbetu' => [
            'dbName' => 'nbetu',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'nbetu',
            'mode' => 'raw',
            ],

        'etape_personneList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\base\Model\Personne',
            'joinTable' => '\base\Model\etape_personne',
            'joinColumn' => 'etapeose',
            'twigName' => 'etapeose',
            'inverseJoinColum' => 'etape_personneList',
            'isFirst' => true,
        ],

        'etape_enseignementList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\base\Model\Enseignement',
            'joinTable' => '\base\Model\etape_enseignement',
            'joinColumn' => 'etapeose',
            'twigName' => 'etapeose',
            'inverseJoinColum' => 'etape_enseignementList',
            'isFirst' => true,
        ],

    ]; 
}

trait _EtapeOSE_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $code = '';
    protected string $nom = '';
    protected int $nbetu = 0;
    protected array $etape_personneList;
    protected array $etape_enseignementList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

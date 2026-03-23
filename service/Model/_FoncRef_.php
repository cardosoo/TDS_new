<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _FoncRef_.php
* created : Fri, 13 Mar 2026 08:02:05 +0100 UTC
*/ 

interface _FoncRef_interface_ {
    const dbName = 'foncref';
    const idName = 'id';
    const SEARCH = array (
  0 => 'intitule',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité FoncRef
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

        'intitule' => [
            'dbName' => 'intitule',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'intitule',
            'mode' => 'raw',
            ],

        'referentiel' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\service\Model\Referentiel',
            'mappedBy' => 'referentiel',
            'inversedBy' => 'foncrefList',
            'twigName' => 'referentiel',
            'dbName' => 'referentiel', 
        ],    

        'personne_foncrefList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\service\Model\Personne',
            'joinTable' => '\service\Model\personne_foncRef',
            'joinColumn' => 'foncref',
            'twigName' => 'foncref',
            'inverseJoinColum' => 'personne_foncrefList',
            'isFirst' => false,
        ],

    ]; 
}

trait _FoncRef_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $intitule = '';
    protected ?\service\Model\Referentiel $referentiel;
    protected int $__referentiel;
    protected array $personne_foncrefList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

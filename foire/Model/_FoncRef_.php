<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _FoncRef_.php
* created : Fri, 02 May 2025 23:33:41 +0200 UTC
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
            'targetEntity' => '\foire\Model\Referentiel',
            'mappedBy' => 'referentiel',
            'inversedBy' => 'foncrefList',
            'twigName' => 'referentiel',
            'dbName' => 'referentiel', 
        ],    

        'personne_foncrefList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Personne',
            'joinTable' => '\foire\Model\personne_foncRef',
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
    protected ?\foire\Model\Referentiel $referentiel;
    protected int $__referentiel;
    protected array $personne_foncrefList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

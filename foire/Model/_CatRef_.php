<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _CatRef_.php
* created : Mon, 06 Apr 2026 11:00:33 +0200 UTC
*/ 

interface _CatRef_interface_ {
    const dbName = 'catref';
    const idName = 'id';
    const SEARCH = array (
  0 => 'description',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité CatRef
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

        'description' => [
            'dbName' => 'description',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'description',
            'mode' => 'raw',
            ],

        'cat1ref' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Cat1Ref',
            'mappedBy' => 'cat1ref',
            'inversedBy' => 'catrefList',
            'twigName' => 'cat1ref',
            'dbName' => 'cat1ref', 
        ],    

        'referentielList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\foire\Model\Referentiel',
            'mappedBy' => 'catref',
            'inversedBy' => 'referentielList',
            'twigName' => 'catref',
        ],

    ]; 
}

trait _CatRef_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $description = '';
    protected ?\foire\Model\Cat1Ref $cat1ref;
    protected int $__cat1ref;
    protected array $referentielList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

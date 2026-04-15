<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Heritage_.php
* created : Mon, 06 Apr 2026 11:00:33 +0200 UTC
*/ 

interface _Heritage_interface_ {
    const dbName = 'heritage';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Heritage
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

        'parent' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Enseignement',
            'mappedBy' => 'parent',
            'inversedBy' => 'Heritage_Enfants',
            'twigName' => 'enfants',
            'dbName' => 'parent', 
        ],    

        'enfant' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Enseignement',
            'mappedBy' => 'enfant',
            'inversedBy' => 'Heritage_Parents',
            'twigName' => 'parents',
            'dbName' => 'enfant', 
        ],    

    ]; 
}

trait _Heritage_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\foire\Model\Enseignement $parent;
    protected int $__parent;
    protected ?\foire\Model\Enseignement $enfant;
    protected int $__enfant;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

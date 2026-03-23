<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _etape_personne_.php
* created : Thu, 10 Apr 2025 16:00:29 +0200 UTC
*/ 

interface _etape_personne_interface_ {
    const dbName = 'etape_personne';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité etape_personne
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

        'etape_ose' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Etape_OSE',
            'mappedBy' => 'etape_ose',
            'inversedBy' => 'etape_personneList',
            'twigName' => 'etape_ose',
            'dbName' => 'etape_ose', 
        ],    

        'personne' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'etape_personneList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

    ]; 
}

trait _etape_personne_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\foire\Model\Etape_OSE $etape_ose;
    protected int $__etape_ose;
    protected ?\foire\Model\Personne $personne;
    protected int $__personne;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

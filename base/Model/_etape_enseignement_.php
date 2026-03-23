<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _etape_enseignement_.php
* created : Mon, 07 Apr 2025 15:20:41 +0200 UTC
*/ 

interface _etape_enseignement_interface_ {
    const dbName = 'etape_enseignement';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité etape_enseignement
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
            'targetEntity' => '\base\Model\Etape_OSE',
            'mappedBy' => 'etape_ose',
            'inversedBy' => 'etape_enseignementList',
            'twigName' => 'etape_ose',
            'dbName' => 'etape_ose', 
        ],    

        'enseignement' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\base\Model\Enseignement',
            'mappedBy' => 'enseignement',
            'inversedBy' => 'etape_enseignementList',
            'twigName' => 'enseignement',
            'dbName' => 'enseignement', 
        ],    

    ]; 
}

trait _etape_enseignement_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\base\Model\Etape_OSE $etape_ose;
    protected int $__etape_ose;
    protected ?\base\Model\Enseignement $enseignement;
    protected int $__enseignement;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

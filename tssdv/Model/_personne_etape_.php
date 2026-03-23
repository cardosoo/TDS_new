<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _personne_etape_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
*/ 

interface _personne_etape_interface_ {
    const dbName = 'personne_etape';
    const idName = 'id';
    const SEARCH = array (
  0 => 'code',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité personne_etape
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

        'personne' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'personne_etapeList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

    ]; 
}

trait _personne_etape_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $code = '';
    protected ?\tssdv\Model\Personne $personne;
    protected int $__personne;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

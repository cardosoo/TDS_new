<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _personne_etape_.php
* created : Sat, 12 Apr 2025 22:55:31 +0200 UTC
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
            'targetEntity' => '\base\Model\Personne',
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
    protected ?\base\Model\Personne $personne;
    protected int $__personne;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

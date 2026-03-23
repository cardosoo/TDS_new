<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _personne_foncRef_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
*/ 

interface _personne_foncRef_interface_ {
    const dbName = 'personne_foncref';
    const idName = 'id';
    const SEARCH = array (
  0 => 'commentaire',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité personne_foncRef
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

        'personne' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'personne_foncrefList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

        'foncref' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\FoncRef',
            'mappedBy' => 'foncref',
            'inversedBy' => 'personne_foncrefList',
            'twigName' => 'foncref',
            'dbName' => 'foncref', 
        ],    

        'commentaire' => [
            'dbName' => 'commentaire',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'commentaire',
            'mode' => 'raw',
            ],

        'volume' => [
            'dbName' => 'volume',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'volume',
            'mode' => 'raw',
            ],

    ]; 
}

trait _personne_foncRef_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\tssdv\Model\Personne $personne;
    protected int $__personne;
    protected ?\tssdv\Model\FoncRef $foncref;
    protected int $__foncref;
    protected string $commentaire = '';
    protected float $volume = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

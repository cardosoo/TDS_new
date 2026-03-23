<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _personne_situation_.php
* created : Fri, 02 May 2025 23:33:41 +0200 UTC
*/ 

interface _personne_situation_interface_ {
    const dbName = 'personne_situation';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = array (
  0 => 'debut',
);

    // les définitions de l'entité personne_situation
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
            'targetEntity' => '\foire\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'personne_situationList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

        'situation' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Situation',
            'mappedBy' => 'situation',
            'inversedBy' => 'personne_situationList',
            'twigName' => 'situation',
            'dbName' => 'situation', 
        ],    

        'debut' => [
            'dbName' => 'debut',
            'type' => Field::DATE,
            'size' => 100,
            'default' => 'now',
            'nullable' => TRUE,
            'twigName' => 'debut',
            'mode' => 'raw',
            ],

        'fin' => [
            'dbName' => 'fin',
            'type' => Field::DATE,
            'size' => 100,
            'default' => 'now',
            'nullable' => TRUE,
            'twigName' => 'fin',
            'mode' => 'raw',
            ],

        'reduction' => [
            'dbName' => 'reduction',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'reduction',
            'mode' => 'raw',
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

    ]; 
}

trait _personne_situation_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\foire\Model\Personne $personne;
    protected int $__personne;
    protected ?\foire\Model\Situation $situation;
    protected int $__situation;
    protected string $debut = 'now';
    protected string $fin = 'now';
    protected int $reduction = 0;
    protected string $commentaire = '';

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _domaine_personne_.php
* created : Mon, 06 Apr 2026 10:58:56 +0200 UTC
*/ 

interface _domaine_personne_interface_ {
    const dbName = 'domaine_personne';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité domaine_personne
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

        'domaine' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Domaine',
            'mappedBy' => 'domaine',
            'inversedBy' => 'domaine_personneList',
            'twigName' => 'domaine',
            'dbName' => 'domaine', 
        ],    

        'personne' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'domaine_personneList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

        'ordre' => [
            'dbName' => 'ordre',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'ordre',
            'mode' => 'raw',
            ],

        'quotite' => [
            'dbName' => 'quotite',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'quotite',
            'mode' => 'raw',
            ],

    ]; 
}

trait _domaine_personne_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\tssdv\Model\Domaine $domaine;
    protected int $__domaine;
    protected ?\tssdv\Model\Personne $personne;
    protected int $__personne;
    protected int $ordre = 0;
    protected float $quotite = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

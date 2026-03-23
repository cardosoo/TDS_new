<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _responsable_.php
* created : Fri, 13 Mar 2026 08:02:05 +0100 UTC
*/ 

interface _responsable_interface_ {
    const dbName = 'responsable';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité responsable
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

        'etape' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\service\Model\Etape',
            'mappedBy' => 'etape',
            'inversedBy' => 'responsableList',
            'twigName' => 'etape',
            'dbName' => 'etape', 
        ],    

        'personne' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\service\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'responsableList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

    ]; 
}

trait _responsable_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\service\Model\Etape $etape;
    protected int $__etape;
    protected ?\service\Model\Personne $personne;
    protected int $__personne;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

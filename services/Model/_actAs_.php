<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _actAs_.php
* created : Thu, 26 Mar 2026 16:11:45 +0100 UTC
*/ 

interface _actAs_interface_ {
    const dbName = 'actas';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité actAs
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
            'targetEntity' => '\services\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'actasList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

        'role' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Role',
            'mappedBy' => 'role',
            'inversedBy' => 'actasList',
            'twigName' => 'role',
            'dbName' => 'role', 
        ],    

    ]; 
}

trait _actAs_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\services\Model\Personne $personne;
    protected int $__personne;
    protected ?\services\Model\Role $role;
    protected int $__role;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

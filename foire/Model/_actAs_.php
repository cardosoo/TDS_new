<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _actAs_.php
* created : Fri, 02 May 2025 23:33:41 +0200 UTC
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
            'targetEntity' => '\foire\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'actasList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

        'role' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Role',
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
    protected ?\foire\Model\Personne $personne;
    protected int $__personne;
    protected ?\foire\Model\Role $role;
    protected int $__role;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

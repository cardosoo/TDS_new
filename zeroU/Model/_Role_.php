<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Role_.php
* created : Tue, 09 Nov 2021 20:50:16 +0000 UTC
*/ 

interface _Role_interface_ {
    const dbName = 'role';
    const idName = 'id';
    const SEARCH = array (
  0 => 'nom',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Role
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

        'nom' => [
            'dbName' => 'nom',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'nom',
            'mode' => 'raw',
            ],

        'actasList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\zeroU\Model\Personne',
            'joinTable' => '\zeroU\Model\actAs',
            'joinColumn' => 'role',
            'twigName' => 'role',
            'inverseJoinColum' => 'actasList',
            'isFirst' => false,
        ],

    ]; 
}

trait _Role_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nom = '';
    protected array $actasList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

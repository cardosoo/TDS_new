<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Role_.php
* created : Fri, 13 Mar 2026 08:02:05 +0100 UTC
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

        'auth' => [
            'dbName' => 'auth',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'auth',
            'mode' => 'raw',
            ],

        'actasList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\service\Model\Personne',
            'joinTable' => '\service\Model\actAs',
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
    protected string $auth = '';
    protected array $actasList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

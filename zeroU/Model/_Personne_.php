<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Personne_.php
* created : Tue, 09 Nov 2021 20:50:16 +0000 UTC
*/ 

interface _Personne_interface_ {
    const dbName = 'personne';
    const idName = 'id';
    const SEARCH = array (
  0 => 'prenom',
  1 => 'nom',
  2 => 'prenom',
);
    const GENERIC = array (
  0 => 'prenom',
  1 => 'nom',
);
    const ORDER = array (
  0 => 'nom',
);

    // les définitions de l'entité Personne
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

        'uid' => [
            'dbName' => 'uid',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'uid',
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

        'prenom' => [
            'dbName' => 'prenom',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'prenom',
            'mode' => 'raw',
            ],

        'actasList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\zeroU\Model\Role',
            'joinTable' => '\zeroU\Model\actAs',
            'joinColumn' => 'personne',
            'twigName' => 'personne',
            'inverseJoinColum' => 'actasList',
            'isFirst' => true,
        ],

    ]; 
}

trait _Personne_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $uid = '';
    protected string $nom = '';
    protected string $prenom = '';
    protected array $actasList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

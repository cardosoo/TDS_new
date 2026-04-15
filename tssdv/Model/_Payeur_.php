<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Payeur_.php
* created : Mon, 06 Apr 2026 10:58:56 +0200 UTC
*/ 

interface _Payeur_interface_ {
    const dbName = 'payeur';
    const idName = 'id';
    const SEARCH = array (
  0 => 'nom',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Payeur
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

        'enseignementList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\tssdv\Model\Enseignement',
            'mappedBy' => 'payeur',
            'inversedBy' => 'enseignementList',
            'twigName' => 'payeur',
        ],

    ]; 
}

trait _Payeur_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nom = '';
    protected array $enseignementList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

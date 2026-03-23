<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Statut_.php
* created : Sat, 12 Apr 2025 22:55:31 +0200 UTC
*/ 

interface _Statut_interface_ {
    const dbName = 'statut';
    const idName = 'id';
    const SEARCH = array (
  0 => 'nom',
);
    const GENERIC = array (
  0 => 'nom',
);
    const ORDER = array (
  0 => 'nom',
);

    // les définitions de l'entité Statut
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

        'obligation' => [
            'dbName' => 'obligation',
            'type' => Field::INT,
            'size' => 100,
            'default' => 192,
            'nullable' => TRUE,
            'twigName' => 'obligation',
            'mode' => 'raw',
            ],

        'personneList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\base\Model\Personne',
            'mappedBy' => 'statut',
            'inversedBy' => 'personneList',
            'twigName' => 'statut',
        ],

    ]; 
}

trait _Statut_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nom = '';
    protected int $obligation = 192;
    protected array $personneList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Labo_.php
* created : Sun, 22 Mar 2026 23:12:16 +0100 UTC
*/ 

interface _Labo_interface_ {
    const dbName = 'labo';
    const idName = 'id';
    const SEARCH = array (
  0 => 'acronyme',
  1 => 'nom',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Labo
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

        'acronyme' => [
            'dbName' => 'acronyme',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'acronyme',
            'mode' => 'raw',
            ],

        'url' => [
            'dbName' => 'url',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'url',
            'mode' => 'raw',
            ],

        'personneList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\services\Model\Personne',
            'mappedBy' => 'labo',
            'inversedBy' => 'personneList',
            'twigName' => 'labo',
        ],

    ]; 
}

trait _Labo_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nom = '';
    protected string $acronyme = '';
    protected string $url = '';
    protected array $personneList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Referentiel_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
*/ 

interface _Referentiel_interface_ {
    const dbName = 'referentiel';
    const idName = 'id';
    const SEARCH = array (
  0 => 'code',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Referentiel
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

        'code' => [
            'dbName' => 'code',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'code',
            'mode' => 'raw',
            ],

        'description' => [
            'dbName' => 'description',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'description',
            'mode' => 'raw',
            ],

        'calcul' => [
            'dbName' => 'calcul',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'calcul',
            'mode' => 'raw',
            ],

        'observations' => [
            'dbName' => 'observations',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'observations',
            'mode' => 'raw',
            ],

        'catref' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\CatRef',
            'mappedBy' => 'catref',
            'inversedBy' => 'referentielList',
            'twigName' => 'catref',
            'dbName' => 'catref', 
        ],    

        'foncrefList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\tssdv\Model\FoncRef',
            'mappedBy' => 'referentiel',
            'inversedBy' => 'foncrefList',
            'twigName' => 'referentiel',
        ],

    ]; 
}

trait _Referentiel_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $code = '';
    protected string $description = '';
    protected string $calcul = '';
    protected string $observations = '';
    protected ?\tssdv\Model\CatRef $catref;
    protected int $__catref;
    protected array $foncrefList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

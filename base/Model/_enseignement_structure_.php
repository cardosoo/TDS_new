<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _enseignement_structure_.php
* created : Sat, 12 Apr 2025 22:55:31 +0200 UTC
*/ 

interface _enseignement_structure_interface_ {
    const dbName = 'enseignement_structure';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité enseignement_structure
    const entityDef = [

        'periode' => [
            'dbName' => 'periode',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'periode',
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

        'ecue' => [
            'dbName' => 'ecue',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'ecue',
            'mode' => 'raw',
            ],

        'cursus' => [
            'dbName' => 'cursus',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'cursus',
            'mode' => 'raw',
            ],

        'etape' => [
            'dbName' => 'etape',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'etape',
            'mode' => 'raw',
            ],

        'maquette' => [
            'dbName' => 'maquette',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'maquette',
            'mode' => 'raw',
            ],

        'composante' => [
            'dbName' => 'composante',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'composante',
            'mode' => 'raw',
            ],

        'nbetu' => [
            'dbName' => 'nbetu',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'nbetu',
            'mode' => 'raw',
            ],

        'netu' => [
            'dbName' => 'netu',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'netu',
            'mode' => 'raw',
            ],

    ]; 
}

trait _enseignement_structure_ {
    protected int $id;            
    protected string $periode = '';
    protected string $code = '';
    protected string $ecue = '';
    protected string $cursus = '';
    protected string $etape = '';
    protected string $maquette = '';
    protected string $composante = '';
    protected string $nbetu = '';
    protected float $netu = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

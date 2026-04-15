<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _enseignement_besoins_detail_.php
* created : Mon, 06 Apr 2026 10:58:56 +0200 UTC
*/ 

interface _enseignement_besoins_detail_interface_ {
    const dbName = 'enseignement_besoins_detail';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité enseignement_besoins_detail
    const entityDef = [

        'cm' => [
            'dbName' => 'cm',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'cm',
            'mode' => 'raw',
            ],

        'ctd' => [
            'dbName' => 'ctd',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'ctd',
            'mode' => 'raw',
            ],

        'td' => [
            'dbName' => 'td',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'td',
            'mode' => 'raw',
            ],

        'tp' => [
            'dbName' => 'tp',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'tp',
            'mode' => 'raw',
            ],

        'extra' => [
            'dbName' => 'extra',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'extra',
            'mode' => 'raw',
            ],

        'bonus' => [
            'dbName' => 'bonus',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'bonus',
            'mode' => 'raw',
            ],

    ]; 
}

trait _enseignement_besoins_detail_ {
    protected int $id;            
    protected float $cm = 0;
    protected float $ctd = 0;
    protected float $td = 0;
    protected float $tp = 0;
    protected float $extra = 0;
    protected float $bonus = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _voeu_enseignement_bilan_prioritaire_.php
* created : Thu, 26 Mar 2026 16:11:45 +0100 UTC
*/ 

interface _voeu_enseignement_bilan_prioritaire_interface_ {
    const dbName = 'voeu_enseignement_bilan_prioritaire';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité voeu_enseignement_bilan_prioritaire
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

        'bonus' => [
            'dbName' => 'bonus',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'bonus',
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

        'correspondant' => [
            'dbName' => 'correspondant',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'correspondant',
            'mode' => 'raw',
            ],

        'heures' => [
            'dbName' => 'heures',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'heures',
            'mode' => 'raw',
            ],

    ]; 
}

trait _voeu_enseignement_bilan_prioritaire_ {
    protected int $id;            
    protected float $cm = 0;
    protected float $ctd = 0;
    protected float $td = 0;
    protected float $tp = 0;
    protected float $bonus = 0;
    protected float $extra = 0;
    protected bool $correspondant = FALSE;
    protected float $heures = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

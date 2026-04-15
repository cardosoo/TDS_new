<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _structure_enseignement_.php
* created : Mon, 06 Apr 2026 10:58:56 +0200 UTC
*/ 

interface _structure_enseignement_interface_ {
    const dbName = 'structure_enseignement';
    const idName = 'enseignement';
    const SEARCH = array (
  0 => 'code_ue',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité structure_enseignement
    const entityDef = [

        'periode' => [
            'dbName' => 'periode',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'periode',
            'mode' => 'raw',
            ],

        'nbetu' => [
            'dbName' => 'nbetu',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'nbetu',
            'mode' => 'raw',
            ],

        'code_ue' => [
            'dbName' => 'code_ue',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'code_ue',
            'mode' => 'raw',
            ],

        'code_ecue' => [
            'dbName' => 'code_ecue',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'code_ecue',
            'mode' => 'raw',
            ],

    ]; 
}

trait _structure_enseignement_ {
    protected int $id;            
    protected int $periode = 0;
    protected float $nbetu = 0;
    protected string $code_ue = '';
    protected string $code_ecue = '';

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

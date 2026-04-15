<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Cat1Ref_.php
* created : Mon, 06 Apr 2026 11:00:33 +0200 UTC
*/ 

interface _Cat1Ref_interface_ {
    const dbName = 'cat1ref';
    const idName = 'id';
    const SEARCH = array (
  0 => 'description',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Cat1Ref
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

        'description' => [
            'dbName' => 'description',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'description',
            'mode' => 'raw',
            ],

        'catrefList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\foire\Model\CatRef',
            'mappedBy' => 'cat1ref',
            'inversedBy' => 'catrefList',
            'twigName' => 'cat1ref',
        ],

    ]; 
}

trait _Cat1Ref_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $description = '';
    protected array $catrefList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

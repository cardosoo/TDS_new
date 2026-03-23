<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Role_.php
* created : Sun, 14 Feb 2021 19:30:59 +0100 UTC
*/ 

interface _Role_interface_ {
    const dbName = 'role';
    const idName = 'id';
    // les définitions de l'entité Role
    const entityDef = [

        'name' => [
            'dbName' => 'name',
            'type' => Field::STRING,
            'size' => 30,
            'default' => '',
            'nullable' => TRUE,
            ],

        'actasList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\zeroUP\Model\User',
            'joinTable' => '\zeroUP\Model\actAs',
            'joinColumn' => 'role',
            'inverseJoinColum' => 'actasList',
        ],

    ]; 
}

trait _Role_ {
    protected int $id;            
    protected string $name = '';
    protected array $actasList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _User_.php
* created : Sun, 14 Feb 2021 19:30:59 +0100 UTC
*/ 

interface _User_interface_ {
    const dbName = 'user';
    const idName = 'id';
    // les définitions de l'entité User
    const entityDef = [

        'uid' => [
            'dbName' => 'uid',
            'type' => Field::STRING,
            'size' => 30,
            'default' => '',
            'nullable' => TRUE,
            ],

        'name' => [
            'dbName' => 'name',
            'type' => Field::STRING,
            'size' => 30,
            'default' => '',
            'nullable' => TRUE,
            ],

        'actasList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\zeroUP\Model\Role',
            'joinTable' => '\zeroUP\Model\actAs',
            'joinColumn' => 'user',
            'inverseJoinColum' => 'actasList',
        ],

    ]; 
}

trait _User_ {
    protected int $id;            
    protected string $uid = '';
    protected string $name = '';
    protected array $actasList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

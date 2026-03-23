<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _actAs_.php
* created : Sun, 14 Feb 2021 19:30:59 +0100 UTC
*/ 

interface _actAs_interface_ {
    const dbName = 'actas';
    const idName = 'id';
    // les définitions de l'entité actAs
    const entityDef = [

        'user' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\zeroUP\Model\User',
            'mappedBy' => 'user',
            'inversedBy' => 'actasList',
            'dbName' => 'user', 
        ],    

        'role' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\zeroUP\Model\Role',
            'mappedBy' => 'role',
            'inversedBy' => 'actasList',
            'dbName' => 'role', 
        ],    

    ]; 
}

trait _actAs_ {
    protected int $id;            
    protected ?\zeroUP\Model\User $user;
    protected int $__user;
    protected ?\zeroUP\Model\Role $role;
    protected int $__role;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

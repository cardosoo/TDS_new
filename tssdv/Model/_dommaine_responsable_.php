<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _dommaine_responsable_.php
* created : Thu, 17 Jun 2021 12:00:12 +0000 UTC
*/ 

interface _dommaine_responsable_interface_ {
    const dbName = 'dommaine_responsable';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité dommaine_responsable
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

        'domaine' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Domaine',
            'mappedBy' => 'domaine',
            'inversedBy' => 'dommaine_responsableList',
            'twigName' => 'domaine',
            'dbName' => 'domaine', 
        ],    

        'responsable' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\responsable',
            'mappedBy' => 'responsable',
            'inversedBy' => 'dommaine_responsableList',
            'twigName' => 'responsable',
            'dbName' => 'responsable', 
        ],    

    ]; 
}

trait _dommaine_responsable_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\tssdv\Model\Domaine $domaine;
    protected int $__domaine;
    protected ?\tssdv\Model\responsable $responsable;
    protected int $__responsable;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

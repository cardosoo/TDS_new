<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _domaine_responsable_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
*/ 

interface _domaine_responsable_interface_ {
    const dbName = 'domaine_responsable';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = array (
);
    const ORDER = NULL;

    // les définitions de l'entité domaine_responsable
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
            'inversedBy' => 'domaine_responsableList',
            'twigName' => 'domaine',
            'dbName' => 'domaine', 
        ],    

        'responsable' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Personne',
            'mappedBy' => 'responsable',
            'inversedBy' => 'domaine_responsableList',
            'twigName' => 'responsable',
            'dbName' => 'responsable', 
        ],    

    ]; 
}

trait _domaine_responsable_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\tssdv\Model\Domaine $domaine;
    protected int $__domaine;
    protected ?\tssdv\Model\Personne $responsable;
    protected int $__responsable;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

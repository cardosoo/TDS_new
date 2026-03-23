<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Panier_.php
* created : Sun, 22 Mar 2026 23:12:16 +0100 UTC
*/ 

interface _Panier_interface_ {
    const dbName = 'panier';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Panier
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

        'personne' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'panierList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

        'enseignement' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Enseignement',
            'mappedBy' => 'enseignement',
            'inversedBy' => 'panierList',
            'twigName' => 'enseignement',
            'dbName' => 'enseignement', 
        ],    

        'cm' => [
            'dbName' => 'cm',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'cm',
            'mode' => 'raw',
            ],

        'ctd' => [
            'dbName' => 'ctd',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'ctd',
            'mode' => 'raw',
            ],

        'td' => [
            'dbName' => 'td',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'td',
            'mode' => 'raw',
            ],

        'tp' => [
            'dbName' => 'tp',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'tp',
            'mode' => 'raw',
            ],

        'commentaire' => [
            'dbName' => 'commentaire',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'commentaire',
            'mode' => 'raw',
            ],

    ]; 
}

trait _Panier_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\services\Model\Personne $personne;
    protected int $__personne;
    protected ?\services\Model\Enseignement $enseignement;
    protected int $__enseignement;
    protected bool $cm = FALSE;
    protected bool $ctd = FALSE;
    protected bool $td = FALSE;
    protected bool $tp = FALSE;
    protected string $commentaire = '';

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

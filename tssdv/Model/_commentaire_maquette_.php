<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _commentaire_maquette_.php
* created : Mon, 06 Apr 2026 10:58:56 +0200 UTC
*/ 

interface _commentaire_maquette_interface_ {
    const dbName = 'commentaire_maquette';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = array (
  0 => 'date',
);

    // les définitions de l'entité commentaire_maquette
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

        'maquette' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Maquette',
            'mappedBy' => 'maquette',
            'inversedBy' => 'commentaire_maquetteList',
            'twigName' => 'maquette',
            'dbName' => 'maquette', 
        ],    

        'auteur' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Personne',
            'mappedBy' => 'auteur',
            'inversedBy' => 'commentaire_maquetteList',
            'twigName' => 'auteur',
            'dbName' => 'auteur', 
        ],    

        'date' => [
            'dbName' => 'date',
            'type' => Field::DATE,
            'size' => 100,
            'default' => 'now',
            'nullable' => TRUE,
            'twigName' => 'date',
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

trait _commentaire_maquette_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\tssdv\Model\Maquette $maquette;
    protected int $__maquette;
    protected ?\tssdv\Model\Personne $auteur;
    protected int $__auteur;
    protected string $date = 'now';
    protected string $commentaire = '';

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

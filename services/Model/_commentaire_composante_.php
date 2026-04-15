<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _commentaire_composante_.php
* created : Thu, 26 Mar 2026 16:11:45 +0100 UTC
*/ 

interface _commentaire_composante_interface_ {
    const dbName = 'commentaire_composante';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = array (
  0 => 'date',
);

    // les définitions de l'entité commentaire_composante
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

        'composante' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Composante',
            'mappedBy' => 'composante',
            'inversedBy' => 'commentaire_composanteList',
            'twigName' => 'composante',
            'dbName' => 'composante', 
        ],    

        'auteur' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Personne',
            'mappedBy' => 'auteur',
            'inversedBy' => 'commentaire_composanteList',
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

trait _commentaire_composante_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\services\Model\Composante $composante;
    protected int $__composante;
    protected ?\services\Model\Personne $auteur;
    protected int $__auteur;
    protected string $date = 'now';
    protected string $commentaire = '';

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

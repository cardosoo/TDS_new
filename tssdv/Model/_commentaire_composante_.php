<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _commentaire_composante_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
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
            'targetEntity' => '\tssdv\Model\Composante',
            'mappedBy' => 'composante',
            'inversedBy' => 'commentaire_composanteList',
            'twigName' => 'composante',
            'dbName' => 'composante', 
        ],    

        'auteur' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Personne',
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
    protected ?\tssdv\Model\Composante $composante;
    protected int $__composante;
    protected ?\tssdv\Model\Personne $auteur;
    protected int $__auteur;
    protected string $date = 'now';
    protected string $commentaire = '';

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

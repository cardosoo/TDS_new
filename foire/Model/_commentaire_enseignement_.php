<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _commentaire_enseignement_.php
* created : Mon, 06 Apr 2026 11:00:33 +0200 UTC
*/ 

interface _commentaire_enseignement_interface_ {
    const dbName = 'commentaire_enseignement';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = array (
  0 => 'date',
);

    // les définitions de l'entité commentaire_enseignement
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

        'enseignement' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Enseignement',
            'mappedBy' => 'enseignement',
            'inversedBy' => 'commentaire_enseignementList',
            'twigName' => 'enseignement',
            'dbName' => 'enseignement', 
        ],    

        'auteur' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Personne',
            'mappedBy' => 'auteur',
            'inversedBy' => 'commentaire_enseignementList',
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

trait _commentaire_enseignement_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\foire\Model\Enseignement $enseignement;
    protected int $__enseignement;
    protected ?\foire\Model\Personne $auteur;
    protected int $__auteur;
    protected string $date = 'now';
    protected string $commentaire = '';

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

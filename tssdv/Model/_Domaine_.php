<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Domaine_.php
* created : Wed, 30 Jul 2025 18:30:52 +0200 UTC
*/ 

interface _Domaine_interface_ {
    const dbName = 'domaine';
    const idName = 'id';
    const SEARCH = array (
  0 => 'acronyme',
  1 => 'nom',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Domaine
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

        'nom' => [
            'dbName' => 'nom',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'nom',
            'mode' => 'raw',
            ],

        'acronyme' => [
            'dbName' => 'acronyme',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'acronyme',
            'mode' => 'raw',
            ],

        'domaine_personneList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\tssdv\Model\Personne',
            'joinTable' => '\tssdv\Model\domaine_personne',
            'joinColumn' => 'domaine',
            'twigName' => 'domaine',
            'inverseJoinColum' => 'domaine_personneList',
            'isFirst' => true,
        ],

        'domaine_enseignementList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\tssdv\Model\Enseignement',
            'joinTable' => '\tssdv\Model\domaine_enseignement',
            'joinColumn' => 'domaine',
            'twigName' => 'domaine',
            'inverseJoinColum' => 'domaine_enseignementList',
            'isFirst' => true,
        ],

        'domaine_responsableList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\tssdv\Model\Personne',
            'joinTable' => '\tssdv\Model\domaine_responsable',
            'joinColumn' => 'domaine',
            'twigName' => 'domaine',
            'inverseJoinColum' => 'domaine_responsableList',
            'isFirst' => true,
        ],

    ]; 
}

trait _Domaine_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nom = '';
    protected string $acronyme = '';
    protected array $domaine_personneList;
    protected array $domaine_enseignementList;
    protected array $domaine_responsableList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

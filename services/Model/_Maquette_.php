<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Maquette_.php
* created : Sun, 22 Mar 2026 23:12:16 +0100 UTC
*/ 

interface _Maquette_interface_ {
    const dbName = 'maquette';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = array (
  0 => 'code',
  1 => 'nom',
);
    const ORDER = array (
  0 => 'ordre',
);

    // les définitions de l'entité Maquette
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

        'ordre' => [
            'dbName' => 'ordre',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'ordre',
            'mode' => 'raw',
            ],

        'code' => [
            'dbName' => 'code',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'code',
            'mode' => 'raw',
            ],

        'version' => [
            'dbName' => 'version',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'version',
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

        'gestionnaire' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Personne',
            'mappedBy' => 'gestionnaire',
            'inversedBy' => 'maquetteList',
            'twigName' => 'gestionnaire',
            'dbName' => 'gestionnaire', 
        ],    

        'responsable' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Personne',
            'mappedBy' => 'responsable',
            'inversedBy' => 'respMaquetteList',
            'twigName' => 'responsable',
            'dbName' => 'responsable', 
        ],    

        'co_responsable' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Personne',
            'mappedBy' => 'co_responsable',
            'inversedBy' => 'coRespMaquetteList',
            'twigName' => 'co_responsable',
            'dbName' => 'co_responsable', 
        ],    

        'composante' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\services\Model\Composante',
            'mappedBy' => 'composante',
            'inversedBy' => 'maquetteList',
            'twigName' => 'composante',
            'dbName' => 'composante', 
        ],    

        'structure_enseignement' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\services\Model\structure_enseignement',
            'dbName' => 'structure_enseignement',
            'twigName' => 'structure_enseignement',
            'targetId' => 'maquette',
        ],    

        'diplomeList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\services\Model\Diplome',
            'mappedBy' => 'maquette',
            'inversedBy' => 'diplomeList',
            'twigName' => 'maquette',
        ],

        'commentaire_maquetteList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\services\Model\Personne',
            'joinTable' => '\services\Model\commentaire_maquette',
            'joinColumn' => 'maquette',
            'twigName' => 'maquette',
            'inverseJoinColum' => 'commentaire_maquetteList',
            'isFirst' => true,
        ],

    ]; 
}

trait _Maquette_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected int $ordre = 0;
    protected string $code = '';
    protected string $version = '';
    protected string $nom = '';
    protected ?\services\Model\Personne $gestionnaire;
    protected int $__gestionnaire;
    protected ?\services\Model\Personne $responsable;
    protected int $__responsable;
    protected ?\services\Model\Personne $co_responsable;
    protected int $__co_responsable;
    protected ?\services\Model\Composante $composante;
    protected int $__composante;
    protected ?\services\Model\structure_enseignement $structure_enseignement;
    protected array $diplomeList;
    protected array $commentaire_maquetteList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

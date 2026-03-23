<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Composante_.php
* created : Sat, 12 Apr 2025 22:55:31 +0200 UTC
*/ 

interface _Composante_interface_ {
    const dbName = 'composante';
    const idName = 'id';
    const SEARCH = array (
  0 => 'nom',
  1 => 'intitule',
);
    const GENERIC = array (
  0 => 'nom',
);
    const ORDER = array (
  0 => 'ordre',
);

    // les définitions de l'entité Composante
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

        'nom' => [
            'dbName' => 'nom',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'nom',
            'mode' => 'raw',
            ],

        'intitule' => [
            'dbName' => 'intitule',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'intitule',
            'mode' => 'raw',
            ],

        'structure_enseignement' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\structure_enseignement',
            'dbName' => 'structure_enseignement',
            'twigName' => 'structure_enseignement',
            'targetId' => 'composante',
        ],    

        'maquetteList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\base\Model\Maquette',
            'mappedBy' => 'composante',
            'inversedBy' => 'maquetteList',
            'twigName' => 'composante',
        ],

        'commentaire_composanteList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\base\Model\Personne',
            'joinTable' => '\base\Model\commentaire_composante',
            'joinColumn' => 'composante',
            'twigName' => 'composante',
            'inverseJoinColum' => 'commentaire_composanteList',
            'isFirst' => true,
        ],

    ]; 
}

trait _Composante_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected int $ordre = 0;
    protected string $nom = '';
    protected string $intitule = '';
    protected ?\base\Model\structure_enseignement $structure_enseignement;
    protected array $maquetteList;
    protected array $commentaire_composanteList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

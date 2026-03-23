<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Situation_.php
* created : Fri, 02 May 2025 23:33:41 +0200 UTC
*/ 

interface _Situation_interface_ {
    const dbName = 'situation';
    const idName = 'id';
    const SEARCH = array (
  0 => 'nom',
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Situation
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

        'ose' => [
            'dbName' => 'ose',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'ose',
            'mode' => 'raw',
            ],

        'reduction' => [
            'dbName' => 'reduction',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'reduction',
            'mode' => 'raw',
            ],

        'public' => [
            'dbName' => 'public',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'public',
            'mode' => 'raw',
            ],

        'reduction_legale' => [
            'dbName' => 'reduction_legale',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '0h',
            'nullable' => TRUE,
            'twigName' => 'reduction_legale',
            'mode' => 'raw',
            ],

        'ufr' => [
            'dbName' => 'ufr',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'ufr',
            'mode' => 'raw',
            ],

        'personneList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\foire\Model\Personne',
            'mappedBy' => 'situation',
            'inversedBy' => 'personneList',
            'twigName' => 'situation',
        ],

        'personne_situationList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Personne',
            'joinTable' => '\foire\Model\personne_situation',
            'joinColumn' => 'situation',
            'twigName' => 'situation',
            'inverseJoinColum' => 'personne_situationList',
            'isFirst' => false,
        ],

    ]; 
}

trait _Situation_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nom = '';
    protected string $ose = '';
    protected int $reduction = 0;
    protected bool $public = FALSE;
    protected string $reduction_legale = '0h';
    protected bool $ufr = FALSE;
    protected array $personneList;
    protected array $personne_situationList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

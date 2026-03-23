<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Etape_.php
* created : Fri, 02 May 2025 23:33:41 +0200 UTC
*/ 

interface _Etape_interface_ {
    const dbName = 'etape';
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

    // les définitions de l'entité Etape
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

        'nom' => [
            'dbName' => 'nom',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'nom',
            'mode' => 'raw',
            ],

        'nbetu' => [
            'dbName' => 'nbetu',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'nbetu',
            'mode' => 'raw',
            ],

        'diplome' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Diplome',
            'mappedBy' => 'diplome',
            'inversedBy' => 'etapeList',
            'twigName' => 'diplome',
            'dbName' => 'diplome', 
        ],    

        'cursus' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Cursus',
            'mappedBy' => 'cursus',
            'inversedBy' => 'etapeList',
            'twigName' => 'cursus',
            'dbName' => 'cursus', 
        ],    

        'structure_enseignement' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\foire\Model\structure_enseignement',
            'dbName' => 'structure_enseignement',
            'twigName' => 'structure_enseignement',
            'targetId' => 'etape',
        ],    

        'responsableList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Personne',
            'joinTable' => '\foire\Model\responsable',
            'joinColumn' => 'etape',
            'twigName' => 'etape',
            'inverseJoinColum' => 'responsableList',
            'isFirst' => true,
        ],

        'semestreList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\foire\Model\Semestre',
            'mappedBy' => 'etape',
            'inversedBy' => 'semestreList',
            'twigName' => 'etape',
        ],

    ]; 
}

trait _Etape_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected int $ordre = 0;
    protected string $code = '';
    protected string $nom = '';
    protected int $nbetu = 0;
    protected ?\foire\Model\Diplome $diplome;
    protected int $__diplome;
    protected ?\foire\Model\Cursus $cursus;
    protected int $__cursus;
    protected ?\foire\Model\structure_enseignement $structure_enseignement;
    protected array $responsableList;
    protected array $semestreList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

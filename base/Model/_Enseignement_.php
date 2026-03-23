<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Enseignement_.php
* created : Sat, 12 Apr 2025 22:55:31 +0200 UTC
*/ 

interface _Enseignement_interface_ {
    const dbName = 'enseignement';
    const idName = 'id';
    const SEARCH = array (
  0 => 'nom',
  1 => 'intitule',
  2 => 'nuac',
  3 => 'code',
);
    const GENERIC = array (
  0 => 'code',
  1 => 'nom',
);
    const ORDER = NULL;

    // les définitions de l'entité Enseignement
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

        'nuac' => [
            'dbName' => 'nuac',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'nuac',
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

        'variante' => [
            'dbName' => 'variante',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'variante',
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

        'attribuable' => [
            'dbName' => 'attribuable',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'attribuable',
            'mode' => 'raw',
            ],

        'cm' => [
            'dbName' => 'cm',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'cm',
            'mode' => 'raw',
            ],

        'td' => [
            'dbName' => 'td',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'td',
            'mode' => 'raw',
            ],

        'ctd' => [
            'dbName' => 'ctd',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'ctd',
            'mode' => 'raw',
            ],

        'tp' => [
            'dbName' => 'tp',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'tp',
            'mode' => 'raw',
            ],

        'extra' => [
            'dbName' => 'extra',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'extra',
            'mode' => 'raw',
            ],

        's_cm' => [
            'dbName' => 's_cm',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 's_cm',
            'mode' => 'raw',
            ],

        's_td' => [
            'dbName' => 's_td',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 's_td',
            'mode' => 'raw',
            ],

        's_ctd' => [
            'dbName' => 's_ctd',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 's_ctd',
            'mode' => 'raw',
            ],

        's_tp' => [
            'dbName' => 's_tp',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 's_tp',
            'mode' => 'raw',
            ],

        's_extra' => [
            'dbName' => 's_extra',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 's_extra',
            'mode' => 'raw',
            ],

        'i_cm' => [
            'dbName' => 'i_cm',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'i_cm',
            'mode' => 'raw',
            ],

        'i_td' => [
            'dbName' => 'i_td',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'i_td',
            'mode' => 'raw',
            ],

        'i_ctd' => [
            'dbName' => 'i_ctd',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'i_ctd',
            'mode' => 'raw',
            ],

        'i_tp' => [
            'dbName' => 'i_tp',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'i_tp',
            'mode' => 'raw',
            ],

        'i_extra' => [
            'dbName' => 'i_extra',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'i_extra',
            'mode' => 'raw',
            ],

        'd_cm' => [
            'dbName' => 'd_cm',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'd_cm',
            'mode' => 'raw',
            ],

        'd_td' => [
            'dbName' => 'd_td',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'd_td',
            'mode' => 'raw',
            ],

        'd_ctd' => [
            'dbName' => 'd_ctd',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'd_ctd',
            'mode' => 'raw',
            ],

        'd_tp' => [
            'dbName' => 'd_tp',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'd_tp',
            'mode' => 'raw',
            ],

        'd_extra' => [
            'dbName' => 'd_extra',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'd_extra',
            'mode' => 'raw',
            ],

        'n_cm' => [
            'dbName' => 'n_cm',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'n_cm',
            'mode' => 'raw',
            ],

        'n_td' => [
            'dbName' => 'n_td',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'n_td',
            'mode' => 'raw',
            ],

        'n_ctd' => [
            'dbName' => 'n_ctd',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'n_ctd',
            'mode' => 'raw',
            ],

        'n_tp' => [
            'dbName' => 'n_tp',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'n_tp',
            'mode' => 'raw',
            ],

        'n_extra' => [
            'dbName' => 'n_extra',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 1,
            'nullable' => TRUE,
            'twigName' => 'n_extra',
            'mode' => 'raw',
            ],

        'm_cm' => [
            'dbName' => 'm_cm',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'm_cm',
            'mode' => 'raw',
            ],

        'm_td' => [
            'dbName' => 'm_td',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'm_td',
            'mode' => 'raw',
            ],

        'm_ctd' => [
            'dbName' => 'm_ctd',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'm_ctd',
            'mode' => 'raw',
            ],

        'm_tp' => [
            'dbName' => 'm_tp',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'm_tp',
            'mode' => 'raw',
            ],

        'm_extra' => [
            'dbName' => 'm_extra',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'm_extra',
            'mode' => 'raw',
            ],

        'bonus' => [
            'dbName' => 'bonus',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'bonus',
            'mode' => 'raw',
            ],

        'syllabus' => [
            'dbName' => 'syllabus',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'syllabus',
            'mode' => 'html',
            ],

        'url' => [
            'dbName' => 'url',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'url',
            'mode' => 'raw',
            ],

        'typeue' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\base\Model\TypeUE',
            'mappedBy' => 'typeue',
            'inversedBy' => 'enseignementList',
            'twigName' => 'typeue',
            'dbName' => 'typeue', 
        ],    

        'payeur' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\base\Model\Payeur',
            'mappedBy' => 'payeur',
            'inversedBy' => 'enseignementList',
            'twigName' => 'payeur',
            'dbName' => 'payeur', 
        ],    

        'enseignement_besoins' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\enseignement_besoins',
            'dbName' => 'enseignement_besoins',
            'twigName' => 'enseignement_besoins',
            'targetId' => 'id',
        ],    

        'enseignement_periode' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\enseignement_periode',
            'dbName' => 'enseignement_periode',
            'twigName' => 'enseignement_periode',
            'targetId' => 'id',
        ],    

        'enseignement_structure' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\enseignement_structure',
            'dbName' => 'enseignement_structure',
            'twigName' => 'enseignement_structure',
            'targetId' => 'id',
        ],    

        'enseignement_etudiant_details' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\enseignement_etudiant_details',
            'dbName' => 'enseignement_etudiant_details',
            'twigName' => 'enseignement_etudiant_details',
            'targetId' => 'id',
        ],    

        'voeu_enseignement_bilan' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\voeu_enseignement_bilan',
            'dbName' => 'voeu_enseignement_bilan',
            'twigName' => 'voeu_enseignement_bilan',
            'targetId' => 'id',
        ],    

        'voeu_enseignement_bilan_prioritaire' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\voeu_enseignement_bilan_prioritaire',
            'dbName' => 'voeu_enseignement_bilan_prioritaire',
            'twigName' => 'voeu_enseignement_bilan_prioritaire',
            'targetId' => 'id',
        ],    

        'structure_enseignement' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\base\Model\structure_enseignement',
            'dbName' => 'structure_enseignement',
            'twigName' => 'structure_enseignement',
            'targetId' => 'enseignement',
        ],    

        'voeuList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\base\Model\Personne',
            'joinTable' => '\base\Model\Voeu',
            'joinColumn' => 'enseignement',
            'twigName' => 'enseignement',
            'inverseJoinColum' => 'voeuList',
            'isFirst' => false,
        ],

        'ecueList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\base\Model\ECUE',
            'mappedBy' => 'enseignement',
            'inversedBy' => 'ecueList',
            'twigName' => 'enseignement',
        ],

        'commentaire_enseignementList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\base\Model\Personne',
            'joinTable' => '\base\Model\commentaire_enseignement',
            'joinColumn' => 'enseignement',
            'twigName' => 'enseignement',
            'inverseJoinColum' => 'commentaire_enseignementList',
            'isFirst' => true,
        ],

    ]; 
}

trait _Enseignement_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $nuac = ''; // Numéro univoquement associé à un cours
    protected string $code = ''; // Code ECUE
    protected string $variante = ''; // liste des ocde Etape (séparés par des | lorsqu'il y a plusieurs enseignement associés à l'étape)
    protected string $nom = '';
    protected string $intitule = '';
    protected bool $attribuable = FALSE;
    protected float $cm = 0;
    protected float $td = 0;
    protected float $ctd = 0;
    protected float $tp = 0;
    protected float $extra = 0;
    protected float $s_cm = 1;
    protected float $s_td = 1;
    protected float $s_ctd = 1;
    protected float $s_tp = 1;
    protected float $s_extra = 1;
    protected float $i_cm = 1;
    protected float $i_td = 1;
    protected float $i_ctd = 1;
    protected float $i_tp = 1;
    protected float $i_extra = 1;
    protected float $d_cm = 0;
    protected float $d_td = 0;
    protected float $d_ctd = 0;
    protected float $d_tp = 0;
    protected float $d_extra = 0;
    protected float $n_cm = 1;
    protected float $n_td = 1;
    protected float $n_ctd = 1;
    protected float $n_tp = 1;
    protected float $n_extra = 1;
    protected float $m_cm = 0;
    protected float $m_td = 0;
    protected float $m_ctd = 0;
    protected float $m_tp = 0;
    protected float $m_extra = 0;
    protected float $bonus = 0;
    protected string $syllabus = '';
    protected string $url = '';
    protected ?\base\Model\TypeUE $typeue;
    protected int $__typeue;
    protected ?\base\Model\Payeur $payeur;
    protected int $__payeur;
    protected ?\base\Model\enseignement_besoins $enseignement_besoins;
    protected ?\base\Model\enseignement_periode $enseignement_periode;
    protected ?\base\Model\enseignement_structure $enseignement_structure;
    protected ?\base\Model\enseignement_etudiant_details $enseignement_etudiant_details;
    protected ?\base\Model\voeu_enseignement_bilan $voeu_enseignement_bilan;
    protected ?\base\Model\voeu_enseignement_bilan_prioritaire $voeu_enseignement_bilan_prioritaire;
    protected ?\base\Model\structure_enseignement $structure_enseignement;
    protected array $voeuList;
    protected array $ecueList;
    protected array $commentaire_enseignementList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

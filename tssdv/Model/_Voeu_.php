<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Voeu_.php
* created : Mon, 06 Apr 2026 10:58:56 +0200 UTC
*/ 

interface _Voeu_interface_ {
    const dbName = 'voeu';
    const idName = 'id';
    const SEARCH = array (
);
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité Voeu
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

        'personne' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Personne',
            'mappedBy' => 'personne',
            'inversedBy' => 'voeuList',
            'twigName' => 'personne',
            'dbName' => 'personne', 
        ],    

        'enseignement' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\tssdv\Model\Enseignement',
            'mappedBy' => 'enseignement',
            'inversedBy' => 'voeuList',
            'twigName' => 'enseignement',
            'dbName' => 'enseignement', 
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

        'ctd' => [
            'dbName' => 'ctd',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'ctd',
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

        'tp' => [
            'dbName' => 'tp',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'tp',
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

        'extra' => [
            'dbName' => 'extra',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'extra',
            'mode' => 'raw',
            ],

        'correspondant' => [
            'dbName' => 'correspondant',
            'type' => Field::BOOL,
            'size' => 100,
            'default' => FALSE,
            'nullable' => TRUE,
            'twigName' => 'correspondant',
            'mode' => 'raw',
            ],

        'etat_ts' => [
            'dbName' => 'etat_ts',
            'type' => Field::INT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'etat_ts',
            'mode' => 'raw',
            ],

        'voeu_bilan_ligne' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\tssdv\Model\voeu_bilan_ligne',
            'dbName' => 'voeu_bilan_ligne',
            'twigName' => 'voeu_bilan_ligne',
            'targetId' => 'id',
        ],    

        'voeu_detail_heures' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\tssdv\Model\voeu_detail_heures',
            'dbName' => 'voeu_detail_heures',
            'twigName' => 'voeu_detail_heures',
            'targetId' => 'id',
        ],    

    ]; 
}

trait _Voeu_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected ?\tssdv\Model\Personne $personne;
    protected int $__personne;
    protected ?\tssdv\Model\Enseignement $enseignement;
    protected int $__enseignement;
    protected float $cm = 0;
    protected float $ctd = 0;
    protected float $td = 0;
    protected float $tp = 0;
    protected float $bonus = 0;
    protected float $extra = 0;
    protected bool $correspondant = FALSE;
    protected int $etat_ts = 0;
    protected ?\tssdv\Model\voeu_bilan_ligne $voeu_bilan_ligne;
    protected ?\tssdv\Model\voeu_detail_heures $voeu_detail_heures;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

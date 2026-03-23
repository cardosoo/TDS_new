<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _voeu_personne_bilan_.php
* created : Fri, 02 May 2025 23:33:41 +0200 UTC
*/ 

interface _voeu_personne_bilan_interface_ {
    const dbName = 'voeu_personne_bilan';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité voeu_personne_bilan
    const entityDef = [

        'heures' => [
            'dbName' => 'heures',
            'type' => Field::FLOAT,
            'size' => 100,
            'default' => 0,
            'nullable' => TRUE,
            'twigName' => 'heures',
            'mode' => 'raw',
            ],

    ]; 
}

trait _voeu_personne_bilan_ {
    protected int $id;            
    protected float $heures = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

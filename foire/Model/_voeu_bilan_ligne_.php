<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _voeu_bilan_ligne_.php
* created : Fri, 02 May 2025 23:33:41 +0200 UTC
*/ 

interface _voeu_bilan_ligne_interface_ {
    const dbName = 'voeu_bilan_ligne';
    const idName = 'id';
    const SEARCH = NULL;
    const GENERIC = NULL;
    const ORDER = NULL;

    // les définitions de l'entité voeu_bilan_ligne
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

trait _voeu_bilan_ligne_ {
    protected int $id;            
    protected float $heures = 0;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

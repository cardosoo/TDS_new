<?php
namespace Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;

/*
* This file is auto-generated and should not be changed by hand.
* filename : _Personne_.php
* created : Mon, 06 Apr 2026 11:00:33 +0200 UTC
*/ 

interface _Personne_interface_ {
    const dbName = 'personne';
    const idName = 'id';
    const SEARCH = array (
  0 => 'prenom',
  1 => 'nom',
  2 => 'prenom',
);
    const GENERIC = array (
  0 => 'prenom',
  1 => 'nom',
);
    const ORDER = array (
  0 => 'nom',
);

    // les définitions de l'entité Personne
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

        'uid' => [
            'dbName' => 'uid',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'uid',
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

        'nom' => [
            'dbName' => 'nom',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'nom',
            'mode' => 'raw',
            ],

        'prenom' => [
            'dbName' => 'prenom',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'prenom',
            'mode' => 'raw',
            ],

        'adresse' => [
            'dbName' => 'adresse',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'adresse',
            'mode' => 'markdown',
            ],

        'tel1' => [
            'dbName' => 'tel1',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'tel1',
            'mode' => 'raw',
            ],

        'tel2' => [
            'dbName' => 'tel2',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'tel2',
            'mode' => 'raw',
            ],

        'email' => [
            'dbName' => 'email',
            'type' => Field::STRING,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'email',
            'mode' => 'raw',
            ],

        'info' => [
            'dbName' => 'info',
            'type' => Field::TEXT,
            'size' => 100,
            'default' => '',
            'nullable' => TRUE,
            'twigName' => 'info',
            'mode' => 'markdown',
            ],

        'statut' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Statut',
            'mappedBy' => 'statut',
            'inversedBy' => 'personneList',
            'twigName' => 'statut',
            'dbName' => 'statut', 
        ],    

        'situation' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Situation',
            'mappedBy' => 'situation',
            'inversedBy' => 'personneList',
            'twigName' => 'situation',
            'dbName' => 'situation', 
        ],    

        'labo' => [
            'type' => Field::ONETOMANY, 
            'targetEntity' => '\foire\Model\Labo',
            'mappedBy' => 'labo',
            'inversedBy' => 'personneList',
            'twigName' => 'labo',
            'dbName' => 'labo', 
        ],    

        'voeu_personne_bilan' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\foire\Model\voeu_personne_bilan',
            'dbName' => 'voeu_personne_bilan',
            'twigName' => 'voeu_personne_bilan',
            'targetId' => 'id',
        ],    

        'personne_charge' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\foire\Model\personne_charge',
            'dbName' => 'personne_charge',
            'twigName' => 'personne_charge',
            'targetId' => 'id',
        ],    

        'personne_situation_reduction' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\foire\Model\personne_situation_reduction',
            'dbName' => 'personne_situation_reduction',
            'twigName' => 'personne_situation_reduction',
            'targetId' => 'id',
        ],    

        'personne_referentiel_heures' => [
            'type' => Field::ONETOONE, 
            'targetEntity' => '\foire\Model\personne_referentiel_heures',
            'dbName' => 'personne_referentiel_heures',
            'twigName' => 'personne_referentiel_heures',
            'targetId' => 'id',
        ],    

        'actasList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Role',
            'joinTable' => '\foire\Model\actAs',
            'joinColumn' => 'personne',
            'twigName' => 'personne',
            'inverseJoinColum' => 'actasList',
            'isFirst' => true,
        ],

        'voeuList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Enseignement',
            'joinTable' => '\foire\Model\Voeu',
            'joinColumn' => 'personne',
            'twigName' => 'personne',
            'inverseJoinColum' => 'voeuList',
            'isFirst' => true,
        ],

        'maquetteList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\foire\Model\Maquette',
            'mappedBy' => 'gestionnaire',
            'inversedBy' => 'maquetteList',
            'twigName' => 'gestionnaire',
        ],

        'respMaquetteList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\foire\Model\Maquette',
            'mappedBy' => 'responsable',
            'inversedBy' => 'respMaquetteList',
            'twigName' => 'responsable',
        ],

        'coRespMaquetteList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\foire\Model\Maquette',
            'mappedBy' => 'co_responsable',
            'inversedBy' => 'coRespMaquetteList',
            'twigName' => 'co_responsable',
        ],

        'responsableList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Etape',
            'joinTable' => '\foire\Model\responsable',
            'joinColumn' => 'personne',
            'twigName' => 'personne',
            'inverseJoinColum' => 'responsableList',
            'isFirst' => false,
        ],

        'personne_etapeList' => [
            'type' => Field::MANYTOONE, 
            'sourceEntity' => '\foire\Model\personne_etape',
            'mappedBy' => 'personne',
            'inversedBy' => 'personne_etapeList',
            'twigName' => 'personne',
        ],

        'commentaire_personneList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Personne',
            'joinTable' => '\foire\Model\commentaire_personne',
            'joinColumn' => 'personne',
            'twigName' => 'personne',
            'inverseJoinColum' => 'commentaire_personneList',
            'isFirst' => true,
        ],

        'auteur_commentaire_personneList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Personne',
            'joinTable' => '\foire\Model\commentaire_personne',
            'joinColumn' => 'auteur',
            'twigName' => 'auteur',
            'inverseJoinColum' => 'auteur_commentaire_personneList',
            'isFirst' => false,
        ],

        'commentaire_enseignementList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Enseignement',
            'joinTable' => '\foire\Model\commentaire_enseignement',
            'joinColumn' => 'auteur',
            'twigName' => 'auteur',
            'inverseJoinColum' => 'commentaire_enseignementList',
            'isFirst' => false,
        ],

        'commentaire_maquetteList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Maquette',
            'joinTable' => '\foire\Model\commentaire_maquette',
            'joinColumn' => 'auteur',
            'twigName' => 'auteur',
            'inverseJoinColum' => 'commentaire_maquetteList',
            'isFirst' => false,
        ],

        'commentaire_composanteList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Composante',
            'joinTable' => '\foire\Model\commentaire_composante',
            'joinColumn' => 'auteur',
            'twigName' => 'auteur',
            'inverseJoinColum' => 'commentaire_composanteList',
            'isFirst' => false,
        ],

        'personne_situationList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Situation',
            'joinTable' => '\foire\Model\personne_situation',
            'joinColumn' => 'personne',
            'twigName' => 'personne',
            'inverseJoinColum' => 'personne_situationList',
            'isFirst' => true,
        ],

        'personne_foncrefList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\FoncRef',
            'joinTable' => '\foire\Model\personne_foncRef',
            'joinColumn' => 'personne',
            'twigName' => 'personne',
            'inverseJoinColum' => 'personne_foncrefList',
            'isFirst' => true,
        ],

        'panierList' =>[ 
            'type' => Field::MANYTOMANY,
            'targetEntity' => '\foire\Model\Enseignement',
            'joinTable' => '\foire\Model\Panier',
            'joinColumn' => 'personne',
            'twigName' => 'personne',
            'inverseJoinColum' => 'panierList',
            'isFirst' => true,
        ],

    ]; 
}

trait _Personne_ {
    protected int $id;            
    protected bool $actif = TRUE;
    protected string $uid = '';
    protected string $ose = '';
    protected string $nom = '';
    protected string $prenom = '';
    protected string $adresse = '';
    protected string $tel1 = '';
    protected string $tel2 = '';
    protected string $email = '';
    protected string $info = '';
    protected ?\foire\Model\Statut $statut;
    protected int $__statut;
    protected ?\foire\Model\Situation $situation;
    protected int $__situation;
    protected ?\foire\Model\Labo $labo;
    protected int $__labo;
    protected ?\foire\Model\voeu_personne_bilan $voeu_personne_bilan;
    protected ?\foire\Model\personne_charge $personne_charge;
    protected ?\foire\Model\personne_situation_reduction $personne_situation_reduction;
    protected ?\foire\Model\personne_referentiel_heures $personne_referentiel_heures;
    protected array $actasList;
    protected array $voeuList;
    protected array $maquetteList;
    protected array $respMaquetteList;
    protected array $coRespMaquetteList;
    protected array $responsableList;
    protected array $personne_etapeList;
    protected array $commentaire_personneList;
    protected array $auteur_commentaire_personneList;
    protected array $commentaire_enseignementList;
    protected array $commentaire_maquetteList;
    protected array $commentaire_composanteList;
    protected array $personne_situationList;
    protected array $personne_foncrefList;
    protected array $panierList;

    protected int $__status__ = Entity::NEW; 
    protected array $__org__ = [];
}

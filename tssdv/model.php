<?php
namespace tssdv;

use \TDS\Model\Model as M;
use \TDS\Model\Entity as E;
use \TDS\Model\Field as F;
use \TDS\Model\ManyToMany as MM;
use \TDS\Model\OneToMany as OM;
use TDS\Model\OneToMany;
use \TDS\Model\View as V;

if (! isset(M::$appName)){
    M::$appName = __NAMESPACE__;
}

include '../base/model.php';
M::$parentApp = 'base';


// pour ajouter les spécificités sur les voeux :
$personne->addField(new F('etat_ts', F::BOOL ));    
$enseignement->addField(new F('etat_ts', F::BOOL ));    
$voeu->addField(new F('etat_ts', F::INT ));    

$enseignement_besoins_detail = M::addEntity(new V('enseignement_besoins_detail', ['withActif' => false] ));
$enseignement_besoins_detail->addField(new F('cm', F::FLOAT));
$enseignement_besoins_detail->addField(new F('ctd', F::FLOAT));
$enseignement_besoins_detail->addField(new F('td', F::FLOAT));
$enseignement_besoins_detail->addField(new F('tp', F::FLOAT));
$enseignement_besoins_detail->addField(new F('extra', F::FLOAT));
$enseignement_besoins_detail->addField(new F('bonus', F::FLOAT));

$enseignement->addOneToOne($enseignement_besoins_detail);




// pour ajouter les domaines
$domaine = M::addEntity(new E('Domaine', [E::search => ['acronyme', 'nom'] ]));
$domaine->addField(new F('nom', F::STRING));
$domaine->addField(new F('acronyme', F::STRING));

// pour indiquer les quotités pour les enseignants
$domaine_personne = M::addEntity(new MM('domaine_personne', $domaine, $personne, 
[E::search =>[]]));
$domaine_personne->addField(new F('ordre',    F::INT));
$domaine_personne->addField(new F('quotite',   F::FLOAT));


// pour indiquer les quotités pour les enseignements
$domaine_enseignement = M::addEntity(new MM('domaine_enseignement', $domaine, $enseignement, [E::search =>[] ]));
$domaine_enseignement->addField(new F('ordre',    F::INT));
$domaine_enseignement->addField(new F('quotite',   F::FLOAT));

// pour indiquer les responsables du domaine
$domaine_responsable = M::addEntity(new MM('domaine_responsable', $domaine, $personne, 
[E::generic => [] , 'opt2' => [ OM::mappedBy => 'responsable', /* OM::inversedBy => 'responsableList', OM::twigName => 'responsables' */] ]));
$domaine_enseignement->addField(new F('ordre',    F::INT));

M::setMocodo([
    [$role, $actAs, ], 
    [$commentaire_enseignement, $labo, $statut, $situation, $commentaire_personne, ],
    [ $enseignement, $voeu, $personne, $maquette,],
    [$payeur, $typeUE, $domaine_enseignement, $domaine, $domaine_personne, $domaine_responsable],
    [$ecue, $ue, $semestre, $etape, $diplome ],
    [$responsable, $cursus, $composante, ]
]);

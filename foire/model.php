<?php
namespace foire;

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

// par ajouter les anciennetés
$voeu->addField(new F('anciennete', F::INT));

// pour l'héritage de l'ancienne
$heritage = M::addEntity(new MM('Heritage', $enseignement, $enseignement, [
    E::search =>[], 
    MM::twigName => 'her', 
    'opt1' => [ OM::mappedBy => 'parent', OM::inversedBy => 'Heritage_Enfants', OM::twigName => 'enfants'], 
    'opt2' => [ OM::mappedBy => 'enfant', OM::inversedBy => 'Heritage_Parents', OM::twigName => 'parents'],
    ]));

// pour ajouter les paniers
$panier = M::addEntity(new MM('Panier', $personne, $enseignement, [E::search =>[] ]));
$panier->addField(new F('cm',    F::BOOL));
$panier->addField(new F('ctd',   F::BOOL));
$panier->addField(new F('td',    F::BOOL));
$panier->addField(new F('tp',    F::BOOL));
$panier->addField(new F('commentaire', F::TEXT, [E::search => [] ]));


// pour les situations pariculières :
$situation->addField(new F('reduction_legale', F::STRING, [ F::default => '0h' ]));
$situation->addField(new F('ufr', F::BOOL));


// Le modèle pour construire le Mocodo qui va bien 
M::setMocodo([
    [$role, $actAs, $foncRef, $referentiel, $catRef, $cat1Ref], 
    [$labo, $statut,  $personne_foncRef, $personne_situation],
    [$enseignement, $voeu, $personne, $situation], 
    [$heritage, $panier, $typeUE, $payeur], 
    //[$ecue, $ue, $semestre, $etape, $diplome, $maquette, ],
    //[$responsable, $cursus, $composante, ]
]);

<?php
namespace zeroU;

use \TDS\Model\Model as M;
use \TDS\Model\Entity as E;
use \TDS\Model\Field as F;
use \TDS\Model\ManyToMany as MM;
use \TDS\Model\View as V;

if (! isset(M::$appName)){
    M::$appName = __NAMESPACE__;
}


M::$idName = 'id';

$role = M::addEntity(new E('Role'));
$role->addField(new F('nom', F::STRING));

$personne = M::addEntity(new E('Personne', [E::search => ['prenom', 'nom', 'prenom' ], E::order =>['nom'], E::generic => ['prenom', 'nom'] ]));
$personne->addField(new F('uid', F::STRING));
$personne->addField(new F('nom', F::STRING));
$personne->addField(new F('prenom', F::STRING));

$actAs = M::addEntity(new MM('actAs', $personne, $role));




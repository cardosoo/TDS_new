<?php
namespace base;

use \TDS\Model\Model as M;
use \TDS\Model\Entity as E;
use \TDS\Model\Field as F;
use \TDS\Model\ManyToMany as MM;
use \TDS\Model\OneToMany as OM;
use \TDS\Model\View as V;

if (! isset(M::$appName)){
    M::$appName = __NAMESPACE__;
}

M::$parentApp ='zeroUP';
include '../zeroUP/model.php';

M::$idName = 'id'; // nécessaire pour travailler avec les bases de données de BD2

M::removeEntity($user);
M::removeEntity($actAs);
M::removeEntity($role);

$labo = M::addEntity(new E('Labo', [E::search => ['acronyme', 'nom'] ]));
$labo->addField(new F('nom', F::STRING));
$labo->addField(new F('acronyme', F::STRING));
$labo->addField(new F('url', F::STRING));

$statut = M::addEntity(new E('Statut' , [E::search => ['nom'], E::order =>['nom'], E::generic => ['nom'] ]));
$statut->addField(new F('nom', F::STRING));
$statut->addField(new F('obligation', F::INT, [F::default => 192]));

$situation = M::addEntity(new E('Situation'));
$situation->addField(new F('nom', F::STRING, [F::size => 100]));
$situation->addField(new F('ose', F::STRING, [F::size => 100]));
$situation->addField(new F('reduction', F::INT));
$situation->addField(new F('public', F::BOOL, [F::default => 'FALSE']));

$personne = M::addEntity(new E('Personne', [E::search => ['prenom', 'nom', 'prenom' ], E::order =>['nom'], E::generic => ['prenom', 'nom'] ]));
$personne->addField(new F('uid', F::STRING));
$personne->addField(new F('ose', F::STRING));
$personne->addField(new F('nom', F::STRING));
$personne->addField(new F('prenom', F::STRING));
$personne->addField(new F('adresse', F::TEXT, [F::mode => F::MARKDOWN ]));
$personne->addField(new F('tel1', F::STRING));
$personne->addField(new F('tel2', F::STRING));
$personne->addField(new F('email', F::STRING));
$personne->addField(new F('info', F::TEXT, [F::mode => F::MARKDOWN ]));
$personne->addOneToMany($statut);
$personne->addOneToMany($situation);
$personne->addOneToMany($labo);

$enseignement = M::addEntity(new E('Enseignement', [E::search =>['nom', 'intitule', 'nuac', 'code'], E::generic => ['code', 'nom'] ]));
$enseignement->addField(new F('nuac', F::STRING, [F::comment => "Numéro univoquement associé à un cours"]));
$enseignement->addField(new F('code', F::STRING, [F::comment => "Code ECUE"]));
$enseignement->addField(new F('variante', F::STRING, [F::comment => "liste des ocde Etape (séparés par des | lorsqu'il y a plusieurs enseignement associés à l'étape)"]));
$enseignement->addField(new F('nom', F::STRING,));
$enseignement->addField(new F('intitule', F::STRING));
$enseignement->addField(new F('attribuable', F::BOOL)); // pour indiquer si l'enseignement peut être ajouter à un tableau de service

$enseignement->addTwigFragment('{% block besoins %}');
// nombre de groupes
$enseignement->addField(new F('cm', F::FLOAT));
$enseignement->addField(new F('td', F::FLOAT));
$enseignement->addField(new F('ctd', F::FLOAT));
$enseignement->addField(new F('tp', F::FLOAT));
$enseignement->addField(new F('extra', F::FLOAT));
// nombre de séances 
$enseignement->addField(new F('s_cm', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('s_td', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('s_ctd', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('s_tp', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('s_extra', F::FLOAT,  [F::default => 1]));
// nombre d'intervenant par séance 
$enseignement->addField(new F('i_cm', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('i_td', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('i_ctd', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('i_tp', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('i_extra', F::FLOAT,  [F::default => 1]));
// durée d'une séance 
$enseignement->addField(new F('d_cm', F::FLOAT));
$enseignement->addField(new F('d_td', F::FLOAT));
$enseignement->addField(new F('d_ctd', F::FLOAT));
$enseignement->addField(new F('d_tp', F::FLOAT));
$enseignement->addField(new F('d_extra', F::FLOAT));
// nombre de séance par semaine 
$enseignement->addField(new F('n_cm', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('n_td', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('n_ctd', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('n_tp', F::FLOAT,  [F::default => 1]));
$enseignement->addField(new F('n_extra', F::FLOAT,  [F::default => 1]));
// capacité max d'un groupe
$enseignement->addField(new F('m_cm', F::FLOAT));
$enseignement->addField(new F('m_td', F::FLOAT));
$enseignement->addField(new F('m_ctd', F::FLOAT));
$enseignement->addField(new F('m_tp', F::FLOAT));
$enseignement->addField(new F('m_extra', F::FLOAT));

$enseignement->addTwigFragment('{% endblock %} ');

$enseignement->addField(new F('bonus', F::FLOAT));
$enseignement->addField(new F('syllabus', F::TEXT, [F::mode => F::HTML ]));
$enseignement->addField(new F('url', F::TEXT, [F::mode => F::RAW ]));

$typeUE = M::addEntity(new E('TypeUE'));
$typeUE->addField(new F('nom', F::STRING));

$payeur = M::addEntity(new E('Payeur'));
$payeur->addField(new F('nom', F::STRING));

$enseignement->addOneToMany($typeUE);
$enseignement->addOneToMany($payeur);


$role = M::addEntity(new E('Role', [E::search =>['nom'] ] ));
$role->addField(new F('nom', F::STRING));
$role->addField(new F('auth', F::TEXT)); // Cela sert à indiquer les autorisations en CRUD sur les différentes entitées

$actAs = M::addEntity(new MM('actAs', $personne, $role, [E::search =>[] ]));

$voeu = M::addEntity(new MM('Voeu', $personne, $enseignement, [E::search =>[] ]));
$voeu->addField(new F('cm',    F::FLOAT));
$voeu->addField(new F('ctd',   F::FLOAT));
$voeu->addField(new F('td',    F::FLOAT));
$voeu->addField(new F('tp',    F::FLOAT));
$voeu->addField(new F('bonus', F::FLOAT));
$voeu->addField(new F('extra', F::FLOAT));
$voeu->addField(new F('correspondant', F::BOOL, [F::default => 'FALSE']));


// structure des enseignements
$composante = M::addEntity(new E('Composante', [E::search => [/*'nom', 'intitule'*/], E::generic => ['nom'], E::order => ['ordre']]));
$composante->addField(new F('ordre', F::INT));
$composante->addField(new F('nom', F::STRING));
$composante->addField(new F('intitule', F::STRING));

$cursus = M::addEntity(new E('Cursus', [E::search => [/*'nom', 'intitule'*/], E::generic => ['nom'], E::order => ['nom']]));
$cursus->addField(new F('nom', F::STRING));
$cursus->addField(new F('intitule', F::STRING));

$maquette = M::addEntity(new E('Maquette', [E::search => [/*'nom', 'code'*/], E::generic => ['code', 'nom'], E::order => ['ordre']]));
$maquette->addField(new F('ordre', F::INT));
$maquette->addField(new F('code', F::STRING));
$maquette->addField(new F('version', F::STRING));
$maquette->addField(new F('nom', F::STRING));
$maquette->addOneToMany($personne, [OM::mappedBy => 'gestionnaire']);
$maquette->addOneToMany($personne, [OM::mappedBy => 'responsable', OM::inversedBy => 'respMaquetteList']);
$maquette->addOneToMany($personne, [OM::mappedBy => 'co_responsable', OM::inversedBy => 'coRespMaquetteList']);
$maquette->addOneToMany($composante);

$diplome = M::addEntity(new E('Diplome', [E::search => [/*'nom', 'code'*/], E::generic => ['code', 'nom'], E::order => ['ordre']]));
$diplome->addField(new F('ordre', F::INT));
$diplome->addField(new F('code', F::STRING));
$diplome->addField(new F('nom', F::STRING));
$diplome->addOneToMany($maquette);

$etape = M::addEntity(new E('Etape', [E::search => [/*'nom', 'code'*/], E::generic => ['code', 'nom'], E::order => ['ordre']]));
$etape->addField(new F('ordre', F::INT));
$etape->addField(new F('code', F::STRING));
$etape->addField(new F('nom', F::STRING));
$etape->addField(new F('nbetu', F::INT));
$etape->addOneToMany($diplome);
$etape->addOneToMany($cursus);

$responsable = M::addEntity(new MM('responsable', $etape, $personne));

$semestre = M::addEntity(new E('Semestre', [E::search => [/*'nom', 'code'*/], E::generic => ['code', 'nom'], E::order => ['ordre']]));
$semestre->addField(new F('ordre', F::INT));
$semestre->addField(new F('code', F::STRING));
$semestre->addField(new F('nom', F::STRING));
$semestre->addField(new F('peretu', F::INT, [F::default=>100]));
$semestre->addField(new F('periode', F::INT, [F::default=>0]));
$semestre->addOneToMany($etape);

$ue = M::addEntity(new E('UE', [E::search => [/*'nom', 'code'*/], E::generic => ['code', 'nom'], E::order => ['ordre']]));
$ue->addField(new F('ordre', F::INT));
$ue->addField(new F('code', F::STRING));
$ue->addField(new F('nom', F::STRING));
$ue->addField(new F('peretu', F::INT, [F::default=>100]));
$ue->addField(new F('ects', F::FLOAT, [F::default=>0]));
$ue->addOneToMany($semestre);

$ecue = M::addEntity(new E('ECUE', [E::search => [/*'nom', 'code'*/], E::generic => ['code', 'nom'], E::order => ['ordre']]));
$ecue->addField(new F('ordre', F::INT));
$ecue->addField(new F('code', F::STRING));
$ecue->addField(new F('nom', F::STRING));
$ecue->addField(new F('peretu', F::INT, [F::default=>100]));
$ecue->addField(new F('ects', F::FLOAT, [F::default=>0]));
$ecue->addOneToMany($ue);
$ecue->addOneToMany($enseignement);

/*
// pour la nouvelle utilisation des étapes... 
$etapeOSE = M::addEntity(new E('Etape_OSE', [E::search => ['nom', 'code'], E::generic => ['code', 'nom'], E::order => ['code']]));
$etapeOSE->addField(new F('code', F::STRING));
$etapeOSE->addField(new F('nom', F::STRING));
$etapeOSE->addField(new F('nbetu', F::INT));

$etape_personne = M::addEntity(new MM('etape_personne', $etapeOSE, $personne));
$etape_personne = M::addEntity(new MM('etape_enseignement', $etapeOSE, $enseignement));
*/
$personne_etape = M::addEntity(new E('personne_etape'));
$personne_etape->addField(new F('code', F::STRING));
$personne_etape->addOneToMany($personne);


// pour ajouter les commentaires sur les personnes
$commentaire_personne = M::addEntity(new MM('commentaire_personne', $personne, $personne, [
    E::search =>[], 
    E::order => ['date'] ,   
    'opt1' => [ ''], 
    'opt2' => [ 'mappedBy' => 'auteur', 'inversedBy' => 'auteur_commentaire_personneList' ],
]));
$commentaire_personne->addField(new F('date',  F::DATE, [F::default => 'now']));
$commentaire_personne->addField(new F('commentaire',   F::TEXT, [E::search => [] ]));

// pour ajouter les commentaires sur les enseignements
$commentaire_enseignement = M::addEntity(new MM('commentaire_enseignement', $enseignement, $personne, [
    E::search =>[], 
    E::order => ['date'],
    'opt1' => [ ''], 
    'opt2' => [ 'mappedBy' => 'auteur' ],
]));
$commentaire_enseignement->addField(new F('date',  F::DATE, [F::default => 'now']));
$commentaire_enseignement->addField(new F('commentaire',   F::TEXT, [E::search => [] ]));


// pour ajouter les commentaires sur les maquettes
$commentaire_maquette= M::addEntity(new MM('commentaire_maquette', $maquette, $personne, [
    E::search =>[], 
    E::order => ['date'] ,   
    'opt1' => [ ''], 
    'opt2' => [ 'mappedBy' => 'auteur'],
]));

$commentaire_maquette->addField(new F('date',  F::DATE, [F::default => 'now']));
$commentaire_maquette->addField(new F('commentaire',   F::TEXT, [E::search => [] ]));


// pour ajouter les commentaires sur les composante
$commentaire_composante= M::addEntity(new MM('commentaire_composante', $composante, $personne, [
    E::search =>[], 
    E::order => ['date'] ,   
    'opt1' => [ ''], 
    'opt2' => [ 'mappedBy' => 'auteur'],
]));

$commentaire_composante->addField(new F('date',  F::DATE, [F::default => 'now']));
$commentaire_composante->addField(new F('commentaire',   F::TEXT, [E::search => [] ]));


// pour les situations particulières
$personne_situation = M::addEntity(new MM('personne_situation', $personne, $situation, [
    E::search =>[], 
    E::order => ['debut'] ,   
]));
$personne_situation->addField(new F('debut',  F::DATE, [F::default => 'now']));
$personne_situation->addField(new F('fin',  F::DATE, [F::default => 'now']));
$personne_situation->addField(new F('reduction', F::INT));
$personne_situation->addField(new F('commentaire', F::TEXT));


// pour le referentiel
$cat1Ref = M::addEntity(new E('Cat1Ref' ));
$cat1Ref->addField(new F('description', F::STRING));


$catRef = M::addEntity(new E('CatRef' ));
$catRef->addField(new F('description', F::TEXT));
$catRef->addOneToMany($cat1Ref);

$referentiel = M::addEntity(new E('Referentiel'));
$referentiel->addField(new F('code', F::STRING));
$referentiel->addField(new F('description', F::TEXT));
$referentiel->addField(new F('calcul', F::TEXT));
$referentiel->addField(new F('observations', F::TEXT));
$referentiel->addOneToMany($catRef);

$foncRef = M::addEntity(new E('FoncRef'));
$foncRef->addField(new F('intitule', F::TEXT));
$foncRef->addOneToMany($referentiel);

$personne_foncRef = M::addEntity(new MM('personne_foncRef', $personne, $foncRef));
$personne_foncRef->addField(new F('commentaire', F::TEXT));
$personne_foncRef->addField(new F('volume', F::FLOAT));

//============================================================== les vues ==================================
$voeu_bilan_ligne = M::addEntity(new V('voeu_bilan_ligne', ['withActif' => false] ));
$voeu_bilan_ligne->addField(new F('heures', F::FLOAT));

$voeu_personne_bilan = M::addEntity(new V('voeu_personne_bilan', ['withActif' => false] ));
$voeu_personne_bilan->addField(new F('heures', F::FLOAT));

$personne->addOneToOne($voeu_personne_bilan);

$voeu_detail_heures = M::addEntity(new V('voeu_detail_heures', ['withActif' => false])) ;
$voeu_detail_heures->addField(new F('cm',    F::FLOAT));
$voeu_detail_heures->addField(new F('ctd',   F::FLOAT));
$voeu_detail_heures->addField(new F('td',    F::FLOAT));
$voeu_detail_heures->addField(new F('tp',    F::FLOAT));
$voeu_detail_heures->addField(new F('bonus', F::FLOAT));
$voeu_detail_heures->addField(new F('extra', F::FLOAT));



$voeu_enseignement_bilan = M::addEntity(new V('voeu_enseignement_bilan', ['withActif' => false] ));
$voeu_enseignement_bilan->addField(new F('cm',    F::FLOAT));
$voeu_enseignement_bilan->addField(new F('ctd',   F::FLOAT));
$voeu_enseignement_bilan->addField(new F('td',    F::FLOAT));
$voeu_enseignement_bilan->addField(new F('tp',    F::FLOAT));
$voeu_enseignement_bilan->addField(new F('bonus', F::FLOAT));
$voeu_enseignement_bilan->addField(new F('extra', F::FLOAT));
$voeu_enseignement_bilan->addField(new F('correspondant', F::BOOL));
$voeu_enseignement_bilan->addField(new F('heures', F::FLOAT));


$voeu_enseignement_bilan_prioritaire = M::addEntity(new V('voeu_enseignement_bilan_prioritaire', ['withActif' => false] ));
$voeu_enseignement_bilan_prioritaire->addField(new F('cm',    F::FLOAT));
$voeu_enseignement_bilan_prioritaire->addField(new F('ctd',   F::FLOAT));
$voeu_enseignement_bilan_prioritaire->addField(new F('td',    F::FLOAT));
$voeu_enseignement_bilan_prioritaire->addField(new F('tp',    F::FLOAT));
$voeu_enseignement_bilan_prioritaire->addField(new F('bonus', F::FLOAT));
$voeu_enseignement_bilan_prioritaire->addField(new F('extra', F::FLOAT));
$voeu_enseignement_bilan_prioritaire->addField(new F('correspondant', F::BOOL));
$voeu_enseignement_bilan_prioritaire->addField(new F('heures', F::FLOAT));


$enseignement_besoins = M::addEntity(new V('enseignement_besoins', ['withActif' => false] ));
$enseignement_besoins->addField(new F('besoins', F::FLOAT));

$structure_enseignement = M::addEntity(new V('structure_enseignement', ['idName' => 'enseignement', 'withActif' => false]));
$structure_enseignement->addField(new F('periode', F::INT));
$structure_enseignement->addField(new F('nbetu', F::FLOAT));
$structure_enseignement->addField(new F('code_ue', F::STRING));
$structure_enseignement->addField(new F('code_ecue', F::STRING));


$enseignement_structure =  M::addEntity(new V('enseignement_structure', [E::search => [], 'withActif' => false]));
$enseignement_structure->addField(new F('periode', F::STRING));
$enseignement_structure->addField(new F('code', F::STRING));
$enseignement_structure->addField(new F('ecue', F::STRING));
$enseignement_structure->addField(new F('cursus', F::STRING));
$enseignement_structure->addField(new F('etape', F::STRING));
$enseignement_structure->addField(new F('maquette', F::STRING));
$enseignement_structure->addField(new F('composante', F::STRING));
$enseignement_structure->addField(new F('nbetu', F::STRING)); // un array avec les étudiants des différents parcours
$enseignement_structure->addField(new F('netu', F::FLOAT)); // le nombre d'étudiants total calculé via la maquette

$enseignement_etudiant_details =  M::addEntity(new V('enseignement_etudiant_details', [E::search => [], 'withActif' => false]));
$enseignement_etudiant_details->addField(new F('cm', F::FLOAT));
$enseignement_etudiant_details->addField(new F('ctd', F::FLOAT));
$enseignement_etudiant_details->addField(new F('td', F::FLOAT));
$enseignement_etudiant_details->addField(new F('tp', F::FLOAT));
$enseignement_etudiant_details->addField(new F('extra', F::FLOAT));
$enseignement_etudiant_details->addField(new F('bonus', F::FLOAT));


$enseignement_periode = M::addEntity(new V('enseignement_periode', [E::search => [], 'withActif' => false, ]));
$enseignement_periode->addField(new F('periode', F::STRING));

$enseignement->addOneToOne($enseignement_besoins);
$enseignement->addOneToOne($enseignement_periode, ['targetId' => 'id' ]);
$enseignement->addOneToOne($enseignement_structure, ['targetId' => 'id' ]);
$enseignement->addOneToOne($enseignement_etudiant_details, ['targetId' => 'id' ]);
$enseignement->addOneToOne($voeu_enseignement_bilan);
$enseignement->addOneToOne($voeu_enseignement_bilan_prioritaire);


$voeu->addOneToOne($voeu_bilan_ligne);
$voeu->addOneToOne($voeu_detail_heures);

$enseignement->addOneToOne($structure_enseignement, ['targetId'=> 'enseignement']);
$composante->addOneToOne($structure_enseignement, ['targetId'=> 'composante']);
$maquette->addOneToOne($structure_enseignement, ['targetId'=> 'maquette']);
$etape->addOneToOne($structure_enseignement, ['targetId'=> 'etape']);
$cursus->addOneToOne($structure_enseignement, ['targetId'=> 'cursus']);
//$diplome->addOneToOne($structure_enseignement, ['targetId'=> 'diplome']);
$semestre->addOneToOne($structure_enseignement, ['targetId'=> 'semestre']);
$ue->addOneToOne($structure_enseignement, ['targetId'=> 'ue']);
$ecue->addOneToOne($structure_enseignement, ['targetId'=> 'ecue']);

$personne_charge = M::addEntity(new V('personne_charge', [E::search => [], 'withActif' => false] ));
$personne_charge->addField(new F('charge', F::FLOAT));
$personne->addOneToOne($personne_charge);

$personne_situation_reduction =  M::addEntity(new V('personne_situation_reduction', [E::search => [], 'withActif' => false, ]));
$personne_situation_reduction->addField(new F('reduction', F::INT));
$personne->addOneToOne($personne_situation_reduction);

$personne_referentiel_heures =  M::addEntity(new V('personne_referentiel_heures', [E::search => [], 'withActif' => false, ]));
$personne_referentiel_heures->addField(new F('heures', F::FLOAT));
$personne->addOneToOne($personne_referentiel_heures);

M::setMocodo([
    [$role, $actAs, ], 
    [$labo, $statut, $situation, ],
    [$enseignement, $voeu, $personne,], 
    [$ecue, $ue, $semestre, $etape, $diplome, $maquette, ],
    [$responsable, $cursus, $composante, ]
]);

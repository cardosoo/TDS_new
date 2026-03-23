<?php
namespace foire;

use \TDS\Route;


include '../base/router.php';

$app = \TDS\App::get();


$app::$router->setNamespace('\\'.__NAMESPACE__.'\\Controllers\\');

// test
$app::$router->updateRoute('test_1', 'TestController::test1');
$app::$router->updateRoute('test_2', 'TestController::test2');
$app::$router->updateRoute('test_3', 'TestController::test3');

// C'est à remplacer par un truc PlanningController::detailsAPI au niveau de base
//$app::$router->routeList['public']['test']['test4']= new Route('GET','/test/test4/[*:cursus]/[*:composante]/[i:perdiode]', 'TestController::test4', 'test_foire_4');
$app::$router->routeList['public']['test']['test4']= new Route('GET','/test/test4', 'TestController::test4', 'test_foire_4');

// mailler 
$app::$router->updateRoute('mailer_home', 'MailerController::mailer');

// enseignement
$app::$router->updateRoute('enseignement_fiche', 'EnseignementController::fiche');
$app::$router->updateRoute('enseignement_voeu', 'EnseignementController::voeu');
$app::$router->updateRoute('post_enseignement_voeu', 'EnseignementController::addOrChangeVoeu');

// personne
$app::$router->updateRoute('personne_fiche', 'PersonneController::fiche');
$app::$router->updateRoute('personne_searchLDAP_numetu', 'PersonneController::searchLDAPNumetu');
$app::$router->updateRoute('personne_saveStages', 'PersonneController::saveStages');
$app::$router->routeList['private']['personne'][] =  new Route('GET','/personne/loadConvention/[i:year]/[i:id]/[a:num]', 'PersonneController::loadConventions', 'personne_loadConventionss');

// migration
$app::$router->routeList['restrict']['Admin'][] = new Route('GET|POST', '/admin/migrationSituations/[confirm:action]?', 'AdminController::migrationSituations','admin_migrationSituations');
$app::$router->routeList['restrict']['Admin'][] = new Route('GET', '/admin/migrationReferentiels/[confirm:action]?', 'AdminController::migrationReferentiels','admin_migrationReferentiels');
$app::$router->routeList['restrict']['Admin'][] = new Route('GET', '/admin/migrationVoeux/[confirm:action]?', 'AdminController::migrationVoeux','admin_migrationVoeux');
$app::$router->routeList['restrict']['Admin'][] = new Route('GET', '/admin/calculReports/[confirm:action]?', 'AdminController::calculReports','admin_calculReport');

// panier
$app::$router->routeList['private']['enseignement'][] = new Route('POST', '/enseignement/addPanier/[i:id]', 'EnseignementController::addPanier','enseignement_addPanier');
$app::$router->routeList['private']['enseignement'][] = new Route('POST', '/enseignement/suppPanier/[i:id]', 'EnseignementController::suppPanier','enseignement_suppPanier');
$app::$router->routeList['restrict']['Admin'][] = new Route('POST', '/enseignement/ajaxPanier', 'EnseignementController::ajaxPanier','enseignement_ajaxPanier');
$app::$router->routeList['restrict']['Admin'][] = new Route('POST', '/personne/ajaxPanier', 'PersonneController::ajaxPanier','personne_ajaxPanier');
$app::$router->routeList['restrict']['Admin'][] = new Route('GET|POST', '/admin/deletePanierInactive', 'AdminController::deletePanierInactive','admin_deletePanierInactive');

// voeux
$app::$router->routeList['restrict']['Admin'][] = new Route('GET', '/CRUD/voeu/computeAnciennete/[i:idP]/[i:idE]', 'VoeuController::CRUD_calculAnciennete','crud_voeu_calculAnciennete');

// Mesures transitoires pour l'année 2020 - 2021 passage de l'ancien modèle de BD au nouveau modèle de BD 
$app::$router->routeList['restrict']['Admin'][] = new Route('GET', '/t2020/migrationVoeux2020', 'T2020Controller::migrationVoeux2020','transitoire_migration2020');
$app::$router->routeList['restrict']['Admin'][] = new Route('GET', '/t2020/calculReports2020', 'T2020Controller::calculReports2020','transitoire_calculReport2020');
$app::$router->routeList['restrict']['Admin'][] = new Route('GET', '/t2020/importCodesLDAP2020', 'T2020Controller::importCodesLDAP2020','transitoire_importCodesLDAP2020');
$app::$router->routeList['restrict']['Gestionnaire'][] = new Route('GET|POST', '/t2020/heures', 'T2020Controller::heures2020','gestionnaire_heures2020');

// gestion 
$app::$router->routeList['restrict']['SuperAdmin'][] = new Route('GET|POST', '/admin/setChargeUFR', 'AdminController::setChargeUFR','admin_setChargeUFR');

// gestionnaire
$app::$router->updateRoute('gestionnaire_home', 'GestionnaireController::home');
$app::$router->routeList['restrict']['Gestionnaire'][] = new Route('GET|POST', '/gestionnaire/effectifsGlobaux', 'GestionnaireController::effectifsGlobaux','gestionnaire_effectifsGlobaux');
$app::$router->routeList['restrict']['Gestionnaire'][] = new Route('GET|POST', '/gestionnaire/ratioHF', 'GestionnaireController::ratioHF','gestionnaire_ratioHF');
$app::$router->routeList['restrict']['Gestionnaire'][] = new Route('GET|POST', '/gestionnaire/repartitionNiveau', 'GestionnaireController::repartitionNiveau','gestionnaire_repartitionNiveau');
$app::$router->routeList['restrict']['Gestionnaire'][] = new Route('GET|POST', '/gestionnaire/repartitionNiveauStatut', 'GestionnaireController::repartitionNiveauStatut','gestionnaire_repartitionNiveauStatut');
$app::$router->routeList['restrict']['Gestionnaire'][] = new Route('GET|POST', '/gestionnaire/vacataires', 'GestionnaireController::vacataires','gestionnaire_vacataires');

// OSE
$app::$router->routeList['restrict']['Gestionnaire'][] = new Route('GET|POST', '/gestionnaire/OSE/listingServices', 'GestionnaireController::listingServices','OSE_listingServices');
$app::$router->routeList['restrict']['Gestionnaire'][] = new Route('GET|POST', '/gestionnaire/OSE/modificationServices', 'GestionnaireController::modificationServices','OSE_modificationServices');

// CENS
$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS', 'CENSController::home', 'CENS_home');
$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/repFiliereAnnee', 'CENSController::repFiliereAnneeMenu', 'CENS_repFiliereAnneeMenu');
$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/repFiliereAnnee/[i:cursusID]', 'CENSController::repFiliereAnnee', 'CENS_repFiliereAnnee');
$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/coutFiliereAnnee', 'CENSController::coutFiliereAnnee', 'CENS_coutFiliereAnnee');
$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/coutTotal', 'CENSController::coutTotal', 'CENS_coutTotal');

$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/listeSituations', 'GestionnaireController::listeSituations', 'CENS_listeSituations');
$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/listeReferentiel', 'GestionnaireController::listeReferentiel', 'CENS_listeReferentiel');

$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/potentielEnseignant', 'CENSController::potentielEnseignant', 'CENS_potentielEnseignant');
$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/listeStages', 'CENSController::listeStages', 'CENS_listeStages');

$app::$router->routeList['restrict']['CENS'][] =   new Route('GET', '/CENS/hEtuFiliereAnnee', 'CENSController::hEtuFiliereAnnee', 'CENS_hEtuFiliereAnnee');

$app::$router->routeList['public']['api']['CENS'] = new Route('GET', '/API/coutFiliereAnnee/[i:year]', 'CENSController::APIcoutFiliereAnnee', 'API_coutFiliereAnnee');
$app::$router->routeList['public']['api']['CENS'] = new Route('GET', '/API/hEtuFiliereAnnee/[i:year]', 'CENSController::APIhEtuFiliereAnnee', 'API_hEtuFiliereAnnee');
$app::$router->routeList['public']['api']['JUPY'] = new Route('GET', '/API/getFullActiveUsers/[i:year]', 'APIController::getFullActiveUsers', 'API_getFullActiveUsers');


$app::$router->routeList['public']['api']['noFinishLine'] = new Route('GET', '/API/noFinishLine', 'APIController::noFInishLine', 'API_noFinishLine');
$app::$router->routeList['public']['api']['getFinishLine'] = new Route('GET', '/API/getNoFinishLine/[i:dossard]', 'APIController::getNoFinishLine', 'API_getNoFinishLine');

<?php
namespace tssdv;

use AltoRouter;
use \TDS\Route;

include '../base/router.php';

$app = \TDS\App::get();
$router = $app::$router;

$router->setNamespace('\\'.__NAMESPACE__.'\\Controllers\\');


// test
$router->updateRoute('test_1', 'TestController::test1');
$router->updateRoute('test_2', 'TestController::test2');
$router->updateRoute('test_3', 'TestController::test3');

// mailler
$router->updateRoute('mailer_home', 'MailerController::mailer');

// recherche
$router->updateRoute('recherche_what', 'RechercheController::what');
$router->updateRoute('post_recherche_what', 'RechercheController::rechercheEnseignement');

// enseignement
$router->updateRoute('enseignement_fiche', 'EnseignementController::fiche');
$router->updateRoute('enseignement_voeu', 'EnseignementController::voeu');
$router->updateRoute('post_enseignement_voeu', 'EnseignementController::addOrChangeVoeu');
$router->updateRoute('delete_enseignement_voeu', 'EnseignementController::deleteVoeu');

// personne
$router->updateRoute('personne_fiche', 'PersonneController::fiche');
$router->routeList['withAuth']['personne'][] =   new Route('GET', '/personne/validerTousLesVoeux', 'PersonneController::validerTousLesVoeux', 'personne_validerTousLesVoeux');


$router->routeList['restrict']['respUE'][] =   new Route('GET', '/respUE', 'respUEController::home', 'respUE_home');
$router->routeList['restrict']['respUE'][] =   new Route('GET', '/respUE/liste1', 'respUEController::liste1', 'respUE_liste1');
$router->routeList['restrict']['respUE'][] =   new Route('GET', '/respUE/doReloadVoeuxListe1/[i:id]', 'respUEController::doReloadVoeuxListe1', 'respUE_doReloadVoeuxListe1');
$router->routeList['restrict']['respUE'][] =   new Route('POST', '/respUE/doSaveVoeuxListe1', 'respUEController::doSaveVoeuxListe1', 'respUE_doSaveVoeuxListe1');
$router->routeList['restrict']['respUE'][] =   new Route('GET', '/respUE/liste2', 'respUEController::liste2', 'respUE_liste2');


$router->routeList['restrict']['respDomaine'][] =   new Route('GET', '/respDomaine', 'respDomaineController::home', 'respDomaine_home');
$router->routeList['restrict']['respDomaine'][] =   new Route('GET', '/respDomaine/listePersonnes/[i:id]', 'respDomaineController::listePersonnes', 'respDomaine_listePersonnes');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/respDomaine/listePersonnes/[i:id]', 'respDomaineController::listePersonnes', 'bureauRHE_listePersonnes');
$router->routeList['restrict']['respDomaine'][] =   new Route('GET', '/respDomaine/listeEnseignements/[i:id]', 'respDomaineController::listeEnseignements', 'respDomaine_listeEnseignements');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/respDomaine/listeEnseignements/[i:id]', 'respDomaineController::listeEnseignements', 'bureauRHE_listeEnseignements');


$router->routeList['restrict']['respParcours'][] =   new Route('GET', '/respParcours', 'respParcoursController::home', 'respParcours_home');
$router->routeList['restrict']['respDiplome'][] =   new Route('GET', '/respDiplome', 'respDiplomeController::home', 'respDiplome_home');

$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE', 'bureauRHEController::home', 'bureauRHE_home');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listeMaquettes', 'bureauRHEController::listeMaquettes', 'bureauRHE_listeMaquettes');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listeDetailsCharges', 'bureauRHEController::listeDetailsCharges', 'bureauRHE_listeDetailsCharges');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listeSansAttache', 'bureauRHEController::listeSansAttache', 'bureauRHE_listeSansAttache');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/servicesEnseignant', 'bureauRHEController::servicesEnseignant', 'bureauRHE_servicesEnseignant');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listResponsables', 'bureauRHEController::listResponsables', 'bureauRHE_listResponsables');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/tousVoeux', 'bureauRHEController::tousVoeux', 'bureauRHE_tousVoeux');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listeDomaines', 'bureauRHEController::ListeDomaines', 'bureauRHE_listeDomaines');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listeStatuts', 'bureauRHEController::ListeStatuts', 'bureauRHE_listeSatuts');

$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listeAssociationsEnseignementDomaines', 'bureauRHEController::listeAssociationsEnseignementDomaines', 'bureauRHE_listeAssociationsEnseignementDomaines');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET|POST', '/bureauRHE/majResponsables', 'bureauRHEController::majResponsables', 'bureauRHE_majResponsables');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET|POST', '/bureauRHE/majAttributairesReferentiel', 'bureauRHEController::majAttributairesReferentiel', 'bureauRHE_majAttributairesReferentiel');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET|POST', '/bureauRHE/majResponsables', 'bureauRHEController::majResponsables', 'bureauRHE_majResponsables');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listeSituations', 'GestionnaireController::listeSituations', 'bureauRHE_listeSituations');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/listeReferentiel', 'GestionnaireController::listeReferentiel', 'bureauRHE_listeReferentiel');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET|POST', '/bureauRHE/majAssociationsEnseignementDomaines', 'bureauRHEController::majAssociationsEnseignementDomaines', 'bureauRHE_majAssociationsEnseignementDomaines');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET|POST', '/bureauRHE/createPersonne', 'bureauRHEController::createPersonne', 'bureauRHE_createPersonne');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/bureauRHE/addPersonne/[a:uid]', 'bureauRHEController::addPersonne','bureauRHE_addPersonne');
$router->routeList['restrict']['bureauRHE'][] =   new Route('POST', '/bureauRHE/addPersonne/[a:uid]', 'bureauRHEController::doAddPersonne','bureauRHE_doAddPersonne');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET|POST', '/bureauRHE/createEnseignement', 'bureauRHEController::createEnseignement', 'bureauRHE_createEnseignement');



$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/maquettePlus/[i:id]', 'bureauRHEController::maquettePlus', 'bureauRHE_maquettePlus');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/respDomaine/listePersonnes/[i:id]', 'respDomaineController::listePersonnes', 'bureauRHE_listePersonnes');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/respDomaine/listeEnseignements/[i:id]', 'respDomaineController::listeEnseignements', 'bureauRHE_listeEnseignements');
$router->routeList['restrict']['bureauRHE'][] =   new Route('GET', '/statut/listePersonnes/[i:id]', 'bureauRHEController::listePersonnesParStatut', 'statut_listePersonnes');

$router->routeList['restrict']['RHEAdmin'][] =   new Route('GET', '/RHEAdmin', 'RHEAdminController::home', 'RHEAdmin_home');

// gestion des états des enseignements et des personnes vis à vis de l'édition du Tableau de service
$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/configEditionTSPersonne', 'AdminController::configEditionTSPersonne', 'admin_configEditionTSPersonne');
$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/configEditionTSEnseignement', 'AdminController::configEditionTSEnseignement', 'admin_configEditionTSEnseignement');
$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/setEditionTS/[a:entity]/[0|1:etat]', 'AdminController::setEditionTS', 'admin_setEditionTS');

// Nouvelle Année
$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/remise0Voeux/[confirm:action]?', 'AdminController::remise0Voeux', 'admin_remise0Voeux');
$router->routeList['restrict']['Admin'][] = new Route('GET', '/admin/transfertReports/[confirm:action]?', 'AdminController::transfertReports','admin_transfertReports');
$router->routeList['restrict']['Admin'][] = new Route('GET|POST', '/admin/migrationSituations/[confirm:action]?', 'AdminController::migrationSituations','admin_migrationVoeux');
$router->routeList['restrict']['Admin'][] = new Route('GET', '/admin/migrationReferentiels/[confirm:action]?', 'AdminController::migrationReferentiels','admin_migrationReferentiels');

// Gestion
$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/forceAValider/[confirm:action]?', 'AdminController::forceAValider', 'admin_forceAValider');

// ficheECUE
$router->routeList['withAuth']['api']['ficheECUE_docUpload']   = new Route('POST',    '/ficheECUE/DocUpload/[*:uid]/[*:name]',           'FicheECUEController::docUpload', 'ficheECUE_docUpload');
$router->routeList['withAuth']['api']['ficheECUE_gestion']     = new Route('GET',     '/ficheECUE/gestion',                     'FicheECUEController::gestion',   'ficheECUE_gestion'  );
$router->routeList['withAuth']['api']['ficheECUE_delete']      = new Route('GET',     '/ficheECUE/delete/[*:name]',             'FicheECUEController::delete',    'ficheECUE_delete'   );
$router->routeList['withAuth']['api']['ficheECUE_destroy']     = new Route('GET',     '/ficheECUE/destroy/[*:name]',            'FicheECUEController::destroy',   'ficheECUE_destroy'  );
$router->routeList['withAuth']['api']['ficheECUE_duplicate']   = new Route('GET',     '/ficheECUE/duplicate/[*:name]',          'FicheECUEController::duplicate', 'ficheECUE_duplicate');
$router->routeList['withAuth']['api']['ficheECUE_getDoc']      = new Route('GET|POST','/ficheECUE/getDoc/[*:hex]?',             'FicheECUEController::getDoc',    'ficheECUE_getDoc'   );
$router->routeList['withAuth']['api']['ficheECUE_renameDoc']   = new Route('GET|POST','/ficheECUE/renameDoc/[*:hex]?',          'FicheECUEController::renameDoc', 'ficheECUE_renameDoc');
$router->routeList['withAuth']['api']['ficheECUE_deleteDoc']   = new Route('GET|POST','/ficheECUE/deleteDoc/[*:hex]?',          'FicheECUEController::deleteDoc', 'ficheECUE_deleteDoc');
$router->routeList['withAuth']['api']['ficheECUE']             = new Route('GET|POST','/ficheECUE/fiche/[*:uid]/[*:name]',      'FicheECUEController::ficheECUE', 'ficheECUE'          );

$router->routeList['restrict']['bureauRHE']['ficheECUE_liste']  = new Route('GET',     '/ficheECUE/liste',                       'FicheECUEController::liste',     'ficheECUE_liste'    );


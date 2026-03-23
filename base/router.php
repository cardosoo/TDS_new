<?php
namespace base;

use \TDS\Route;

include '../zeroUP/router.php';

$app = \TDS\App::get();

$app::$router->setNamespace('\\'.__NAMESPACE__.'\\Controllers\\');


$app::$router->routeList['restrict']['Admin']['test1']= new Route('GET','/test/test1', 'TestController::test1', 'test_1');
$app::$router->routeList['restrict']['Admin']['test2']= new Route('GET','/test/test2', 'TestController::test2', 'test_2');
$app::$router->routeList['restrict']['Admin']['test3']= new Route('GET','/test/test3', 'TestController::test3', 'test_3');

$app::$router->routeList['restrict']['Admin']['info']= new Route('GET','/test/info', 'TestController::phpinfo', 'test_phpinfo');

$app::$router->routeList['public']['api']['candidatureME_docUpload']= new Route('POST','/candidatureME/DocUpload/[*:uid]', 'CandidatureController::docUpload', 'candidatureME_docUpload');

$app::$router->routeList['public']['api']['candidatureME_docDownload']= new Route('GET','/candidatureME/getDoc/[*:uid]/[*:filename]', 'CandidatureController::getDoc', 'candidatureME_getDoc');
$app::$router->routeList['public']['api']['candidatureME_liste']= new Route('GET','/candidatureME/liste', 'CandidatureController::liste', 'candidatureME_liste');

$app::$router->routeList['public']['api']['candidatureME']= new Route('GET|POST','/candidatureME/[*:uid]?', 'CandidatureController::candidatureME', 'candidatureME');

$app::$router->routeList['public']['api']['candidatureME_getDoc']= new Route('GET|POST','/documentsME/getDoc/[*:hex]?', 'CandidatureController::getDoc', 'documentsME_getDoc');
$app::$router->routeList['public']['api']['candidatureME_renameDoc']= new Route('GET|POST','/documentsME/renameDoc/[*:hex]?', 'CandidatureController::renameDoc', 'documentsME_renameDoc');
$app::$router->routeList['public']['api']['candidatureME_deleteDoc']= new Route('GET|POST','/documentsME/deleteDoc/[*:hex]?', 'CandidatureController::deleteDoc', 'documentsME_deleteDoc');


$app::$router->routeList['public']['api']['planning']= new Route('GET','/planning/detailsAPI/[*:cursus]/[*:composante]/[i:perdiode]/[i:serie]', 'PlanningController::detailsAPI', 'api_planning_detailsAPI');
$app::$router->routeList['public']['api']['listingServices']= new Route('GET','/api/listingServices', 'APIController::listingServices', 'api_listingServices');
$app::$router->routeList['public']['api']['listingServicesOSE']= new Route('GET','/api/listingServicesOSE', 'APIController::listingServicesOSE', 'api_listingServicesOSE');
$app::$router->routeList['public']['api']['allEcueOSE']= new Route('GET','/api/allEcuesOSE/[i:year]', 'APIController::allEcuesOSE', 'api_allEcuesOSE');
$app::$router->routeList['public']['api']['activeUserList']= new Route('GET','/api/activeUserList/[i:year]', 'APIController::activeUserList', 'api_activeUserList');
$app::$router->routeList['public']['api']['activeTeachingList']= new Route('GET','/api/activeTeachingList/[i:year]', 'APIController::activeTeachingList', 'api_activeTeachingList');
$app::$router->routeList['public']['api']['activeFoncRef']= new Route('GET','/api/activeFoncRef/[i:year]', 'APIController::activeFoncRef', 'api_activeFoncRef');
$app::$router->routeList['public']['api']['activeSituationList']= new Route('GET','/api/activeSituationList/[i:year]', 'APIController::activeSituationList', 'api_activeSituationList');
$app::$router->routeList['public']['api']['getEmail']= new Route('GET','/api/getEmail/[i:id]', 'APIController::getEmail', 'api_getEmail');
$app::$router->routeList['public']['api']['isUIDInBase']= new Route('GET','/api/isUIDInBase/[a:uid]', 'APIController::isUIDInBase', 'api_isUIDInBase');

// l'application OSE // c'est seulement pour faire les importations
$app::$router->routeList['public']['api']['structOSEEtape']= new Route('GET','/api/structOSEEtape/[*:code]', 'APIController::structOSEEtape', 'api_structOSEEtape');
$app::$router->routeList['public']['api']['structOSEEcue']= new Route('GET','/api/structOSEEcue/[*:code]', 'APIController::structOSEEcue', 'api_structOSEEcue');




$app::$router->routeList['private']['personne'] = [
    new Route('GET','/personne/[i:id]', 'PersonneController::fiche', 'personne_fiche'),
    new Route('GET','/personne/historique/[i:id]/[i:year]', 'PersonneController::historique', 'personne_historique'),
    new Route('GET','/personne/historique/[i:id]', 'PersonneController::historiqueComplet', 'personne_historique_complet'),
    new Route('GET|POST','/personne/edit/[i:id]', 'PersonneController::edit', 'personne_edit'),
    new Route('GET|POST','/personne/details/[i:id]', 'PersonneController::saveDetails', 'personne_saveDetails'),
    new Route('GET|POST','/personne/ldap/numetu', 'PersonneController::searchLDAPNumetu', 'personne_searchLDAP_numetu'),
    new Route('GET|POST','/personne/ldap', 'PersonneController::searchLDAP', 'personne_searchLDAP'),
    new Route('GET|POST','/personne/saveStages/[i:id]', 'PersonneController::saveStages', 'personne_saveStages'),
];

$app::$router->routeList['private']['search'] = [
    new Route('GET|POST','/qui/[*:who]?', 'RechercheController::search', 'recherche_search'),
    new Route('GET|POST','/quoi/[*:what]?', 'RechercheController::what', 'recherche_what'),
    new Route('POST','/rechercheEnseignement', 'RechercheController::rechercheEnseignement', 'post_recherche_what'),
    new Route('POST','/selectMaquetteJSON', 'RechercheController::selectMaquetteJSON', 'post_select_maquette'),
    new Route('POST','/selectEtapeJSON', 'RechercheController::selectEtapeJSON', 'post_select_etape'),
];

$app::$router->routeList['private']['enseignement'] = [
    new Route('GET','/enseignement/[i:id]', 'EnseignementController::fiche', 'enseignement_fiche'), 
    new Route('GET','/enseignement/historique/[i:id]/[i:Y]', 'EnseignementController::historique', 'enseignement_historique'),
    new Route('GET','/enseignement/voeu/[i:id]', 'EnseignementController::voeu', 'enseignement_voeu'),
    new Route('POST','/enseignement/voeu/[i:id]', 'EnseignementController::addOrChangeVoeu', 'post_enseignement_voeu'),
    new Route('DELETE','/enseignement/voeu/[i:id]', 'EnseignementController::deleteVoeu', 'delete_enseignement_voeu'),
    new Route('POST','/enseignement/saveSyllabus/[i:id]', 'EnseignementController::saveSyllabus', 'enseignement_saveSyllabus'),
    new Route('GET','/enseignement/editBesoins/[i:id]', 'EnseignementController::editBesoins', 'enseignement_editBeoins'),
    new Route('POST','/enseignement/editBesoins/[i:id]', 'EnseignementController::saveEditBesoins', 'enseignement_saveEditBeoins'),
];

$app::$router->routeList['private']['maquette'] = [
    new Route('GET', '/maquette/[i:N]', 'MaquetteController::fiche', 'maquette_fiche'),
];


$app::$router->routeList['private']['composante'] = [
    new Route('GET', '/composante/[i:N]', 'ComposanteController::fiche', 'composante_fiche'),
];



$app::$router->routeList['private']['comments'] = [
    new Route('POST', '/saveComment/[a:entity]/[i:id]', 'AdminController::saveComment', 'admin_saveComment'),
    new Route('POST', '/maskComment/[a:entity]/[i:id]', 'AdminController::maskComment', 'admin_maskComment'),
    new Route('POST', '/delComment/[a:entity]/[i:id]', 'AdminController::delComment', 'admin_delComment'),
    new Route('POST', '/addComment/[a:entity]/[i:id]', 'AdminController::addComment', 'admin_addComment'),
];

$app::$router->routeList['private']['stages']=[
    new Route('POST', '/saveStages/[i:id]', 'PersonneController::saveStages', 'personne_saveStages'),
];

// Listes 
$app::$router->routeList['private']['Listes'][] =   new Route('GET', '/listes/foncRef/[i:id]', 'ListesController::foncRef', 'listes_foncRef');

// Admin
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/mailer', 'MailerController::mailer', 'mailer_home');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/mail/[i:id]', 'MailerController::mail', 'mailer_mail');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/sendmail/[i:id]', 'MailerController::sendmail', 'mailer_sendmail');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/sendReport', 'MailerController::sendReport', 'mailer_sendreport');
$app::$router->routeList['restrict']['Admin']['ficheHTML'] = new Route('GET','/getFicheFoireHTML', 'RechercheController::getFicheFoireHTML', 'recherche_what_getFicheFoireHTML');
$app::$router->routeList['restrict']['Admin']['fichePDF'] = new Route('GET','/getFicheFoirePDF', 'RechercheController::getFicheFoirePDF', 'recherche_what_getFicheFoirePDF');
$app::$router->routeList['restrict']['Admin']['trombinoscope'] = new Route('GET','/admin/trombinoscope', 'PersonneController::trombinoscope', 'admin_trombinoscope');

$app::$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/maquette', 'MaquetteController::editList', 'maquette_editList');
$app::$router->routeList['restrict']['Admin'][] =   new Route('POST', '/admin/maquette', 'MaquetteController::saveList', 'maquette_saveList');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/maquette/[i:id]', 'MaquetteController::edit', 'maquette_edit');
$app::$router->routeList['restrict']['Admin'][] =   new Route('POST', '/admin/maquette/[i:id]', 'MaquetteController::save', 'maquette_save');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/CRUD/search/[*:what]', 'RechercheController::crudSearch', 'crud_search');

$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/allVoeux', 'AdminController::allVoeux', 'admin_all_voeux');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/allVoeux2', 'AdminController::allVoeux2', 'admin_all_voeux2');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/noVoeu', 'AdminController::noVoeu', 'admin_no_voeu');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/chargesBesoins', 'AdminController::chargesBesoins', 'admin_charges_besoins');

$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/listCorrespondants', 'AdminController::listCorrespondants', 'admin_list_correspondants');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/autoCorrespondants', 'AdminController::autoCorrespondants', 'admin_auto_correspondants');

$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/inactiveVoeuxBlancs', 'AdminController::inactiveVoeuxBlancs', 'admin_unactive_voeux_blancs');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/deleteVoeuxBlancs', 'AdminController::deleteVoeuxBlancs', 'admin_delete_voeux_blancs');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/infoEtapes', 'AdminController::infoEtapes', 'admin_infoEtapes');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET|POST', '/admin/deleteVoeuxInactif', 'AdminController::deleteVoeuxInactif', 'admin_deleteVoeuxInactif');


$app::$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/listComments/[a:entity]', 'AdminController::listComments', 'admin_listComments');

$app::$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/verifECUE', 'AdminController::verifECUE', 'admin_verifECUE');
$app::$router->routeList['restrict']['Admin'][] =   new Route('GET', '/admin/vacatairesSansVoeu', 'AdminController::vacatairesSansVoeu', 'admin_vacatairesSansVoeu');

// superAdmin
$app::$router->routeList['restrict']['SuperAdmin'][] =  new Route('GET', '/superAdmin', 'SuperAdminController::home', 'superadmin_home');
$app::$router->routeList['restrict']['SuperAdmin'][] = new Route('GET|POST', '/admin/searchLDAP', 'SuperAdminController::searchLDAP','superadmin_searchLDAP');
$app::$router->routeList['restrict']['SuperAdmin'][] = new Route('GET', '/admin/addFromLDAP/[a:uid]', 'SuperAdminController::addFromLDAP','superadmin_addFromLDAP');
$app::$router->routeList['restrict']['SuperAdmin'][] = new Route('GET', '/admin/updateOSE_FromLDAP', 'SuperAdminController::updateOSE_FromLDAP','superadmin_updateOSE_FromLDAP');
$app::$router->routeList['restrict']['SuperAdmin'][] = new Route('GET', '/admin/importServicesOSE', 'SuperAdminController::importServicesOSE','superadmin_importServicesOSE');
$app::$router->routeList['restrict']['SuperAdmin'][] = new Route('GET', '/admin/importReferentielOSE', 'SuperAdminController::importReferentielOSE','superadmin_importReferentielOSE');
$app::$router->routeList['restrict']['SuperAdmin'][] = new Route('GET', '/admin/importRespUE', 'SuperAdminController::importRespUE','superadmin_importRespUE');
$app::$router->routeList['restrict']['SuperAdmin'][] = new Route('GET', '/admin/importStructure', 'SuperAdminController::importStructure','superadmin_importStructure');


// Gestionnaire
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire', 'GestionnaireController::home', 'gestionnaire_home');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire/utilisationServices', 'GestionnaireController::utilisationServices', 'gestionnaire_utilisationServices');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire/comparaisonOSE', 'GestionnaireController::comparaisonOSE', 'gestionnaire_comparaisonOSE');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire/listeSituations', 'GestionnaireController::listeSituations', 'gestionnaire_listeSituations');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire/listeReferentiel', 'GestionnaireController::listeReferentiel', 'gestionnaire_listeReferentiel');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire/compareEcueFromFoireToOse', 'GestionnaireController::compareEcueFromFoireToOse', 'gestionnaire_compareEcueFromFoireToOse');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire/listeUtilisateursLDAP', 'GestionnaireController::listeUtilisateursLDAP', 'gestionnaire_listeUtilisateursLDAP');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire/listeUtilisateursActifs', 'GestionnaireController::listeUtilisateursActifs', 'gestionnaire_listeUtilisateursActifs');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/gestionnaire/exportOSEUserList', 'GestionnaireController::exportOSEUserList', 'gestionnaire_exportOSEUserList');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('POST', '/gestionnaire/exportOSE_SERVICE', 'GestionnaireController::exportOSE_SERVICE', 'gestionnaire_exportOSE_SERVICE');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('POST', '/gestionnaire/exportOSE_VOLUME_HORAIRE', 'GestionnaireController::exportOSE_VOLUME_HORAIRE', 'gestionnaire_exportOSE_VOLUME_HORAIRE');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('POST', '/gestionnaire/exportOSE', 'GestionnaireController::exportOSE', 'gestionnaire_exportOSE');


// OSE
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/OSE/comparaison', 'OSEController::comparaison', 'OSE_comparaison');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/OSE/comparaisonFromOSE', 'OSEController::comparaisonFromOSE', 'OSE_comparaisonFromOSE');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/OSE/comparaisonFromDB', 'OSEController::comparaisonFromDB', 'OSE_comparaisonFromDB');
$app::$router->routeList['restrict']['Gestionnaire'][] =   new Route('GET', '/OSE/bilanPersonneList', 'OSEController::bilanPersonneList', 'OSE_bilanPersonneList');

// Planning
$app::$router->routeList['restrict']['Planning'][] =   new Route('GET', '/planning', 'PlanningController::home', 'planning_home');
$app::$router->routeList['restrict']['Planning'][] =   new Route('GET', '/planning/detailsEnseignements', 'PlanningController::detailsEnseignements', 'planning_detailsEnseignements');


// respEtape
$app::$router->routeList['private']['respEtape'][] =   new Route('GET', '/role/respEtape', 'respEtapeController::home', 'respEtape_home');

// respAdmin
$app::$router->routeList['private']['respAdmin'][] =   new Route('GET', '/role/respAdmin', 'respAdminController::home', 'respAdmin_home');


// structure
$app::$router->routeList['private']['Structure'][] =   new Route('GET', '/structure', 'StructureController::home', 'structure_home');
$app::$router->routeList['private']['Structure'][] =   new Route('GET', '/structure/structure/[*:noom]', 'StructureController::structure', 'structure_structure');
$app::$router->routeList['private']['Etape'][] =   new Route('GET', '/structure/etape/[a:code]', 'StructureController::etape', 'structure_etape');
$app::$router->routeList['private']['ECUE'][] =   new Route('GET', '/structure/ecue/[a:code]', 'StructureController::ecue', 'structure_ecue');
$app::$router->routeList['private']['ECUE'][] =   new Route('GET', '/structure/addEnseignement/[a:code]', 'StructureController::addEnseignement', 'structure_addEnseignement');
$app::$router->routeList['private']['Structure'][] =   new Route('GET', '/admin/structure/importExtraction', 'StructureController::importExtraction', 'structure_importExtraction');
$app::$router->routeList['private']['Structure'][] =   new Route('GET', '/admin/structure/importMutualisation', 'StructureController::importMutualisation', 'structure_importMutualisation');
$app::$router->routeList['private']['Structure'][] =   new Route('GET', '/admin/structure/importAjout', 'StructureController::importAjout', 'structure_importAjout');
$app::$router->routeList['private']['Structure'][] =   new Route('GET', '/admin/structure/importStructure', 'StructureController::importimportStructure', 'structure_importStructure');
$app::$router->routeList['private']['Structure'][] =   new Route('GET', '/admin/structure/correctionEtape', 'StructureController::correctionEtape', 'structure_correctionEtape');


// vacation Vacation
$app::$router->routeList['private']['vacation']['vacation_docUpload']   = new Route('POST',    '/vacation/DocUpload/[*:id]',     'VacationController::docUpload', 'vacation_docUpload');
$app::$router->routeList['private']['vacation']['vacation_gestion']     = new Route('GET',     '/vacation/gestion',              'VacationController::gestion',   'vacation_gestion'  );
$app::$router->routeList['private']['vacation']['vacation_deleteFiche'] = new Route('GET',     '/vacation/deleteFiche/[*:id]',    'VacationController::deleteFiche', 'vacation_deleteFiche'   );
$app::$router->routeList['private']['vacation']['vacation_deleteFichePermanent'] = new Route('GET',     '/vacation/deleteFichePermanent/[*:id]',    'VacationController::deleteFichePermanent', 'vacation_deleteFichePermanent'   );
$app::$router->routeList['private']['vacation']['vacation_destroy']     = new Route('GET',     '/vacation/destroy/[*:id]',       'VacationController::destroy',   'vacation_destroy'  );
$app::$router->routeList['private']['vacation']['vacation_duplicate']   = new Route('GET',     '/vacation/duplicate/[*:id]',     'VacationController::duplicate', 'vacation_duplicate');
$app::$router->routeList['private']['vacation']['vacation_getDoc']      = new Route('GET|POST','/vacation/getDoc/[*:hex]?',      'VacationController::getDoc',    'vacation_getDoc'   );
$app::$router->routeList['private']['vacation']['vacation_renameDoc']   = new Route('GET|POST','/vacation/renameDoc/[*:hex]?',   'VacationController::renameDoc', 'vacation_renameDoc');
$app::$router->routeList['private']['vacation']['vacation_deleteDoc']   = new Route('GET|POST','/vacation/deleteDoc/[*:hex]?',   'VacationController::deleteDoc', 'vacation_deleteDoc');
$app::$router->routeList['private']['vacation']['vacation_fiche']       = new Route('GET|POST','/vacation/fiche/[*:id]',         'VacationController::fiche',     'vacation_fiche');
$app::$router->routeList['private']['vacation']['vacation_liste']       = new Route('GET'     ,'/vacation/liste',                'VacationController::liste',     'vacation_liste');
$app::$router->routeList['private']['vacation']['vacation_listeInDB']   = new Route('GET'     ,'/vacation/listeInDB',            'VacationController::listeInDB', 'vacation_listeInDB');
$app::$router->routeList['private']['vacation']['vacation_importFromText']= new Route('GET|POST','/vacation/importFromText',     'VacationController::importFromText', 'vacation_importFromText'          );
$app::$router->routeList['private']['vacation']['vacation_createFiche']   = new Route('GET'     ,'/vacation/createFiche',        'VacationController::createFiche', 'vacation_createFiche');
$app::$router->routeList['private']['vacation']['vacation_devalide']    = new Route('GET'  ,'/vacation/devalide/[*:id]',    'VacationController::devalide',    'vacation_devalide');
$app::$router->routeList['private']['vacation']['vacation_dearchive']    = new Route('GET'  ,'/vacation/dearchive/[*:id]',    'VacationController::dearchive',    'vacation_dearchive');
$app::$router->routeList['private']['vacation']['vacation_downloadValide']    = new Route('GET'  ,'/vacation/downloadValide',    'VacationController::downloadValide',    'vacation_downloadValide');
$app::$router->routeList['private']['vacation']['vacation_downloadValideAndArchive']    = new Route('GET'  ,'/vacation/downloadValideAndArchive',    'VacationController::downloadValideAndArchive',    'vacation_downloadValideAndArchive');


$app::$router->routeList['private']['vacation']['vacation_getInfoFromCodeECUEList']= new Route('GET|POST','/vacation/getInfoFromCodeECUEList',     'VacationController::getInfoFromCodeECUEList', 'vacation_getInfoFromCodeECUEList');

$app::$router->routeList['private']['vacation']['vacation_getEtapeListJSON']   = new Route('POST','/vacation/getEtapeListJSON',      'VacationController::getEtapeListJSON', 'vacation_getEtapeListJSON');
$app::$router->routeList['private']['vacation']['vacation_getEtapeList']   = new Route('GET','/vacation/getEtapeList/[i:structureId]/[i:cursusId]',      'VacationController::getEtapeList', 'vacation_getEtapeList');
$app::$router->routeList['private']['vacation']['vacation_search']   = new Route('POST','/vacation/search',      'VacationController::search', 'vacation_search');
$app::$router->routeList['private']['vacation']['vacation_infoECUE']   = new Route('GET','/vacation/infoECUE/[a:code]', 'VacationController::infoECUE', 'vacation_infoECUE');


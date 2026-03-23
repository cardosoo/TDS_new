<?php
namespace zeroUP;

use \TDS\Route;

$app = \TDS\App::get();

$app::$router->setNamespace('\\'.__NAMESPACE__.'\\Controllers\\');

$app::$router->routeList = [
    'public' => [ // zone publique sans authentification
      'gen' => [
        new Route('GET', '', 'GenController::home', 'home'),
        new Route('GET', '/', 'GenController::home', 'home2'),
        new Route('GET', '/status/[html|raw:format]', 'GenController::status', 'gen_status'),
        new Route('GET','/texte/documents/[*:document]', 'GenController::texte_documents'),
        new Route('GET','/texte/[*:t]', 'GenController::texte', 'texte'),
        new Route('GET', '/setCurrentYear/[i:year]', 'GenController::setCurrentYear', 'gen_setCurrentYear'),
        new Route('GET', '/setCurrentYear/[i:year]', 'GenController::setCurrentYear', 'gen_setCurrentYear'),
    ],
      'auth' => [
        new Route('GET','/directLink/[*:hex]', 'AuthController::directLink', 'directLink'),
        // nouveau, pour utliser le cas externe sur la même machine...
        new Route('GET|POST', '/cas/logout/[a:service]', 'ExternController::caslogout', 'casextern_logout'),
        new Route('GET|POST', '/cas/login/[a:service]/[**:uri]', 'ExternController::caslogin', 'casextern_login'),
        new Route('GET', '/ldap/[**:search]', 'ExternController::ldapsearch', 'ldapextern_search'),

        // ['GET|POST', '/cas/logout/[a:service]', 'cas_externe/logout.php', 'cas_logout'],
        //['GET|POST', '/cas/login/[a:service]/[**:uri]', 'cas_externe/login.php', 'cas_login'],
        //['GET', '/jupy/test-ip', 'jupy/test-ip.php', 'jupy_test-ip'],
        //['GET', '/jupy/[**:bypass]', 'jupy/bypass.php', 'jupy_bypass'],
        //['GET', '/ldap/[**:search]', 'ldap_externe/search.php', 'ldap_login'],
    ],
      'test' => [
      ]
  ],
    'withAuth' => [ // zone avec authentification 
      'auth' => [
        new Route('GET','/auth/login', 'AuthController::login', 'login'),   // c'est bizarre, mais c'est un moyen simple de déclencher l'authentification
        new Route('GET','/alive', 'AuthController::alive', 'alive'),        // c'est bizarre, mais c'est un moyen simple de déclencher l'authentification
        new Route('GET','/auth/logout', 'AuthController::logout', 'logout'),
      ],
      'user' => [
      ],
      'search' => [
      ],
    ],
    'private' => [ // zone privée avec authentification et présence dans la base de données
    ],
    'restrict' => [ // zone à accès restreint en fonction des droits de l'utilisation authentifié
      'Admin' => [
        new Route('GET','/admin', 'AdminController::home','admin_home'),
      ],
    ],
  ];
  

  $app::$router->setNamespace('\\TDS\\');
  
  $app::$router->routeList['restrict']['SuperAdmin'] = [
    new Route('GET', '/CRUD/L/[a:entityName]',  'Crud::listAllJSON', 'crud_list_all_json'),
    new Route('GET', '/CRUD/LI/[a:entityName]', 'Crud::listAll', 'crud_list_all'),
    new Route('POST', '/CRUD/HE', 'Crud::entity_history', 'crud_entity_history'),
    new Route('POST', '/CRUD/HF', 'Crud::entity_field_history', 'crud_entity_field_history'),
    new Route('POST', '/CRUD/HL', 'Crud::entity_links_history', 'crud_entity_links_history'),
    new Route('POST', '/CRUD/HMM', 'Crud::entity_manyToMany_history', 'crud_entity_many_to_many_history'),

    new Route('GET', '/CRUD/', 'Crud::home', 'crud_home'),   // création d'une entité
    new Route('GET', '/CRUD/[a:entityName]', 'Crud::createEntity', 'crud_create_entity'),   // création d'une entité
    new Route('GET', '/CRUD/[a:entityName]/[a:from]/[i:fromId]', 'Crud::createManyToMany', 'crud_create_manyToMany'), // création d'une association manyToMany
    new Route('GET', '/CRUD/[a:entityName]/[i:id]', 'Crud::read', 'crud_read'),
    new Route('PUT', '/CRUD/[a:entityName]', 'Crud::doCreateEntity', 'crud_do_create_entity'), // création d'une entité
    new Route('PUT', '/CRUD/[a:entityName]/[i:id]', 'Crud::doCreateEntity','crud_put_do_create_entity'), // création d'une entité
    new Route('PATCH', '/CRUD/[a:entityName]/[i:id]', 'Crud::updateEntity','crud_patch_update_entity'), // mise à jour d'une entité
    new Route('PATCH', '/CRUD/[a:entityName]/[a:from]/[i:fromId]', 'Crud::updateManyToMany','crud_patch_update_manyToMany'), // création/mise à jour d'une association manyToMany
    new Route('PUT', '/CRUD/[a:entityName]/[a:from]/[i:fromId]', 'Crud::updateManyToMany', 'crud_put_create_manyToMany'), // création d'une association manyToMany
    new Route('DELETE', '/CRUD/[a:entityName]/[i:id]', 'Crud::delete', 'crud_delete'),

  ];  
  
<?php
namespace zeroU;

use \TDS\Route;

$CN = '\\'.__NAMESPACE__.'\\Controllers\\';


App::$router->routeList = [
    'public' => [ // zone publique sans authentification
      'gen' => [
        new Route('GET', '', $CN.'Gen::home', 'home'),
        new Route('GET','/texte/[*:texte]', $CN.'Gen::texte'),
      ],
      'auth' => [
        new Route('GET','/directLink/[*:hex]', $CN.'Auth::directLink'),
      ],
      'test' => [
      ]
  ],
    'withAuth' => [ // zone avec authentification 
      'auth' => [
        new Route('GET','/auth/login', $CN.'Auth::login'),   // c'est bizarre, mais c'est un moyen simple de déclencher l'authentification
        new Route('GET','/alive', $CN.'Auth::alive'),        // c'est bizarre, mais c'est un moyen simple de déclencher l'authentification
        new Route('GET','/auth/logout', $CN.'Auth::logout'),
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
          new Route('GET','/admin', $CN.'Admin::home'),
      ],
      'SuperAdmin' => [
      ],
    ],
  ];  

  
  // pour le CRUD
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
  
  
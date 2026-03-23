<?php
namespace zeroUP;

use \TDS\Model\Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;
use \TDS\Model\ManyToMany;
use \TDS\Model\View;

if (! isset(Model::$appName)){
    Model::$appName = __NAMESPACE__;
}

Model::$parentApp=null; 
Model::$idName = 'id';

$role = Model::addEntity(new Entity('Role'));
$role->addField(new Field('name', Field::STRING));

$user = Model::addEntity(new Entity('User'));
$user->addField(new Field('uid', Field::STRING));
$user->addField(new Field('name', Field::STRING));

$actAs = Model::addEntity(new ManyToMany('actAs', $user, $role));


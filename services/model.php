<?php
namespace services;

use \TDS\Model\Model;
use \TDS\Model\Entity;
use \TDS\Model\Field;
use \TDS\Model\ManyToMany;
use \TDS\Model\View;

if (! isset(Model::$appName)){
    Model::$appName = __NAMESPACE__;
}

include '../foire/model.php';
Model::$parentApp = 'foire';
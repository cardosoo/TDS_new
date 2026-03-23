<?php

namespace TDS\Model;

use TDS\Model\Entity;

class View extends Entity {

    public function buildForModel($namespace){
        $app = \TDS\App::get();
        $modelName = $this->getModelName();
        $appName = $app::$appName;
        $extends = $namespace === $this->namespace ? 'View' : '\\'.$this->namespace.'\\Model\\'.$this->name;
        return "<?php
namespace {$appName}\\Model;

use \\TDS\\View;
use \\TDS\\App;

class {$this->name} extends {$extends} implements \\Model\\{$modelName}interface_ {
    use \\Model\\{$modelName};

}        
        ";
    }


    public function getSQL($schema=""){
        $sql = "
-- ****************************** {$this->name} as {$schema}{$this->dbName}
-- ***** C'est une VUE alors je ne sais pas encore faire...";
        return $sql;
    }


}
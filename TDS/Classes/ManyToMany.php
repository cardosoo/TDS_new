<?php

namespace TDS;

abstract class ManyToMany extends Table {
    
    
    public function getCrudAddLink($linkText){
/*
var_dump('');
var_dump($this);
var_dump($linkText);
*/
        $entityName = self::getEntityName();
        $tmp = \explode('\\',$entityName);
        $eName = \end( $tmp);

        return "<a href='/CRUD/{$eName}'>{$linkText}</a>";
    }
}
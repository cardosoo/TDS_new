<?php
namespace zeroU;

class App extends \TDS\App {


    public static function loadFromUid($uid){
        $app = \TDS\App::get();
        
        return $app::NS('Personne')::loadOneWhere("uid = '{$uid}'");
    }

    public static function getRoleList($user){
        $roleList = [];
        foreach($user->actasList as $role){
            $roleList[$role->role->nom]=true;
        }
        return $roleList;
    }
}
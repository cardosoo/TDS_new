<?php
namespace zeroUP;

class App extends \TDS\App {

    public static function loadFromUid(string $uid){
        $app = \TDS\App::get();
        $UserNS = $app::NS('User');
        return $UserNS::loadOneWhere("uid = '{$uid}'");
    }

    public static function getRoleList(\base\Model\Personne $user){
        $roleList = [];
        foreach($user->actasList as $role){
            $roleList[$role->role->name]=true;
        }
        return $roleList;
    }
}
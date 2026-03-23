<?php
namespace tssdv\Controllers;


class ExternController extends \TDS\Controller {

    public static function caslogin($service, $uri){
        \phpCAS::setLogger();
        \phpCAS::client(CAS_VERSION_2_0, 'auth.u-paris.fr', 443, '/idp/profile/cas/', 'https://ts.sdv.u-paris.fr');
        \phpCAS::setNoCasServerValidation();
        \phpCAS::forceAuthentication();
        $user = \phpCAS::getUser();
        
        // file_put_contents("cas_externe/log/{$service}_{$ticket}.log",$user);
        $uri = hex2bin($uri);
        
        //file_put_contents("cas_externe/log/cas.log", date(DATE_RFC2822)."\t{$user}\t{$service}\t{$uri}\n",FILE_APPEND);
        $p = hash('ripemd160', $user);
        $params = parse_url($uri, PHP_URL_QUERY);
        if (""==$params){
            header("Location: {$uri}?user={$user}&u={$p}");
        } else {
            header("Location: {$uri}&user={$user}&u={$p}");
        }        
        exit();
    }

    public static function caslogout($service){
        \phpCAS::setLogger();
        \phpCAS::client(CAS_VERSION_2_0, 'auth.u-paris.fr', 443, '/idp/profile/cas/', 'https://ts.sdv.u-paris.fr');
        \phpCAS::setNoCasServerValidation();
        \phpCAS::forceAuthentication();
        \phpCAS::logout();

    }


}

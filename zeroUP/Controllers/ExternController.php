<?php
namespace zeroUP\Controllers;


class ExternController extends \TDS\Controller {

    public static function caslogin(string $service, string $uri){
        $app = \TDS\App::get();
        $appName = $app::$appName;

        $domaine = "https://foire.physique.u-paris.fr";

        if ($appName =='foire'){
            $domaine="https://foire.physique.u-paris.fr";
        }
        if ($appName =='tssdv'){
            $domaine="https://ts.sdv.u-paris.fr";
        }

        \phpCAS::setLogger();
        \phpCAS::client(CAS_VERSION_2_0, 'auth.u-paris.fr', 443, '/idp/profile/cas/', 'https://foire.physique.u-paris.fr');
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

    public static function caslogout(string $service){
        \phpCAS::setLogger();
        \phpCAS::client(CAS_VERSION_2_0, 'auth.u-paris.fr', 443, '/idp/profile/cas/', 'https://foire.physique.u-paris.fr');
        \phpCAS::setNoCasServerValidation();
        \phpCAS::forceAuthentication();
        \phpCAS::logout();

    }


    public static function ldapsearch(string $search){        
        $app = \TDS\App::get();

        $ldap = ldap_connect("".$app::$list["LDAP_HOST"].":". $app::$list["LDAP_PORT"]);
        if (!$ldap) {
            if (session_id() != "") {
                session_destroy();
            }
            die("Connexion impossible");
            return;
        }
        
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ldap, $app::$list["PASS_ADMIN"], $app::$list["PASS_PASS"]);
        if (!$r) {
            die(ldap_error($ldap) . " while binding");
            if (session_id() != "") {
                session_destroy();
            }
            ldap_close($ldap);
            return;
        }
        
        $search = json_decode(hex2bin($search), true);

        if (is_array($search)){
            @$sr = ldap_list($ldap, $app::$list["DATASPATH"], $search['filter'], $search['attributes'], 0, $search['limit']);
            $info = ldap_get_entries($ldap, $sr);
            echo(json_encode($info));
        } else {
            $sr = ldap_search($ldap, $app::$list["DATASPATH"], $search);
            $info = ldap_get_entries($ldap, $sr);
            echo(json_encode($info));
        }
        
    }
}

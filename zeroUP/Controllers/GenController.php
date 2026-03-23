<?php
namespace zeroUP\Controllers;

use Dom\Document;

class GenController extends \TDS\Controller {

    public static function home(){
//        App::$router->redirect(App::$router->generate('texte',['t' => 'introduction'] ) );
        $app = \TDS\App::get();
        $appName = $app::$appName; 
        $app::$router->redirect("/{$appName}/texte/introduction" );
    }

    public static function texte($texte){
        $app = \TDS\App::get();
        
        $texte = filter_var($texte, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
        $app::$cmpl['withMathjax'] = true;
        $app::$cmpl['withMarkdown'] = true;
        


        echo $app::$viewer->render('textes.html.twig', [ 'md' => "textes/{$texte}.md.twig" ]);
    }

    public static function texte_documents($document){
        $app = \TDS\App::get();

        $texte = filter_var($document, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
        $app::$cmpl['withMathjax'] = true;
        $app::$cmpl['withMarkdown'] = true;
        var_dump($app::$pathList);
        var_dump(getcwd());
        \TDS\Document::downloadStatic($app::$pathList['base']."/twig/templates/textes/documents/".$document);
        exit();
    }

 
    /**
     * @param string $year
     * 
     * Cette fonction permet de fixer l'année courante d'utilisation
     * Elle ne fonctionne cependant pas très bien dans la mesure où 
     * elle fonctionne uniquement pour les persponnes normalement 
     * identifiées via CAS (c'est le problème de forceAuth() )
     * Il faudrait réfléchir un tant soit peu pour l'améliorer
     * afin de voir comment faire en sorte qu'elle puisse fonctionner
     * pour tout le monde.
     * 
     */
    public static function setCurrentYear(string $year){
        $app = \TDS\App::get();
        unset($_SESSION['TDS_auth_'.$app::$appName]);
        $_SESSION['currentYear']=$year;
        $app::$auth->forceAuth();
        $app::$router->redirect('/');
    }


    private static function testLDAP(){
        try {
            $ldap = new \TDS\LDAPExtern();

            $filter='uid=ocardoso';
            $rep = $ldap->list($filter, ['uid', 'displayName']);
            return $rep->count ==1;
        } catch (\Exception $e){
            return false;
        }
    }

    private static function testDatabase(){
        try {
            $app = \TDS\App::get();
            return $app::$db->fetchOne("SELECT count(*) as N FROM Personne AS P WHERE P.id >0 AND P.actif")->n >0;
        } catch (\Exception $e){
            return false;
        }
    }


    public static function status($format){
        ini_set('display_errors', '0');
        $app = \TDS\App::get();

        $rep = [];
        // version de php
        $rep['Version PHP'] = phpversion();
        $rep['database'] = self::testDatabase();
        $rep['LDAP'] = self::testLDAP();
        

        var_dump($rep);
        phpinfo();

    }

}
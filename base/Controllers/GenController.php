<?php
namespace base\Controllers;

class GenController extends \TDS\Controller {

    public static function home(){
        $app = \TDS\App::get();
        $app::$router->redirect('/texte/introduction');
    }

    public static function texte($texte){
        $app = \TDS\App::get();
        
        $texte = filter_var($texte, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
        $app::$cmpl['withMathjax'] = true;
        $app::$cmpl['withMarkdown'] = true;
        
        echo $app::$viewer->render('textes.html.twig', [ 'md' => "textes/{$texte}.md.twig" ]);
    }


    public static function setCurrentYear(string $year){
 
        $app = \TDS\App::get();
        unset($_SESSION['TDS_auth_'.$app::$appName]);
        $_SESSION['currentYear']=$year;
        $app::$auth->forceAuth();
        $app::$router->redirect('/');
    }

}
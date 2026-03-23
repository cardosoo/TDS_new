<?php
namespace zeroU\Controllers;

use \zeroU\App;

class Gen extends \TDS\Controller {

    public static function home(){
        $app = App::get();
        $app::$router->redirect("/{$app::$appName}/texte/introduction");
    }

    public static function texte($t){
        $texte = filter_var($t, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
        App::$cmpl['withMathjax'] = true;
        App::$cmpl['withMarkdown'] = true;
        
        echo App::$viewer->render('textes.html.twig', [ 'md' => "textes/{$texte}.md.twig" ]);
    }

}
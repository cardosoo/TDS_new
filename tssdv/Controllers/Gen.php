<?php
namespace tssdv\Controllers;

use \tssdv\App;

class Gen extends \base\Controller {

    public static function home(){
        App::$router->redirect('/texte/introduction');
    }

    public static function texte($texte){
        $texte = filter_var($texte, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
        App::$cmpl['withMathjax'] = true;
        App::$cmpl['withMarkdown'] = true;
        
        echo App::$viewer->render('textes.html.twig', [ 'md' => "textes/{$texte}.md.twig" ]);
    }
    

}
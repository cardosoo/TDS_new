<?php
namespace tssdv\Controllers;


class MailerController extends \base\Controllers\MailerController {

    public static function initialMessage(){
        return "
Bonjour {{ P.getGeneric }},
{#
{{ include('mailer/rappelVoeux2021.md.twig') }}
{{ include('mailer/finVoeux2021.md.twig') }}
#}
    ";    
}

public static function mailer(){
    $app = \TDS\App::get();

    $cmpl = [
        'mail' => $app::$mail,
        'webmestre' => $app::$webmestre,
    ];

    $personneList = $app::NS('Personne')::loadWhere('actif');
    $statutList = $app::NS('Statut')::loadWhere('actif');

    $app::$cmpl["withJQuery"]=true;
    $app::$cmpl["withMarkdown"]=true;

    $message = self::initialMessage();

    echo $app::$viewer->render('mailer/index.html.twig', ['cmpl' => $cmpl, 'personneList' => $personneList, 'statutList' => $statutList, 'message' => $message]);
}

    
}

<?php
namespace foire\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailerController extends \base\Controllers\MailerController {

    public static function initialMessage(){
        return "
Bonjour {{ P.getGeneric }},
{#

{{ include('mailer/01_email_missionnaires_2eme_annee.md.twig') }} 
{{ include('mailer/02_email_missionnaires_3eme_annee.md.twig') }} 
{{ include('mailer/03_email_ouverture.md.twig') }} 
{{ include('mailer/04_email_ouverture_voeux.md.twig') }} 
{{ include('mailer/05_email_rappelVoeux.md.twig') }} 
{{ include('mailer/06_email_voeux_trop_nombreux.md.twig') }} 
{{ include('mailer/07_email_voeux_non_faits.md.twig') }} 
{{ include('mailer/08_email_service_minimum.md.twig') }} 
{{ include('09_email_fin_voeu.md.twig') }} 
{{ include('mailer/10_email_fermeture.md.twig') }} 
{{ include('mailer/11_email_heures_supp_vacataires_ATV.md.twig') }} 
{{ include('mailer/12_email_heures_supp_vacataires_CEV.md.twig') }} 
{{ include('mailer/13_email_service_missionnaire_minifoire.md.twig') }} 
{{ include('mailer/14_email_service_missionnaire.md.twig') }} 
{{ include('mailer/15_email_MAJ_apres_fermeture.md.twig') }} 
{{ include('mailer/16_email_surveillance.md.twig') }} 

{{ include('mailer/panier.md.twig') }} 
{{ include('mailer/voeux.md.twig') }}
{{ include('mailer/signature.md.twig') }}

{{ App.currentYear }}
{{ App.currentYear+1 }}

{{ App.texte.chargeReference }}
{{ App.texte.chargeReferenceNm }}
{{ App.texte.debutPanier }}
{{ App.texte.debutVoeux }}
{{ App.texte.limiteVoeux }}
{{ App.texte.debutDiagonalisation }}
{{ App.texte.finDiagonalisation }}
{{ App.texte.dateFoire }}
{{ App.texte.lieuFoire }}
{{ App.texte.exempleServiceNm }}
{{ App.texte.exempleService }}
{{ App.texte.correspondants }}

{{ P.getCharge() }}
{{ P.getGeneric }}
{{ P.voeu_personne_bilan.heures)|f2 }}

---
D'après les informations de la base de données {{ App.appName }}{{ App.currentYear }}, vous devez assurer une charge de 
{{P.getCharge()}} heures d'enseignement et vos vœux correspondent à une charge de {{(P.getCharge() - P.voeu_personne_bilan.heures)|f2}} heures. 
---
Nous vous rappelons que la **phase des Vœux {{ App.currentYear }} ferme le {{ App.texte.limiteVoeux }}**.
---
Vous trouverez un récapitulatif ci-dessous et sur votre page sur le [site de la foire](http://foire.physique.u-paris.fr).
{% if P.uid starts with '--' %}
Rappel : Pour vous connecter à la foire, en attendant de disposer d'un compte ENT, vous pouvez utiliser [ce lien](http://foire.physique.u-paris.fr/foire/directLink/{{ P.getDirectLink }}) qui est valable 3 mois. Merci de ne le communiquer à personne.
{% endif %}
---
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

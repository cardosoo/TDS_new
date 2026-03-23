<?php

namespace tssdv\Controllers;

use TDS\Query;
use TDS\Utils;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EnseignementController extends \base\Controllers\EnseignementController {


    /**
     * @param int $id
     * @param bool $isVoeu
     * 
     */
    public static function fiche(int $id, $isVoeu = false){
        $app = \TDS\App::get();

        $options = self::getOptions($id);
        self::setCmpl($id, $isVoeu);

        $voeu = $isVoeu?"Voeu":"";
        
        echo $app::$viewer->render("enseignement/fiche{$voeu}.html.twig", $options);
    }

    /**
     * @param int $id
     * 
     */
    public static function voeu(int $id){
        self::verificationUsage($id);
        self::fiche($id, true);
    }


    /**
     * @param int $id
     * 
     */
    public static function addOrChangeVoeu(int $id){
        $app = \TDS\App::get();
        
        self::verificationUsage($id, 'XHR');

        // lecture du voeu actuel si il existe
        $voeu = self::getVoeu($id);
        $new = $voeu->id ==0;

        if ($voeu->etat_ts < 3 ){ // si le voeu n'est  pas encore validé
            // lecture des données en POST
            $args = [
                'cm'   => \FILTER_VALIDATE_FLOAT,
                'td'      => \FILTER_VALIDATE_FLOAT,
                'ctd'     => \FILTER_VALIDATE_FLOAT,
                'tp'      => \FILTER_VALIDATE_FLOAT,
                'extra'   => \FILTER_VALIDATE_FLOAT,
                'bonus'   => \FILTER_VALIDATE_FLOAT,
                'correspondant' => \FILTER_VALIDATE_BOOLEAN,
                'validation' => \FILTER_VALIDATE_BOOLEAN,
            ];
            $propList = filter_input_array(INPUT_POST, $args);

            $voeu->cm = $propList['cm']?$propList['cm']:0;
            $voeu->ctd = $propList['ctd']?$propList['ctd']:0;
            $voeu->td = $propList['td']?$propList['td']:0;
            $voeu->tp = $propList['tp']?$propList['tp']:0;
            $voeu->extra = $propList['extra']?$propList['extra']:0;
            $voeu->bonus = $propList['bonus']?$propList['bonus']:0;
            $voeu->correspondant = $propList['correspondant']?true:false;
            $voeu->etat_ts = $propList['validation']?2:1;

            $voeu->save();

            if ($app::$texte['sendMailOnModif'] !== false){
                $app::$pub->error[] = "On prépare l'envoi d'un message";

                $P = $voeu->personne;
                $E = $voeu->enseignement;
                if ($new){
                    $subject = "[TSO][{$app::$currentYear}]Ajout pour {$P->prenom} {$P->nom}"; 
                    $msg = "Il y a eu un ajout dans le TS <a href='http://ts.sdv.u-paris.fr/tssdv/personne/{$P->id}'>{$P->getGeneric()}</a>
                        pour l'enseignement <a href='http://ts.sdv.u-paris.fr/tssdv/enseignement/{$E->id}'>{$E->getGeneric()}</a>";    
                } else {
                    $subject = "[TSO][{$app::$currentYear}]Modif pour {$P->prenom} {$P->nom}"; 
                    $msg = "Il y a eu une modif dans le TS <a href='http://ts.sdv.u-paris.fr/tssdv/personne/{$P->id}'>{$P->getGeneric()}</a>
                        pour l'enseignement <a href='http://ts.sdv.u-paris.fr/tssdv/enseignement/{$E->id}'>{$E->getGeneric()}</a>";    
                }

                if (MailerController::sendOneMail($app::$texte['sendMailOnModif'], $surname ='', $P->id, $msg, $subject, FALSE, $app::$texte['sendMailOnModif'])) {
                    $app::$pub->warning[] = 'Un message a été envoyé pour cette action sur le tableau de service';
                } else {
                    $app::$pub->error[] = "Il y a eu un problème dans l'envoi du message pour notification cette action sur le tableau de service";
                }
            }
        } else {
            $app::$pub->error[] = "Cette ligne a été validée par le responsable d'UE - Aucune modification ne peut-y être apportée";
        }
        self::fiche($id);
    }

    public static function deleteVoeu(int $id){
        $app = \TDS\App::get();

        $voeu = self::getVoeu($id);
        $P = $voeu->personne;
        $E = $voeu->enseignement;


        parent::deleteVoeu($id);
        if ($app::$texte['sendMailOnModif'] !== false){
            $subject = "[TSO][{$app::$currentYear}]Suppression pour {$P->prenom} {$P->nom}"; 
            $msg = "Il y a eu une suppression dans le TS <a href='http://ts.sdv.u-paris.fr/tssdv/personne/{$P->id}'>{$P->getGeneric()}</a>
                pour l'enseignement <a href='http://ts.sdv.u-paris.fr/tssdv/enseignement/{$E->id}'>{$E->getGeneric()}</a>";

            if (MailerController::sendOneMail($app::$texte['sendMailOnModif'], $surname ='', $P->id, $msg, $subject, FALSE, $app::$texte['sendMailOnModif'])) {
                $app::$pub->warning[] = 'Un message a été envoyé pour cette action sur le tableau de service';
            } else {
                $app::$pub->error[] = "Il y a eu un problème dans l'envoi du message pour notification cette action sur le tableau de service";
            }
        }
    }



    /**
     * @param int $id
     * @param mixed $error='404'
     * 
     */
    protected static function verificationUsage(int $id, $error='404'){
        $app = \TDS\App::get();

        // vérifications d'usage
        if (!$app::$auth->isInBase()) Utils::error($error);
        if (!$app::$phaseList[$app::$phase]->withAjouterVoeux)  Utils::error($error);
        $enseignement = $app::load('Enseignement', $id);
        if (! $enseignement->attribuable) Utils::error($error);
        if (! $enseignement->actif) Utils::error($error);
    }





}

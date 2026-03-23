<?php
namespace base\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailerController extends \TDS\Controller {

    public static function initialMessage(){
        return "
Bonjour {{ P.getGeneric }},
{#
{{ include('mailer/rappelVoeux.md.twig') }}
{{ include('mailer/voeux.md.twig') }}
{{ include('mailer/signature.md.twig') }}
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

    public static function mail($id){
        $app = \TDS\App::get();

        $message = filter_input(INPUT_POST,'message', FILTER_UNSAFE_RAW); // ce n'est pas forcément très bien de faire comme cela pour le filtrage...

        $P = $app::NS('Personne')::load($id);

       // $cmpl['css']= file_get_contents('textes/markdown.css');

        echo $app::$viewer->render('mailer/message.html.twig', ['P' => $P, 'message' => $message]);        
    }


    // Cela permet d'envoyer un message de façon "manuelle"
    public static function  sendOneMail($email, $surname ='', $id = null, $message='', $sujet='', $ccFoire='', $reply=''){
        $app = \TDS\App::get();

        if (!is_null($id)){
            $P = $app::NS('Personne')::load($id);
        } else {
            $P=null;
        }
                
        $message = $app::$viewer->render('mailer/message.html.twig', ['P' => $P, 'message' => $message]);
                
        //require 'vendor/autoload.php';
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->Mailer = "smtp";

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = $app::$mailerUsername;
        $mail->Password   = $app::$mailerPassword;
        try {
            //Recipients
            $mail->setFrom( $app::$mail, $app::$appName);
            $mail->addAddress($email, $surname);     // Add a recipient
            $mail->addReplyTo($reply, '');
            if ($ccFoire){
                $mail->addCC($app::$mail); 
            }
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $sujet;
            $mail->Body    = $message;
        
            $mail->send();
            sleep(2);
            return true;
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    // C'est cela qui permet d'envoyer un message depuis le mailer automatique
    public static function sendmail($id){
        $app = \TDS\App::get();

        $message = filter_input(INPUT_POST,'message', FILTER_UNSAFE_RAW); // ce n'est pas forcément très bien de faire comme cela pour le filtrage...
        $sujet = filter_input(INPUT_POST,'sujet', FILTER_UNSAFE_RAW);
        $ccFoire = filter_input(INPUT_POST,'ccFoire', FILTER_VALIDATE_BOOLEAN);
        $reply = filter_input(INPUT_POST,'reply', FILTER_VALIDATE_EMAIL);
        
        $P = $app::NS('Personne')::load($id);
        // $P->email = "Olivier.Cardoso@gmail.com";
        if (self::sendOneMail($P->email, "{$P->prenom} {$P->nom}", $id, $message, "{$sujet} -- {$P->prenom} {$P->nom}", $ccFoire, $reply)){
            echo "<div class='w3-pale-green  w3-margin-right w3-border' style='background-color: PaleGreen;'>{$P->email}, {$P->prenom} {$P->nom}</div>"; 
        } else {
            echo "<div class='w3-pale-red  w3-margin-right w3-border'   style='background-color: Pink;'>{$P->email}, {$P->prenom} {$P->nom}</div>"; 
        }
    }

    public static function sendReport(){
        $app = \TDS\App::get();
        
        $filters = array (
            "sujet" =>  FILTER_UNSAFE_RAW,
            'ccFoire' => FILTER_VALIDATE_BOOLEAN,
            "markdownText" => FILTER_UNSAFE_RAW,
            "messageHTML" => FILTER_UNSAFE_RAW,
            "compteRendu" => FILTER_UNSAFE_RAW,
            "statut" => array('filter' =>FILTER_VALIDATE_INT, "flags"=>FILTER_FORCE_ARRAY)
        );
        $input = filter_input_array(INPUT_POST, $filters);
        
        // $statutList = $app::NS('Statut')::loadWhere('actif');        
        $statutArr = [];
        foreach($input['statut'] as $s){
            $statut = $app::NS('Statut')::load($s);
            $statutArr[] = $statut->nom;    
        }
        $statutLi = join('<li/><li>',$statutArr) ;
        
        $body = "
        <div>CC Foire : {$input['ccFoire']}
        </div>
        <div><h4>Destinataires</h4>
        <ul>
          <li>{$statutLi}</li>
        </ul>
        </div>
        <div><h4>message Markdown</h4>
        <pre>{$input['markdownText']}</pre>
        </div>
        <div>
        <h4>message HTML</h4>
        {$input['messageHTML']}
        </div>
        <div>
        <h4>compte rendu d'envoi</h4>
        {$input['compteRendu']}
        </div>
        
        ";
        
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->Mailer = "smtp";

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = $app::$mailerUsername;
        $mail->Password   = $app::$mailerPassword;
        
        try {        
            //Recipients
            $mail->setFrom( $app::$mail, $app::$appName);
            $mail->addAddress( $app::$mail, $app::$appName);
            $mail->addReplyTo( $app::$mail, $app::$appName);
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = "[CRE] {$input['sujet']}";
            $mail->Body    = $body;
        
            $mail->send();
            
            echo "<div class='w3-pale-green  w3-margin-right w3-border'>Le comptre rendu a été envoyé</div>";
        } catch (Exception $e) {
            echo "<div class='w3-pale-red  w3-margin-right w3-border'>Le compte rendu n'a pas pu être envoyé</div>"; 
        }
    }


    
}

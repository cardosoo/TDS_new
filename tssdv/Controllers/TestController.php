<?php
namespace tssdv\Controllers;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Structure;
use StructureQuery;
use Etape;
use EtapeQuery;
use ECUE;
use ECUEQuery;
use ecue_etape;
use ecue_etapeQuery;


class TestController extends \base\Controllers\TestController {

    public static function test1_old(){
        var_dump('désactivation de /tssdv/test/test1'); exit();
        /* pour remettre les codes depuis la base 2024
        CREATE EXTENSION IF NOT EXISTS dblink;

        UPDATE enseignement AS e1
        SET code = e2.code
        FROM (
        SELECT id, code
        FROM dblink(
            'dbname=tssdv2024',
            'SELECT id, code FROM Enseignement'
        ) AS t(id integer, code text)
        ) AS e2
        WHERE e1.id = e2.id;
        */


        $app = \TDS\App::get();

        $struct = new \base\Struct();

        $structureList = StructureQuery::create()
            ->orderBy('nom')
            ->find();


        $app::$db->h_query('
            ALTER TABLE enseignement 
            ALTER COLUMN i_cm SET DEFAULT 1,
            ALTER COLUMN i_td SET DEFAULT 1,
            ALTER COLUMN i_ctd SET DEFAULT 1,
            ALTER COLUMN i_tp SET DEFAULT 1,
            ALTER COLUMN i_extra SET DEFAULT 1
            ;        
        ');


        $ecueList = EcueQuery::create()
            ->useEtapeQuery()                 // jointure avec la table "etape"
                ->useStructureQuery()         // jointure avec "structure"
                    ->filterById(10)          // structure.id = 10
                ->endUse()                    // fin du bloc StructureQuery
            ->endUse()                        // fin du bloc EtapeQuery
            ->find();

        $n = 0;
        foreach ($ecueList as $ecue) {
            $n++;
            $ecue->createEnseignementInDatabase();
            var_dump("{$n} {$ecue->getNom()}");
        }
    }

    public static function test1(){
        var_dump('désactivation de /tssdv/test/test1'); exit();
        $app = \TDS\App::get();


        include (getcwd()."/../tssdv/responsableList.php");
        // var_dump($respList);

        $voeuNS = $app::NS('Voeu');

        foreach($respList as $Eid => $Pid){
            $V = $app::NS('Voeu')::loadOneWhere("personne = {$Pid} and enseignement = {$Eid} ");
            if (is_null($V)){ // alors il faut le créer
                $V = new $voeuNS;
                $V->personne = $Pid;
                $V->enseignement = $Eid;
                $V->correspondant = true;
                $V->save();
            } else { // il faut voir si il faut le forcer
                if (! $V->correspondant){
                    $V->correspondant = true;
                    $V->save();
                }

            }
        }

    }


    public static function test2(){
        //var_dump('désactivation de /tssdv/test/test2'); exit();

        $data = <<<END
Stephanie Migrenne-Li
FREDERIC BERNARD
Veronique Birraux
Christine Rampon

Anne Badel
Frederique Deshayes
Giuseppe Gangarossa

Aurore Vidy-Roche
Chrystele Ikonomou-Racine
CELINE SORIN
Frederic Fluteau
Anne Badel
Giuseppe Gangarossa
Frederique Deshayes



Julien Dairou
Giliane Maton
CELINE SORIN
Frederic Fluteau
Leslie Regad
Anne Badel
Anne Badel
CELINE SORIN
Frederic Fluteau
Alexis Lalouette
Jean-Charles Cadoret
Frederique Deshayes
Giuseppe Gangarossa



Sabrina PICHON
Fernando Rodrigues Lima
Sandie Munier
Sandie Munier
Pierre-Emmanuel Ceccaldi
ANNE Couedel
Marie Leborgne
Claire Morvan
Sylvain Brun
Bertrand Cosson
Jea-Francois Ouimette
Giliane Maton
 Jean-Marc Verbavatz

Delara Sabéran-Djoneidi
Anne-Laure Todeschini

Sophie Filleur
Anouck Diet
Cecile Tourrel-Cuzin
Sylvie Soues
Clement Ricard


Gautier Moroy
Delphine Flatters
Anne Claude Camproux
Olivier Taboureau
Olivier Taboureau
Anne Claude Camproux

Anna Verschueren
Francois Gay

Isabelle DAJOZ

Fernando Rodrigues Lima
Sabrina PICHON
Pierre-Emmanuel Ceccaldi
India Leclercq
Isabelle Verstraete
Anne Jamet
Olivier Dussurget
Mireille Viguier
Rachel Golub

Alexandre Benmerah
Antoine Guichet
Jean-Marc Verbavatz

Bertran Cosson
Jean-Francois Ouimette
Nicolas Dulphy
Stéphane Giraudier
Luc Mouthon
Luc de Chaisemartin


Anne Claude Camproux
Anne Claude Camproux
Viet Khoa Nguyen

Jean-Christophe Gelly
Veronique Liebe-Gruber

Veronique Monnier
Mariano OSTUNI
Jamileh Movassat
David L'Hote
Marie Justine Guerquin
Sophie Filleur


Anna Verschueren
Francois Gay

Souhila Medjkane
Sophie Vriz

Catherine Quiblier
Patricia Genet

François Bouteau
Etienne Grésillon
END;

    $app = \TDS\App::get();
    $fu = new \base\FindUser();
    $rep = [];

    $data = explode("\n", $data);
    foreach($data as $nom){
        if (empty($nom)){
            $rep[]=[$nom, "", ""];
            continue;
        }
        $id = $fu->bestPersonneId($nom);
        if ($id === false){
            $rep[]=[$nom, "--", "--"];
            continue;
        }
        $P = $app::NS('Personne')::load($id);
        $rep[]=[$nom, $id, "{$P->prenom} {$P->nom}"];
    }

    foreach($rep as $l){
        echo (join(';', $l)."\n");
    }

    exit();


        $app = \TDS\App::get();

        $fu = new \base\FindUser();
        $id = $fu->bestPersonneId('Olivier Cardoso');
        if ($id === false) {
            die("Personne n'a été trouvé");
        }

        $P = $app::NS('Personne')::load($id);
        var_dump($P);
        exit();

        $struct = new \base\Struct();

        $structureList = StructureQuery::create()
            ->orderBy('nom')
            ->find();

        $etapeList = EtapeQuery::create()
                ->useStructureQuery()         // jointure avec "structure"
                    ->filterById(10)          // structure.id = 10
                ->endUse()                        // fin du bloc EtapeQuery
                ->find();

        $n = 0;
        foreach ($etapeList as $etape) {
            $n++;
            print("{$n}\t{$etape->getCode()}\t{$etape->getNom()}\n");
        }

    }

    public static function test3(){
        // var_dump('désactivation de /tssdv/test/test1'); exit();
        // pour lire des informations depuis un fichier xlsx (MCCC)
        // les fichiers à lire sont dans le dossier TDS_plus/tssdv/MCC/
        
        ini_set( 'max_execution_time', 180 ); 

        $app = \TDS\App::get();
        
        $inputGlob = $app::$pathList['plus'].'/tssdv/MCC/UFR**.xlsx';
        //$inputGlob = $app::$pathList['plus'].'/tssdv/MCC/UFR SDV-LG-Sciences de la vieV2.xlsx';
        
        $outputFilename = $app::$pathList['plus'].'/tssdv/MCC/out.xlsx';

        $ccc = new \tssdv\ImportCCC();
        $glob = \glob($inputGlob);
        foreach($glob as $inputFilename){
            var_dump($inputFilename);
            $ccc->processFile($inputFilename);
        }
        $ccc->saveSpreadsheet($outputFilename);
        //var_dump($ccc);
        exit();
    }
}

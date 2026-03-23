<?php
namespace base\Controllers;

use Base\EtapeQuery;
use Propel\Runtime\Propel;
use TDS\App;

class TestController extends \TDS\Controller {

    public static function phpinfo(){
        phpinfo();
    }

    public static function test1(){
        var_dump('désactivation de /base/test/test1'); exit();
        $app= \TDS\App::get();

        $app::$cmpl["withJQuery"]=true;

        var_dump('avant');
        $situation= $app::NS('Situation')::load(144);
        var_dump($situation);
        $situationList = $app::NS('Situation')::loadWhere('actif');
        var_dump('après');
        var_dump($situationList);

    }

    public static function test2(){
        var_dump('désactivation de /base/test/test1'); exit();
        $app = \TDS\App::NSC('App');
                
        $q = new \TDS\Query($app::NS('Voeu'), 'V');
        $q->join('V.personne', 'P', ['id', 'actif', 'nom', 'prenom']);
        $q->join('V.enseignement', 'E');
        $q->join('E.enseignement_periode', 'EP');
        $q->addSQL("WHERE {$q->V_id}>0");
        $voeuList = $q->exec();
        echo "<pre>";
        print_r($q->getSQL());
        echo "</pre>";
        var_dump($voeuList);
        exit();

        $maquetteNS = $app::NS("Maquette");
        $m = new $maquetteNS;
        var_dump($m);
//        $ma = json_decode('{"num":"39","actif":"t","departement":"19","gestionnaire":"0","responsable1":"94","responsable2":"0","code":"PASS","version":"","nom":"Adhoc - PASS","ordre":"3001","diplomeList":[{"num":"66","actif":"t","maquette":"39","code":"PCMPONE","nom":"PASS","ordre":"0","etapeList":[{"num":"65","actif":"t","diplome":"66","cursus":"1","code":"PMMPA 118","nom":"PASS","nbetu":"2300","ordre":"0","semestreList":[{"num":"134","actif":"t","etape":"65","code":"25AS01PC","nom":"Semestre 1","periode":"1","peretu":"100","ordre":"0","ueList":[{"num":"685","actif":"t","semestre":"134","peretu":"100","nom":"UE3 appar. syst\u00e8me 1 P1","code":"25AU03PC","ects":"4","ordre":"0","ecueList":[{"num":"1214","actif":"t","ue":"685","enseignement":"153","nom":"Physique PASS Cordelier + St Germain","nom_court":"UE1","code":"?","ects":"4","peretu":"60","ordre":"0"}]}]},{"num":"135","actif":"t","etape":"65","code":"?","nom":"PCC","periode":"3","peretu":"0","ordre":"1","ueList":[{"num":"686","actif":"t","semestre":"135","peretu":"100","nom":"RES FIL-PACES - Fili\u00e8re PASS","code":"?","ects":"0","ordre":"0","ecueList":[{"num":"1215","actif":"t","ue":"686","enseignement":"299","nom":"RES FIL-PASS - Fili\u00e8re PASS","nom_court":"UE1","code":"?","ects":"0","peretu":"100","ordre":"0"}]}]}]}]}]};var defaultData = {"Maquette":{"num":-1,"actif":true,"departement":-1,"gestionnaire":-1,"responsable1":-1,"responsable2":-1,"code":"Code Maquette","version":"version","nom":"Nom Maquette","ordre":-1,"diplomeList":"__LIST__"},"Diplome":{"num":-1,"actif":true,"maquette":null,"code":"Code Diplome","nom":"Nom Diplome","ordre":-1,"etapeList":"__LIST__"},"Etape":{"num":-1,"actif":true,"diplome":null,"cursus":-1,"code":"Code ETAPE","nom":"Nom Etape","nbetu":100,"ordre":-1,"semestreList":"__LIST__"},"Semestre":{"num":-1,"actif":true,"etape":null,"code":"code Semestre","nom":"Nom Semestre","periode":0,"peretu":100,"ordre":-1,"ueList":"__LIST__"},"UE":{"num":-1,"actif":true,"semestre":null,"peretu":100,"nom":"Nom UE","code":"Code UE","ects":0,"ordre":-1,"ecueList":"__LIST__"},"ECUE":{"num":-1,"actif":true,"ue":null,"enseignement":-1,"nom":"Nom ECUE","nom_court":"Nom_court  ECUE","code":"Code ECUE","ects":0,"peretu":100,"ordre":-1}}', true);
        $ma = json_decode('{"num":"39","actif":"t","departement":"19","gestionnaire":"0","responsable1":"94","responsable2":"0","code":"PASS","version":"","nom":"Adhoc - PASS","ordre":"3001","diplomeList":[{"num":"66","actif":"t","maquette":"39","code":"PCMPONE","nom":"PASS","ordre":"0","etapeList":[{"num":"65","actif":"t","diplome":"66","cursus":"1","code":"PMMPA 118","nom":"PASS","nbetu":"2300","ordre":"0","semestreList":[{"num":"134","actif":"t","etape":"65","code":"25AS01PC","nom":"Semestre 1","periode":"1","peretu":"100","ordre":"0","ueList":[{"num":"685","actif":"t","semestre":"134","peretu":"100","nom":"UE3 appar. syst\u00e8me 1 P1","code":"25AU03PC","ects":"4","ordre":"0","ecueList":[{"num":"1214","actif":"t","ue":"685","enseignement":"153","nom":"Physique PASS Cordelier + St Germain","nom_court":"UE1","code":"?","ects":"4","peretu":"60","ordre":"0"}]}]},{"num":"135","actif":"t","etape":"65","code":"?","nom":"PCC","periode":"3","peretu":"0","ordre":"1","ueList":[{"num":"686","actif":"t","semestre":"135","peretu":"100","nom":"RES FIL-PACES - Fili\u00e8re PASS","code":"?","ects":"0","ordre":"0","ecueList":[{"num":"1215","actif":"t","ue":"686","enseignement":"299","nom":"RES FIL-PASS - Fili\u00e8re PASS","nom_court":"UE1","code":"?","ects":"0","peretu":"100","ordre":"0"}]}]}]}]}]}', true);
        var_dump($ma);

        exit();


        var_dump($_SERVER);
        var_dump($_SESSION);

/*
        var_dump( $app::$router->generate('directLink', ['hex' => 'bretzel']) );
        var_dump('Il faut aller chercher un truc là');
        
        var_dump($app::$router->getRoutes());
*/
    }


    /**
     * Pour incrire une liste de personne dont on connait déjà tout
     */
    public static function test3_AjoutNonPermanentsDansLDAP(){
         // pour ajouter les non permanents qui sont dans le LDAP
        $app = \TDS\APP::get();

        // datas du 30/10/2021
        $impetrantList = [
            ['UDP000134414', 'aanantha', 'Anantharajah', 'Anusanth', 'anusanth.anantharajah@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000139102', 'ganqueti', 'Anquetin', 'Guillaume', 'guillaume.anquetin@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000123429', 'aattouch', 'Attouche', 'Angie', 'angie.attouche@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000136126', 'vballand', 'Balland-Jurine', 'Veronique', 'veronique.balland@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000135761', 'fbarbaul', 'Barbault', 'Florent', 'florent.barbault@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000138867', 'xbaudin', 'Baudin', 'Xavier', 'xavier.baudin@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000133547', 'bellamyp', 'Bellamy', 'Pascale', 'pascale.bellamy@u-paris.fr', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000140900', 'gbertho', 'Bertho', 'Gildas', 'gildas.bertho@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000041660', 'blanceti', 'Blanc', 'Etienne', 'etienne.blanc@u-paris.fr', 'EC UP (hors Sciences)',],
            ['UDP000137404', 'cboissar', 'Boissard', 'Christophe', 'christophe.boissard@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000122941', 'pbourdon', 'Bourdoncle', 'Pierre', 'pierre.bourdoncle@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000126147', 'mbranca', 'Branca', 'Mathieu', 'mathieu.branca@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000148504', 'brisser', 'Brisse', 'Romain', 'romain.brisse@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000135630', 'lbrun', 'Brun', 'Lelio', 'lelio.brun@u-paris.fr', 'CEV non fonctionnaire',],
            ['UDP000148500', 'calvetc', 'Calvet', 'Corentin', 'corentin.calvet@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000126338', 'scaulet', 'Caulet', 'Stephane', 'stephane.caulet@u-paris.fr', 'Intervenant occasionnel non titulaire: (C2055)',],
            ['UDP000108541', 'mcelume', 'Celume Bustamante', 'Macarena-Paz', 'macarena-paz.celume@u-paris.fr', 'CEV non fonctionnaire',],
            ['UDP000138716', 'fchau', 'Chau', 'Francois', 'francois.chau@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000112809', 'constantt', 'Constant', 'Thomas', 'thomas.constant@u-paris.fr', 'Agent Contractuel Public',],
            ['UDP000134397', 'sadahech', 'Dahech', 'Salem', 'salem.dahech@u-paris.fr', 'EC UP (hors Sciences)',],
            ['UDP000072683', 'dechanaudn', 'De Chanaud', 'Nicolas', 'nicolas.de-chanaud@u-paris.fr', 'CEV non fonctionnaire',],
            ['UDP000139667', 'kdesboeu', 'Desboeufs', 'Karine', 'karine.desboeufs@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000131906', 'sdiard', 'Diard', 'Simon', 'simon.diard@u-paris.fr', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000138767', 'cdong', 'Dong', 'Chang-Zhi', 'chang-zhi.dong@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000153213', 'felixs1', 'Felix', 'Sophie', 'sophie.felix@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000148549', 'sforest', 'Forest', 'Simon', 'simon.forest@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000016919', 'girardph', 'Girard', 'Philippe', 'philippe.girard@u-paris.fr', 'EC UP (hors Sciences)',],
            ['UDP000138367', 'piegirar', 'Girard', 'Pierre', 'pierre.girard@u-paris.fr', 'ATV (C2041)',],
            ['UDP000113647', 'bgreshak', 'Greshake Tzovaras', 'Bastian', 'bastian.greshake-tzovaras@u-paris.fr', 'Agent Contractuel Public',],
            ['UDP000139445', 'agroleau', 'Groleau', 'Alexis', 'alexis.groleau@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000137092', 'cgutle', 'Gutle', 'Claudine', 'claudine.gutle@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000136116', 'thaduong', 'Ha Duong', 'Nguyêt Thanh', 'thanh.haduong@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000126816', 'nhamid', 'Hamid', 'Naila', 'naila.hamid@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000135197', 'mhemadi', 'Hemadi Chalach', 'Miryana', 'hemadi@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000139395', 'nkassis', 'Kassis', 'Nadim', 'nadim.kassis@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000139097', 'fkwabiat', 'Kwabia Tchana', 'Fridolin', 'fridolin.kwabia-tchana@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000138500', 'alamouri', 'Lamouri', 'Aazdine', 'aazdine.lamouri@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000133870', 'jlemineu', 'Lemineur', 'Jean Francois', 'jeanfrancois.lemineur@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000138084', 'rmantaci', 'Mantaci', 'Roberto', 'roberto.mantaci@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000124693', 'gmattana', 'Mattana', 'Giorgio', 'giorgio.mattana@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000138775', 'fmaurel', 'Maurel', 'Francois', 'francois.maurel@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000136401', 'fmavre', 'Mavre', 'Francois', 'francois.mavre@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000135600', 'kmccoll', 'Mc Coll', 'Kathleen', 'kathleen.mc-coll@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000124787', 'vmichoud', 'Michoud', 'Vincent', 'vincent.michoud@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000129740', 'cmighird', 'Mighirditchian', 'Corinne', 'corinne.mighirditchian@u-paris.fr', 'CEV non fonctionnaire',],
            ['UDP000131754', 'emontalb', 'Montalban', 'Enrica', 'enrica.montalban@u-paris.fr', 'Agent Contractuel Public',],
            ['UDP000135799', 'vnoel', 'Noel', 'Vincent', 'vincent.noel@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000153104', 'norop', 'Noro', 'Pierre', 'pierre.noro@u-paris.fr', 'CEV non fonctionnaire',],
            ['UDP000139636', 'vpadovan', 'Padovani', 'Vincent', 'vincent.padovani@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000085572', 'vperduca', 'Perduca', 'Vittorio', 'vittorio.perduca@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000135413', 'cperruch', 'Perruchot', 'Christian', 'christian.perruchot@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000136124', 'mpicanti', 'Picantin', 'Matthieu', 'matthieu.picantin@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000140488', 'pietrirouxef', 'Pietri Rouxel', 'France', 'france.pietri-rouxel@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000139139', 'fprevot', 'Prevot', 'Francois', 'francois.prevot@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000136228', 'hravelom', 'Ravelomanana', 'Vlady', 'vlady.ravelomanana@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000043464', 'renaultg', 'Renault', 'Gilles', 'gilles.renault@u-paris.fr', 'CEV fonctionnaire',],
            ['UDP000100744', 'msantoli', 'Santolini', 'Marc', 'marc.santolini@u-paris.fr', 'Agent Contractuel Public',],
            ['UDP000124621', 'masanton', 'Santoni', 'Marie Pierre', 'marie-pierre.santoni@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000122115', 'dschamin', 'Schaming', 'Delphine', 'delphine.schaming@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000139964', 'syschmit', 'Schmitz', 'Sylvain', 'sylvain.schmitz@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000135833', 'bschollh', 'Schollhorn', 'Bernd', 'bernd.schollhorn@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000135405', 'nserradj', 'Serradji', 'Nawal', 'nawal.serradji@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000120181', 'mseydou', 'Seydou', 'Mahamadou', 'mahamadou.seydou@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000124652', 'csirange', 'Sirangelo', 'Cristina', 'cristina.sirangelo@u-paris.fr', 'EC Sciences (hors SdV)',],
            ['UDP000004802', 'soues', 'Soues', 'Sylvie', 'sylvie.soues@u-paris.fr', 'EC UP (hors Sciences)',],
            ['UDP000106523', 'lsovet', 'Sovet', 'Laurent', 'laurent.sovet@u-paris.fr', 'EC UP (hors Sciences)',],
            ['UDP000112921', 'cthoma98', 'Thomas', 'Cyril', 'cyril.thomas@u-paris.fr', 'EC UP (hors Sciences)',],
            ['UDP000127602', 'vviel', 'Viel', 'Vincent', 'vincent.viel@u-paris.fr', 'EC UP (hors Sciences)',],
            ['UDP000153072', 'zeinounb', 'Zeinoun', 'Bechara', 'bechara.zeinoun@u-paris.fr', 'CEV non fonctionnaire',],
            ['UDP000005515', 'zenasnfr', 'Zenasni', 'Franck', 'franck.zenasni@u-paris.fr', 'EC UP (hors Sciences)',],
       ];


        $personneNS = $app::NS('Personne');
        foreach($impetrantList as $impetrant){
            $nomStatut = pg_escape_string($app::$db->conn, $impetrant[5]);

            $statut = $app::NS('Statut')::loadOneWhere("nom = '{$nomStatut}'");


            $personne = new $personneNS;
            $personne->ose    = $impetrant[0]; 
            $personne->uid    = $impetrant[1];
            $personne->nom    = $impetrant[2];
            $personne->prenom = $impetrant[3];
            $personne->email   = $impetrant[4];
            $personne->statut  = $statut->id;
            $personne->save();
        }

    
        var_dump('Fait pour les Dans LDAP');
    }

    /**
     * Pour incrire une liste de personne dont on ne connait pas tout
     */
    public static function test3_AjoutNonPermanentsHorsLDAP(){
        // pour ajouter les non permanents qui ne sont dans le LDAP
        $app = \TDS\APP::get();

        // datas du 30/10/2021
        $impetrantList = [
            ['UDP000131562', 'ALBAUD BENOIT', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['OSE1370ose.nadia.alfaidy-benh', 'ALFAIDY-BENHAROUGA NADIA', 'CEV fonctionnaire',],
            ['OSE1436ose.alexia.alfaro', 'ALFARO ALEXIA', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000140875', 'ANDRIEU MURIEL', 'CEV fonctionnaire',],
            ['OSE1118ose.stephane.angles', 'ANGLES STEPHANE', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000122633', 'AUTIER VALERIE', 'Intervenant occasionnel non titulaire: (C2055)',],
            ['OSE1634ose.anais.baudot', 'BAUDOT ANAIS', 'CEV fonctionnaire',],
            ['UDP000152505', 'BELE PATRICK', 'CEV non fonctionnaire',],
            ['OSE20202257ose.magali.berland', 'BERLAND MAGALI', 'CEV fonctionnaire',],
            ['OSE1845ose.francois.bertaux', 'BERTAUX François', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000122071', 'BLONDEAU KARINE', 'CEV fonctionnaire',],
            ['UDP000133700', 'BOHEC MYLENE', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000134202', 'BURGUIERE PIERRE', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['OSE1635ose.laura.cantini', 'CANTINI LAURA', 'CEV fonctionnaire',],
            ['OSE20202835ose.jeancharles.ca', 'CARVAILLO JEAN-CHARLES', 'CEV fonctionnaire',],
            ['UDP000129543', 'CASSAING FREDERIC', 'CEV non fonctionnaire',],
            ['OSE1723ose.olivier.cassar', 'CASSAR OLIVIER', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000152517', 'CAZES REMI', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['OSE854ose.fabien.chenel', 'CHENEL FABIEN', 'CEV non fonctionnaire',],
            ['OSE1715ose.matthieu.cisel', 'CISEL Matthieu', 'CEV fonctionnaire',],
            ['OSE1675ose.dominique.clermont', 'CLERMONT DOMINIQUE', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000150619', 'CROZET PIERRE', 'CEV fonctionnaire',],
            ['UDP000152007', 'DELEPAUT AGATHE', 'CEV non fonctionnaire',],
            ['OSE1643ose.sandra.derozier', 'DEROZIER SANDRA', 'CEV fonctionnaire',],
            ['OSE20202157ose.binta.dieme', 'DIEME BINTA', 'CEV fonctionnaire',],
            ['OSE450ose.florent.dumont', 'DUMONT FLORENT', 'CEV fonctionnaire',],
            ['UDP000112187', 'EJSMONT RADOSLAW', 'CEV fonctionnaire',],
            ['OSE1327ose.said.el ouazizi', 'EL OUAZIZI SAID', 'CEV fonctionnaire',],
            ['OSE1720ose.pierre-alban.ferrer', 'FERRER Pierre-Alban', 'CEV fonctionnaire',],
            ['OSE855ose.stephane.fouquay', 'FOUQUAY STEPHANE', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000129567', 'GENEVET THIERRY', 'ATV (C2041)',],
            ['UDP000128510', 'GHOZLANE AMINE', 'CEV non fonctionnaire',],
            ['OSE20204518ose.sebastien.goud', 'GOUDEAU Sébastien', 'CEV fonctionnaire',],
            ['UDP000122946', 'GUILBERT THOMAS', 'CEV fonctionnaire',],
            ['UDP000122660', 'KERGOAT MICHELINE', 'Intervenant occasionnel non titulaire: (C2055)',],
            ['UDP000129570', 'LAMEIRAS SONIA', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['UDP000120917', 'LARCHER EMILIE', 'CEV fonctionnaire',],
            ['UDP000122685', 'LEROY OLIVIER', 'Salarié d\'une entreprise / multi employeurs / d\'un organisme étranger (C2038/C2054/C2055)',],
            ['OSE2004ose.wolfram.liebermeis', 'LIEBERMEISTER Wolfram', 'CEV fonctionnaire',],
            ['OSE20203177ose.valentin.loux', 'LOUX VALENTIN', 'CEV fonctionnaire',],
            ['OSE1739ose.jean-leon.maitre', 'MAÎTRE Jean-Léon', 'CEV fonctionnaire',],
            ['UDP000139689', 'MOTARD ERIC', 'CEV fonctionnaire',],
            ['UDP000146455', 'Marchiol Carmen', 'CEV fonctionnaire',],
            ['OSE917ose.olivier.nicole', 'NICOLE Olivier', 'CEV fonctionnaire',],
            ['OSE2069ose.paolo.pierobon', 'PIEROBON Paolo', 'CEV fonctionnaire',],
            ['OSE20201651ose.michael.rera', 'RERA Michael', 'CEV fonctionnaire',],
            ['UDP000148494', 'SAHLI CELIA', 'EC Sciences (hors SdV)',],
            ['UDP000120166', 'SANTUZ HUBERT', 'CEV fonctionnaire',],
            ['UDP000148513', 'SENABRE HIDALGO ENRIQUE', 'CEV fonctionnaire',],
            ['UDP000122468', 'SUEUR JEROME', 'CEV fonctionnaire',],
            ['UDP000132594', 'SUZANNE MARION', 'CEV non fonctionnaire',],
            ['OSE20202088ose.edwin.wintermu', 'WINTERMUTE EDWIN', 'CEV fonctionnaire',],
        ];


        $personneNS = $app::NS('Personne');
        foreach($impetrantList as $impetrant){
            $nomStatut = pg_escape_string($app::$db->conn, $impetrant[2]);

            $statut = $app::NS('Statut')::loadOneWhere("nom = '{$nomStatut}'");


            $personne = new $personneNS;
            $personne->ose     = $impetrant[0]; 
            $personne->uid     = "--";
            $personne->nom     = $impetrant[1];
            $personne->prenom  = "";
            $personne->email   = "";
            $personne->statut  = $statut->id;
            $personne->save();
        }

        var_dump('Fait pour les Hors LDAP');
    }

    public static function test3(){
        var_dump('désactivation de /base/test/test1'); exit();
        // self::test3_AjoutNonPermanentsDansLDAP();
        // self::test3_AjoutNonPermanentsHorsLDAP();

        // ici c'est pour initialiser la nouvelle façon de modéliser les étapes à partir l'ancienne façon< de représenter les étapes.
        // on le fait pour chaque etape de la foire et on recherche la correspondance dans les  etapes de OSE

        // je ne sais pas comment faire, il faudrait que chaque enseignement soit relié aux étapes qui le concerne vraiment 
        // par exemple pour les enseignements qui sont mutualisés mais pour lesquels il y a un seul code ecue mutualisé
        // présent dans différentes maquettes on devrait pouvoir faire la distinction. 
        // le problème arrive pour les UE libres qui apparraissent partout

        $app = \TDS\App::get();

        $struct = new \base\Struct();

        $etapeList = $app::NS('Etape')::loadWhere("actif");
        // var_dump($etapeList);
        $etapeOSE = $struct->getUsefulEtapeList(['what' => 'PH01Y090' ]);
        var_dump($etapeOSE);

        exit();

        foreach($etapeList as $etape){
            $code = explode('-', explode(' ',$etape->code)[0])[0];
            if (empty($code)){
                $et = [];
            } else {
                $et = EtapeQuery::create()
                ->filterByCode($code.'%', \Propel\Runtime\ActiveQuery\Criteria::LIKE)
                ->find();    
            }

            $res = [
                'etape' => [
                    'code' => $etape->code,
                    'nom' => $etape->nom,
                ],
                'et' => [],
            ];

            foreach($et as $e){
                $res['et'][]=[
                    'code' => $e->getCode(),
                    'nom' => $e->getNom(),
                ];
            }

            var_dump($res);
        }

    }



}
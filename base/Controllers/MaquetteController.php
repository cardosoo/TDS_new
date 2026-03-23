<?php

namespace base\Controllers;

use stdClass;

class MaquetteController extends \TDS\Controller {

    public static function getOptions($id){
        $app = \TDS\App::get();

        $M = $app::load('Maquette', $id);

        $asideTabList= [];
        if ($M->withCommentaires()){
            $asideTabList[]=[
                'name' => 'Commentaires',
                'label' => 'Commentaires ('.count(array_filter($M->commentaire_maquetteList, '\TDS\App::isActive')).')',
                'template' => 'maquette/aside_commentaires.html.twig',
                'hasChangedCall' => 'hasChangedCommentaires',
                'canEdit' => $M->canEditCommentaires(),
            ];
        } 

        if ($M->withDocuments() ){
            $docList = $M->getDocumentList();
            $asideTabList[]=[
                'name' => 'Documents',
                'label' => 'Documents ('.count($docList).')',
                'template' => 'maquette/aside_documents.html.twig',
                'docList' => $docList, 
                'canEdit' => $M->canEditDocuments(),
            ];
        }


        return [ 
            'M' => $M, 
            'asideTabList' =>$asideTabList,
        ];
    }




    public static function fiche($id){
        $app = \TDS\App::get();

        $options = self::getOptions($id);

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl['withDataTables'] = true; 
        $app::$cmpl['withKnockout'] = true;



        $app::$toCRUD="/{$app::$appName}/CRUD/Maquette/$id";




        echo $app::$viewer->render("maquette/fiche.html.twig", $options  );
    }

    // Ici débute la partie administration 
    // d'édition des maquettes
    public static function editList(){
        $app = \TDS\App::get();

        $composanteList = $app::NS('Composante')::loadWhere('actif');

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withKnockout"]=true;

        echo $app::$viewer->render("maquette/liste.html.twig", ['composanteList' => $composanteList] );
    }

    public static function saveList(){
        $app = \TDS\App::get();

        $actual = json_decode(filter_input(INPUT_POST, 'actual'));
        $deleted = json_decode(filter_input(INPUT_POST, 'deleted'));

        $maquetteNS = $app::NS('Maquette');
        $composanteNS = $app::NS('Composante');


        $dOrdre = 0;
        foreach($actual as $d){
            $dOrdre+= 1;
            $dName = pg_escape_string($app::$db->conn, $d->name);
            if ($d->id <=0) { // alors c'est une creation
                $composante = new $composanteNS;
            } else {
                $composante = $composanteNS::load($d->id);
            }
            $composante->ordre = $dOrdre;
            $composante->nom = pg_escape_string($app::$db->conn, $dName);
            $composante->save();

//            print_r($composante);
            
            $mOrdre = 1000*$dOrdre;
            foreach ($d->maquetteList as $m){
                $mOrdre+=1;
                $mName = pg_escape_string($app::$db->conn, $m->name);
                if ($m->id <=0) { // alors c'est une creation
                    $maquette = new $maquetteNS;
                } else {
                    $maquette = $maquetteNS::load($m->id);
                }
                $maquette->ordre = $mOrdre;
                $maquette->nom = pg_escape_string($app::$db->conn, $mName);
                $maquette->actif = true;
                $maquette->composante = $composante->id;
                $maquette->save();             
            }
        }
        
        foreach($deleted as $d){
            foreach($d->maquetteList as $m){
                if ($m->id>0) {
                    $maquette = $maquetteNS::load($m->id);
                    $maquette->delete();
                }    
            }
            
            if ($d->id >0) { // alors ce n'est pas une création
                $composante = $composanteNS::load($d->id);
                $composante->delete();
            }
        }        
    }

    public static function getECUEList($ue){
        $eL = [];
        foreach($ue->ecueList as $ecue){
            $eL[] =  $ecue->__org__;
        }
        return $eL;
    }
    public static function getUEList($semestre){
        $uL = [];
        foreach($semestre->ueList as $ue){
            $u =  $ue->__org__;
            $u['ecueList'] = self::getECUEList($ue);
            $uL[] = $u;
        }
        return $uL;
    }

    public static function getSemestreList($etape){
        $sL = [];
        foreach($etape->semestreList as $semestre){
            $s =  $semestre->__org__;
            $s['ueList'] = self::getUEList($semestre);
            $sL[] = $s;
        }
        return $sL;
    }

    public static function getEtapeList($diplome){
        $eL = [];
        foreach($diplome->etapeList as $etape){
            $e =  $etape->__org__;
            $e['semestreList'] = self::getSemestreList($etape);
            $eL[] = $e;
        }
        return $eL;
    }

    public static function getDiplomeList($maquette){
        $dL = [];
        foreach($maquette->diplomeList as $diplome){
            $d =  $diplome->__org__;
            $d['etapeList'] = self::getEtapeList($diplome);
            $dL[] = $d;
        }
        return $dL;
    }

    public static function edit($id){
        $app = \TDS\App::get();

        $maquette = $app::load('Maquette', $id);

        $maquetteObj = $maquette->__org__;
        $maquetteObj['diplomeList'] = self::getDiplomeList($maquette);

        $personneList =  $app::NS('Personne')::loadWhere('actif');
        $enseignementList = $app::NS('Enseignement')::loadWhere('actif');
        $cursusList = $app::NS('Cursus')::loadWhere('actif');
        $composanteList = $app::NS('Composante')::loadWhere('actif');

        $maquetteNS = $app::NS('Maquette');
        $diplomeNS =  $app::NS('Diplome');
        $etapeNS =  $app::NS('Etape');
        $semestreNS =  $app::NS('Semestre');
        $ueNS =  $app::NS('UE');
        $ecueNS =  $app::NS('ECUE');

        $maquetteD =  new $maquetteNS;
        $diplomeD =  new $diplomeNS;
        $etapeD = new $etapeNS;
        $semestreD = new $semestreNS;
        $ueD =  new $ueNS;
        $ecueD = new $ecueNS;

        $maquetteD = $maquetteD->__org__;
        $diplomeD =  $diplomeD->__org__;
        $etapeD =  $etapeD->__org__;
        $semestreD =  $semestreD->__org__;
        $ueD =  $ueD->__org__;
        $ecueD =  $ecueD->__org__;

        $diplomeD['diplomeList'] = '__LIST__';
        $diplomeD['maquette'] = null;
        $etapeD['semestreList'] = '__LIST__';
        $etapeD['diplome'] = null;
        $semestreD['ueList'] = '__LIST__';
        $semestreD['etape']=null;
        $ueD['ecueList'] = '__LIST__';
        $ueD['semestre'] = null;
        $ecueD['UE'] = null;

        $maquetteDefault = [
            'Maquette' => $maquetteD,
            'Diplome' => $diplomeD,
            'Etape' => $etapeD,
            'Semestre' => $semestreD,
            'UE' => $ueD,
            'ECUE' => $ecueD,
        ];
        

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl["withKnockout"]=true;

        echo $app::$viewer->render("maquette/edit.html.twig", ['maquetteObj' => $maquetteObj, 'maquetteDefault'=> $maquetteDefault, 'personneList' => $personneList, 'enseignementList' => $enseignementList, 'cursusList' => $cursusList ,'composanteList' => $composanteList] );
    }


    public static function update(&$elm, $e){
        foreach($e as $key => $item){
            if (  ($key=='id') || (substr($key,-2) == '__') || ( substr($key, -4) == 'List')  ){
                // doNothing
            } else {
                if (isset($elm->$key)){
                    $elm->$key = $item;
                }
            }
        }
    }

    public static function save($id){
        $app = \TDS\App::get();
        $maquette = $app::load('Maquette', $id);

        $maquette->nom = htmlspecialchars_decode(filter_input(INPUT_POST, 'maquetteName', FILTER_UNSAFE_RAW));
        $maquette->code = htmlspecialchars_decode(filter_input(INPUT_POST, 'maquetteCode', FILTER_UNSAFE_RAW));
        $maquette->composante = filter_input(INPUT_POST, 'composante', FILTER_VALIDATE_INT);
        $maquette->responsable = filter_input(INPUT_POST, 'responsable', FILTER_VALIDATE_INT);
        $maquette->co_responsable = filter_input(INPUT_POST, 'co_responsable', FILTER_VALIDATE_INT);
        $maquette->gestionnaire = filter_input(INPUT_POST, 'gestionnaire', FILTER_VALIDATE_INT);

        $maquette->save();


        $actual = json_decode(filter_input(INPUT_POST, 'actual'));
        $deleted = json_decode(filter_input(INPUT_POST, 'deleted'));

//        print_r($actual);                            


        foreach($actual as $d){
            $diplomeNS = $app::NS('Diplome');
            $diplome = $d->id>0?$diplomeNS::load($d->id):new $diplomeNS;
            self::update($diplome, $d);
            $diplome->maquette=$maquette->id;
            $diplome->save();

            foreach ($d->etapeList as $e){
                $etapeNS = $app::NS('Etape');
                $etape = $e->id>0?$etapeNS::load($e->id):new $etapeNS;
                self::update($etape, $e);
                $etape->diplome = $diplome->id;
                $etape->save();

                foreach ($e->semestreList as $s){
                    $semestreNS = $app::NS('Semestre');
                    $semestre = $s->id>0?$semestreNS::load($s->id):new $semestreNS;
                    self::update($semestre, $s);
                    $semestre->etape = $etape->id;
                    $semestre->save();

                    foreach ($s->ueList as $u){
                        $ueNS = $app::NS('UE');
                        $ue = $u->id>0?$ueNS::load($u->id):new $ueNS;
                        self::update($ue, $u);
                        $ue->semestre = $semestre->id;
                        $ue->save();

                        foreach ($u->ecueList as $ec){
                            $ecueNS = $app::NS('ECUE');
                            $ecue = $ec->id>0?$ecueNS::load($ec->id):new $ecueNS;
                            self::update($ecue, $ec);
                            $ecue->ue = $ue->id;
                            $ecue->save();
                        }
                    }    
                }
            }
        }
        $classFromList = [
            'maquetteList' => 'Maquette',
            'diplomeList' => 'Diplome',
            'etapeList' => 'Etape',
            'semestreList' => 'Semestre',
            'ueList' => 'UE',
            'ecueList' => 'ECUE',    
        ];
        
        
        // on fait les suppressions, normalement cela devrait le faire puisque la suppression se propoage à l'ensemble des choses qui sont liées entre elles
        // il y a peut-être un petit problème avec les enseignements qui sont liés mollement... Argh !!!
        foreach($deleted as $toDelete){
            $className= $app::NS($classFromList[$toDelete->list]);
            $e = $className::load($toDelete->elm->id);
            $e->deleteWithCascade();
        }
    }

}


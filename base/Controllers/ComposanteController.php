<?php

namespace base\Controllers;

use stdClass;

class ComposanteController extends \TDS\Controller {

    public static function getOptions($id){
        $app = \TDS\App::get();

        $C = $app::load('Composante', $id);

        $asideTabList= [];
        if ($C->withCommentaires()){
            $asideTabList[]=[
                'name' => 'Commentaires',
                'label' => 'Commentaires ('.count(array_filter($C->commentaire_composanteList, '\TDS\App::isActive')).')',
                'template' => 'composante/aside_commentaires.html.twig',
                'hasChangedCall' => 'hasChangedCommentaires',
                'canEdit' => $C->canEditCommentaires(),
            ];
        } 

        if ($C->withDocuments() ){
            $docList = $C->getDocumentList();
            $asideTabList[]=[
                'name' => 'Documents',
                'label' => 'Documents ('.count($docList).')',
                'template' => 'composante/aside_documents.html.twig',
                'docList' => $docList, 
                'canEdit' => $C->canEditDocuments(),
            ];
        }


        return [ 
            'C' => $C, 
            'asideTabList' =>$asideTabList,
        ];
    }




    public static function fiche($id){
        $app = \TDS\App::get();

        $options = self::getOptions($id);

        $app::$cmpl["withJQuery"]=true;
        $app::$cmpl['withDataTables'] = true; 
        $app::$cmpl['withKnockout'] = true;



        $app::$toCRUD="/{$app::$appName}/CRUD/Composante/$id";

        echo $app::$viewer->render("composante/fiche.html.twig", $options  );
    }


}


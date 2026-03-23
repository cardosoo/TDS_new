<?php
namespace TDS;
/**
 * Description of Utils
 *
 * @author olivier
 */
class Utils {
    
    public static function formatHTML($st){
        return htmlentities($st,ENT_QUOTES|ENT_HTML5);
    }

    public static function fNombre($val){
        if ($val == 0){
            return "";
        }
        
        if ( abs(intval($val)- floatval($val))< 1e-2  ){
           return intval($val); 
        }
        
        return sprintf("%.2f", floatval($val));
    }

    public static function eNombre($val){
        if ($val == 0){
            return "0";
        }
        
        if ( abs(intval($val)- floatval($val))< 1e-2  ){
           return intval($val); 
        }
        
        return sprintf("%.2f", floatval($val));
    }

    // supprimme les tags <p> qui entoure la conversion markdown
    public static function clearMarkdown($val){
        return substr($val, 3,-4);
    }

    public static function json_decode($json, $associative = null){
        return json_decode($json, $associative);
    }

    public static function redirect($url){
        header('Location: '.$url);
        exit(0);
    }
        
    public static function error($err="404"){
        $app = \TDS\App::get();
        echo $app::$viewer->render("error{$err}.html.twig"); 
        exit(0);
    }

    public static function getIP(){
        return filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)." / ".filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP);         
    }
}

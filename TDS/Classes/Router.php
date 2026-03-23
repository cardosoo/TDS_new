<?PHP
namespace TDS;

class Router extends \AltoRouter {

    public array $routeList = [];
    protected string $namespace = ""; 

    protected $matchTypes = [
        'i'  => '[0-9]++',
        'a'  => '[0-9A-Za-z_]++',
        'h'  => '[0-9A-Fa-f]++',
        '*'  => '.+?',
        '**' => '.++',
        ''   => '[^/\.]++'
    ];


    public function namedRouteExists($routeName){
        return isset($this->namedRoutes[$routeName]);
    }


    public function setNamespace($CN){
        $this->namespace = $CN;
    }

    public function getNamespace(){
        return $this->namespace;
    }

    public function addRoute($route){
        if (is_a($route, 'TDS\Route') ){
            $route->name = $route->name??$route->target;
            if ( ! $this->namedRouteExists($route->name) ){
                $this->map($route->method,$route->route, $route->target, $route->name);
                //$this->namedRoutes[$route->name]=$this->namespace.$route->target;
            }
        } elseif (is_array($route)) {
            foreach($route as $r){
                $this->addRoute($r);
            }
        } else {
            var_dump($route);
            throw new \Exception("Erreur dans la table de routage");
        }
    }

    public function buildRoutes(){
        $app = \TDS\App::get();

        // on parcours les différentes sections : 
        // === les routes publiques
        foreach($this->routeList['public'] as $route){
            $this->addRoute($route);
        }

        $app::$auth = new Authenticate();
        if ( $app::$auth->isAuth) {
            // === les routes qui ont besoin d'une authentification
            foreach($this->routeList['withAuth'] as $route){
                $this->addRoute($route);
            }

            if ($app::$auth->inBase) {
                if ($app::$auth->user->uid =='fconstan'){
                    exit();
                }

                // === les routes qui ont besoin d'être dans la base de données
                foreach($this->routeList['private'] as $route){
                    $this->addRoute($route);
                }

                // === les routes qui ont besoin d'un rôle particulier
                foreach($this->routeList['restrict'] as $role => $routeList){
                    if (isset($app::$auth->roleList[$role])){
                        foreach($routeList as $route){
                            $this->addRoute($route);
                        }
                    }
                }
            }
        }

        $this->setNamespace('\\'.$app::$appName.'\\');
        $this->addRoute(new Route('GET', '/assets/[*:asset]', 'Router::routeAsset','route_asset'));
        $this->addRoute(new Route('GET', '/photos/[i:id]', 'Router::routePhotos','route_photos'));
        $this->addRoute(new Route('GET', '/Docs/[h:hex]', 'Router::routeDocs','route_documents'));
        $this->addRoute(new Route('POST', '/DocUpload/[a:entity]/[i:id]', 'Router::routeDocUpload','route_document_upload'));
        //$this->addRoute(new Route('POST', '/DocUpload/[a:entity]/[i:id]', 'Router::routeDocUpload','route_document_upload'));
        $this->addRoute(new Route('POST', '/renameDoc/[h:hex]', 'Router::routeRenameDoc','route_renameDoc'));
        $this->addRoute(new Route('POST', '/deleteDoc/[h:hex]', 'Router::routeDeleteDoc','route_deleteDoc'));

        if ($app::$auth->isAuth){
            foreach($app::$auth->roleList as $role => $b){
                $this->addRoute(new Route('GET', "/role/{$role}", "Router::{$role}","router_{$role}"));                            
            }
        }
        require_once("../TDS/Classes/AltoRouter.php"); // on a besoin de cela ou alors il faut faire une entrée spéciale dans l'autoload... à voir ce qui est préférable
        $app::$auth->isAdmin();


        //var_dump($app::$auth);
    }

    protected function beforeCall(){

    }


    public function doMatch($requestUrl = null, $requestMethod = null){
        $app=\TDS\App::get();
        $match = $this->match($requestUrl, $requestMethod);
        if (is_array($match)) {

            $r = explode('::',$match['target'],);
            if (! in_array($r[1], ['routeAsset','routePhotos', 'routeDocs','routeDocUpload', 'routeRenameDoc', 'deleteDoc'] )){
                $app::doLog( $match['target'] );
            }

            if( is_array($match) && is_callable( $match['target'] ) ) {
                // On a sans doute pas besoin de cela ici car on passe toujours target comme une chaine
                
                $this->beforeCall();
                $app::$router->callFunc( $match['target'], $match['params'] ); 
                exit();
            } else {
                header("Status: 200 OK", false, 200);
                $params = [];
                foreach($match['params'] as $var => $value){
                    $params[$var]= urldecode($value);
                }

                $this->beforeCall();
                $app::$router->callFunc($match['target'],$params);
                exit();
            }
        }


        if (!$app::$auth->isAuth){
            $app::$auth->forceAuth();
            $this->buildRoutes();
            $this->doMatch();
        }
        call_user_func_array([$app::$router, "error404"], []);
    }

    protected function doRouteAsset($asset, $dir){
        if (substr($asset,-3) == 'css'){
            header('Content-Type: text/css');
        } elseif (substr($asset,-2) == 'js') {
            header('content-type: application/javascript;');
        }
        $fname = $dir.DIRECTORY_SEPARATOR.$asset;
        if (file_exists($fname)){
            readfile($fname);
            return true;
        }
        return false;
    }
    
    protected static function routeAsset($asset){
        $app = \TDS\App::get();
        return $app::$router->doRouteAsset($asset, __DIR__. "/../assets" );
    }


    protected static function routeDocs($hex){
//// C'est sans doute par ici qu'il va falloir faire quelque chose...
        $app = \TDS\App::get();
        if (!$app::$auth->isAuth){
            var_dump("Pas de connexion à l'application");
            exit();
        }
        $key = str_pad('', SODIUM_CRYPTO_SECRETBOX_KEYBYTES ,"{$app::$auth->user->id}");
        $doc = $app::simpleDecrypt($hex, $key);
        $document = new Document($doc->className, $doc->id, $doc->filename, $doc->timestamp);
        $document->download();
    }


    public static function getPhotosPath(){
        $app = \TDS\App::get();
        return \realpath( __DIR__."/../../../TDS_plus/".$app::$appName."/photos");
    }

    protected static function routePhotos($id){
        $app = \TDS\App::get();
        $fname =  self::getPhotosPath()."/photo_{$id}.jpg";
        if (file_exists($fname)){
            header('Content-type: image/jpeg');
        } else {
            $fname = __DIR__."/../../../TDS_plus/".$app::$appName."/photos/noPhoto.jpg";
        }
        include($fname);
        exit();
    }


    protected static function routeDocUpload($entity, $id){
        $app = \TDS\App::get();

        // var_dump([$entity, $id]);
        // var_dump($_FILES);

        if (! $app::NS($entity)::canEditDocuments()){
            echo json_encode([
                'error' => true,
                'message' => "L'utilisateur n'est pas autorisé à déposer ce document.",
            ]);
            exit();
        }

        if (! isset($_FILES['file'])){
            echo json_encode([
                'error' => true,
                'message' => "Problème à l'upload (Fichier trop volumineux ?).",
            ]);
            exit();
        }


        if ($_FILES['file']['error'] !== 0 ){
            echo json_encode([
                'error' => true,
                'message' => "erreur {$_FILES['file']['error']} lors de l'upload.",
            ]);
            exit();
        }

        $dir = Document::getDocumentPath($entity)."/{$id}";
        if (!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        $filename = basename($_FILES['file']['name']);
        $path_parts = pathinfo($filename);
        $title = $path_parts['filename'];
        $ext = strtolower($path_parts['extension']);

        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            echo json_encode([
                'error' => true,
                'message' => "Upload pas bon.",
            ]);
            exit();            
        }

        $filepath = "{$dir}/{$_FILES['file']['name']}";

        move_uploaded_file($_FILES['file']['tmp_name'], $filepath);
        $doc = new Document($entity, $id, $filename, time());


        echo json_encode([
            'error' => false,
            'doc' => $doc,
            'url' => $doc->getDocDownloadURL(),
            'title' => $title,
        ]);

        exit();
    }

    protected static function routeRenameDoc($hex){
        $app = \TDS\App::get();

        $key = str_pad('', SODIUM_CRYPTO_SECRETBOX_KEYBYTES ,"{$app::$auth->user->id}");
        $doc = $app::simpleDecrypt($hex, $key);
        if (! $app::NS($doc->className)::canEditDocuments()){
            echo json_encode([
                'error' => true,
                'message' => "Pb",
            ]);
            exit();
        }

        $document = new Document($doc->className, $doc->id, $doc->filename, $doc->timestamp);
        $document->rename($_POST['title']);
        echo $document->getDocDownloadURL();
    }

    protected static function routeDeleteDoc($hex){
        $app = \TDS\App::get();

        $key = str_pad('', SODIUM_CRYPTO_SECRETBOX_KEYBYTES ,"{$app::$auth->user->id}");
        $doc = $app::simpleDecrypt($hex, $key);
        if (! $app::NS($doc->className)::canEditDocuments()){
            echo json_encode([
                'error' => true,
                'message' => "Pb : Pas autorisé à supprimer ce document",
            ]);
            exit();
        }

        $document = new Document($doc->className, $doc->id, $doc->filename, $doc->timestamp);
        $document->delete();
        echo "Done";
    }


    public function redirect($url){
        $app = \TDS\App::get();
        /*
        $_SESSION["{$app::$appName}_pub"]=$app::$pub; // C'est pour conserver les publications 
        header('Location: '.$url);
        exit();
        */
        $app::$setURL = $url;
        $this->doMatch($url);
        exit();
    }


    protected static function error404(){
        $app = \TDS\App::get();
        echo $app::$viewer->render('error404.html.twig');
        exit();
    }

    private function getRouteFromArray($routeName, $array){
        foreach($array as $elm){
            if (is_array($elm)){
                $res = $this->getRouteFromArray($routeName, $elm);
                if ($res !== false ){
                    return $res;
                }
            } else {
                if ($elm->hasName($routeName)){
                    return $elm;
                }
            }
        }
        return false;
    }

    /**
     *  renvoie la Route qui porte le nom $routeName 
     *  false si elle n'existe pas;
     */
    public  function getRoute($routeName){
        return $this->getRouteFromArray($routeName, $this->routeList);
    }

    public  function updateRoute($routeName, $target){
        $route = $this->getRoute($routeName);
        if ($route ===  false){
            die("La route « {$routeName} » n'existe pas et ne peut pas être modifiée");
        }
        $route->setTarget($target);
    }

    public static function callFunc($callback, $args){
        $app = \TDS\App::get();
        $a = [];
        foreach($args as $ar){
            $a[]=$ar;
        }
        call_user_func_array($callback, $a);
    }


    public static function Admin(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.\TDS\App::$appName.'/admin');
    }

    public static function SuperAdmin(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.\TDS\App::$appName.'/superAdmin');
    }

    public static function Mailer(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.\TDS\App::$appName.'/admin/mailer');
    }

    public static function Gestionnaire(){
        $app = \TDS\App::get();
        $app::$router->redirect('/'.\TDS\App::$appName.'/gestionnaire');
    }

}

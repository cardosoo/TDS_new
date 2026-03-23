<?php
namespace TDS;

// use AltoRouter;

abstract class App {
    static public string        $service = '';
    static public string        $mail = 'olivier.cardoso@u-paris.fr';
    static public string        $webmestre = 'olivier.cardoso@u-paris.fr';
    static public string        $basePath = '';           // chemin de base (dans le système de fichier) de l'application 
    static public string        $appName = 'TDS';         // porte le nom de l'application
    static public string        $longAppName = "Tableau De Service";
    static public string        $webmaster = "<a href='mailto:Olivier.Cardoso@u-paris.fr'>Webmaster</a>";
    static public bool          $prod = true;             // indique que l'application est en mode production
    static public bool          $isCached = true;         // indique sur la requête provient du cache (???)
    static public array         $pathList = [];           // les chemins par défaut
    static public string        $currentYear = "";        // version de la base de données (normalement l'année en court)
    static public string        $officialYear = "";
    static public array         $historyYearList = [];
    static public bool          $isCurrentYear = true;
    static public array         $yearList;
    static public string        $baseName = "";           // le nom de la base de données
    static public string        $baseUser;
    static public string        $basePwd;
    static public Database      $db;                      // la connexion vers la base de données (à changer)
    static public               $router;                  // le routeur de l'application           
    static public               $viewer;
    static public Authenticate  $auth;
    static public Permission    $perm;                    // pour gérer les permissions sur les différentes fonctions du logiciel
    static public array         $cmpl = []; 
    static public string        $secretkey = "12345678901234567890123456789012";    
    static public array         $sqlList = [];
    static public string        $toCRUD = "";
    static public bool          $fromCli = false;
    static public \stdClass     $pub;                     // les variables publiques pour twig en particulier  
    static public string|null   $setURL= null;
    static public array         $hETD = [];
    static public array         $phaseList = [];
    static public string        $phase = "";
    static public array         $texte = ['correspondants' => 'correspondants',];
 
    static private $session_string = ""; 
    static private $originalLocales =[];
    abstract public static function loadFromUid($uid);
    abstract public static function getRoleList($user);
    
    static function init(string $dir, $close = false, $session=true, $appName = null){
        // pour mettre les différents messages sur les pages avec les différents niveaux d'utilisation
        self::$pub = new \stdClass();
        self::$pub->info = [];
        self::$pub->error = [];
        self::$pub->warning = [];
        self::$pub->success = [];

        self::$cmpl = [];

        self::$basePath = realpath("{$dir}/../");
        
        ini_set('default_charset','UTF-8');
        if ($session){
            self::openSession($close);
        }
        self::$fromCli = !is_null($appName);
        if (self::$fromCli){
            self::$appName = $appName;
            $_SERVER['REMOTE_ADDR'] = '192.168.1.254'; 
        } else {
            self::buildAppName();
        }
        $dir = realpath("{$dir}/../".self::$appName."/");
	    self::setIsCached();
        //self::forRedirect(); Je pense que ce n'est plus nécessaire
        self::setDefaultPath($dir);
        self::autoloader();
        self::$perm = new Permission();
        self::setPermission();
    }

    static function setPermission(){
    }

    public static function get():String|App{
        return self::NSC('App');
    }

    public static function setLongName($longName){
        self::$longAppName = $longName;
    }

    public static function setWebmaster($webmaster){
        self::$webmaster = $webmaster;
    }

    public static function initRouter(){
        $routerName = '\\'.self::$appName.'\\Router';
        self::$router = new $routerName;
        self::$router->setBasePath("/".self::$appName);
    }


    public static function initViewer(){
        $viewerName = '\\'.self::$appName.'\\Viewer';
        self::$viewer = new $viewerName;
    }


    /***
     * 
     */
    public static function autoloader(){
        spl_autoload_register(
            function ($className) { 
                $app = \TDS\App::get();
                $parts = preg_split('#\\\#', $className);
                $part = array_pop($parts);
                $part = str_replace('interface_', '', $part); 
                $fileName = $part.'.php';
                $mainNamespace = array_shift($parts);
                $path = implode(DIRECTORY_SEPARATOR, $parts);
                if ( 'TDS' === $mainNamespace ) { // si le namespace commence par TDS alors on cherche dans TDS/Classes/...
                    $basePath = $app::$basePath.DIRECTORY_SEPARATOR.'TDS/Classes';
                } elseif ('Model' === $mainNamespace) { // si cela commence par {$appName}/Model alros on va chercher dans TDS/{appName}/Model/...
                    $basePath = $app::$basePath.DIRECTORY_SEPARATOR.self::$appName.DIRECTORY_SEPARATOR.'Model';
                } else {
                    $secondNamespace = array_shift($parts);
                    if (in_array($secondNamespace, ['Model' ,'Controllers' ])) {
                        $basePath = App::$basePath.DIRECTORY_SEPARATOR.$mainNamespace;
                    } else  {
                        $basePath = App::$basePath.DIRECTORY_SEPARATOR.$mainNamespace.DIRECTORY_SEPARATOR.'Classes';
                    }
                }
                $filePath = $basePath.DIRECTORY_SEPARATOR.(empty($path)?"":$path.DIRECTORY_SEPARATOR).$fileName;
                if (file_exists($filePath)){
                    require_once($filePath);
                    return;
                }
            }
        ); 
    }

    /**
     * @param string $year l'année en cours (ce n'est pas forcement numerique)
     */
    public static function setCurrentYear(string $year){
        self::$currentYear = $year;
        self::setBaseName();
    }


    /**
     * Permet d'indiquer à l'application quels sont les années qui peuvent
     * être utilisées. 
     * L'année courante est fixée en fonction de ce que l'on trouve dans
     * la variable de session 'currentYear'
     * 
     * @param string $officialYear l'année officielle fixée pour l'utilisation
     * du logicile
     * 
     * @param array syearList la liste des années qui peuvent être utilisées.
     * 
     * @return string renvoie l'année qui a été sélectionnée (en via la
     * variable de session) ou sinon l'année officielle 
     * 
     */
    public static function setYear(string $officialYear, array $yearList){
        
        $currentYear= $_SESSION['currentYear']??$officialYear;
        self::setCurrentYear($currentYear);
        self::$officialYear = $officialYear;
        self::$yearList = $yearList;
        self::$isCurrentYear = $officialYear == $currentYear;
        return $currentYear;
    }

    /**
     * @param string $baseName le nom de la base de données de l'annéeCourante
     */
    public static function setBaseName(string $baseName = null){
        if (is_null($baseName)){
            $baseName = self::buildBaseName();
        }
        self::$baseName = $baseName;
    }

    /**
     * 
     */
    public static function openDatabase(string $host = "localhost"){
        self::$db = new Database(self::$baseName, self::$baseUser, self::$basePwd, $host);
    }

    /**
     * Le nom de l'application est donné par le nom de l'hote (foire ou ts ou tssdv ou je ne sais trop quoi d'autre encore)
     * Seuls les noms d'hotes enregistrés sont autorisés
     * 
     * 04/04/2021 -> Changement de stratégie, maintenant le nom de l'application est donnée par le premier élemént du 
     * 
     * 
     */
    private static function buildAppName(){
        $php_self = $_SERVER['PHP_SELF'];
        $php_self = $_SERVER['REQUEST_URI'];
                
        if ($php_self=="/"){  // si il n'y a rien devant, on suppose que l'application est donné par le premier élément du nom de domaine
            $http_host = $_SERVER['HTTP_HOST'];
            $app = explode(".", $http_host); // le nom de l'application est donné par le nom nom de l'hote
            
            $name = $app[0] =='ts'?'tssdv':$app[0];
            header("Location: /{$name}");
            exit();
        }


        $app = explode("/", $php_self);
        // ici on vérifie que le nom de l'application n'est qu'avec des alphanumeriques
        // et que le nom de l'application ne contient pas plus de 20 caractères
        if (! ctype_alnum($app[1])){
            die("Cette application {$app[1]} n'est pas configurable -1 ");
        }
        if (strlen($app[1]) >20) {
            die("Cette application {$app[1]} n'est pas configurable -2");
        }

        self::$appName = $app[1];
        // Ici on vérifie que le dossier existe effectivement et on se place à l'interieur de ce dossier
        if (is_dir("../{$app[1]}") ) return;
        var_dump(getcwd());
        die("Cette application {$app[1]} n'est pas configurée... -3");	
    }

    /**
     * 
     */
    private static function buildBaseName(){
        return self::$appName.self::$currentYear;
    }

    /**
     * Permet de fixer les chemins par défaut
     */
    private static function setDefaultPath($dir){
        self::$pathList = ['route' => parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)]; // le chemin pour le routage !
        self::setBaseDir($dir);
        $plusPath = realpath("{$dir}/../../TDS_plus/");
        self::setPlusPath($plusPath);
        $appName = self::$appName;
        self::setPhotosPath("{$plusPath}/{$appName}/photos/");
        self::setLogPath("{$plusPath}/{$appName}/log/");
        $secretPath = realpath("{$dir}/../../TDS_secret/{$appName}/");
        self::setSecretPath($secretPath);
    }

    public static function setBaseDir($path){
        self::$pathList['base']= realpath($path);
    }

    public static function setPlusPath($path){
        self::$pathList['plus']= realpath($path);
    }

    public static function setSecretPath($path){
        self::$pathList['secret']= realpath($path);
    }
    /**
     * pour mettre le path de BD2 mais il faudrait ne pas avoir à l'utiliser
     */
    public static function setBD2Path($path){
        self::$pathList['BD2']= realpath($path);
    }

    /**
     * Pour mettre le path des photos
     */
    public static function setPhotosPath($path){
        self::$pathList['photos'] = realpath($path);
    }

    /**
     * Pour mettre le path du log
     */
    public static function setLogPath($path){
        self::$pathList['log'] = realpath($path);
    }

    public static function getPageURL() {
        $pageURL = 'http';
    
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
    
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
    
        return $pageURL;
    }


    /** 
     * Permet de faire en sorte que les paramètres en GET passe bien lors d'un redirect
     * Je ne sais plus très bien pourquoi il y a besoin de le faire, ni si c'est bien 
     * d'un point de vue de la sécurité
    private static function forRedirect(){
        if (isset($_SERVER['REDIRECT_QUERY_STRING'])){ 
            parse_str($_SERVER['REDIRECT_QUERY_STRING'],$_GET);
        }
    }
     */
    /**
     * permet d'ouvrir la session
     * 
     * @param boolean $close Indique si la session doit-être fermée aussitôt qu'elle est ouverte. 
     *                       Cela permet de rendre le script non-bloquant si on sait qu'il n'y a 
     *                       pas besoin de modifier la variable de session.  
     */
    private static function openSession( $close = false){
        
        $params = ['cookie_lifetime' => 30*86400, 'gc_maxlifetime' => 30*86400]; // ['cookie_lifetime' => 2000 /* 86400*/,];
        if ($close){
           $p['read_and_close'] = true;
        }
        @session_start($params);
        $count = 0;
        while (!isset($_SESSION)){
            $count++;
            @session_start($params);
        };
        self::$session_string = session_encode();
    }

    /**
     *  permet de réouvrir une session.
     *  Cela est utile lorsque la session contient des variables qui sont des instances de classes qui
     *  ne sont pas encore définies au moment de l'ouverture initiale de la session
     */
    public static function reopenSession(){
        $app = self::get();
        try {
            session_decode(self::$session_string);
        } catch (\Exception $e) {
            var_dump("c'est pas très bon, mais on va faire avec");
            var_dump("c'est pas très bon, mais on va faire avec");
        }
        if (isset($_SESSION["{$app::$appName}_pub"])){
            $app::$pub = $_SESSION["{$app::$appName}_pub"];
            unset($_SESSION["{$app::$appName}_pub"]); 
        }
    }

    /**
     * Permet de mémoriser si la requête est issue du cache ou pas
     * C'est utile par exemple pour savoir qu'il faut recharger la photo lorsqu'on vient de la modifier
     */
    private static function setIsCached(){
        $headers = getallheaders();
        if (! isset($headers['Cache-Control'])){
            self::$isCached = false;
        } else {
            self::$isCached =  false === stripos($headers['Cache-Control'],'no-cache');
        }
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline ){
        if (self::$prod){
            return false;
        }
    
        var_dump([
            'pwd' => getcwd(),
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline,
        ]);

        if (!(error_reporting() & $errno)) {
            // Ce code d'erreur n'est pas inclus dans error_reporting(), donc il continue
            // jusqu'au gestionaire d'erreur standard de PHP

            echo "Pas pris en compte ??? : [$errno] $errstr<br />\n";

            return false;
        }
    
        // $errstr doit peut être être échappé :
        $errstr = htmlspecialchars($errstr);
    
        switch ($errno) {
            case E_ERROR:
                echo "<b>Mon ERREUR</b> [$errno] $errstr<br />\n";
                echo "  Erreur fatale sur la ligne $errline dans le fichier $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Arrêt...<br />\n";
                exit(1);
    
            case E_WARNING:
                echo "<b>Mon ALERTE</b> [$errno] $errstr<br />\n";
                break;
    
            case E_NOTICE:
                echo "<b>Mon AVERTISSEMENT</b> [$errno] $errstr<br />\n";
                break;
    
            default:
                echo "Type d'erreur inconnu : [$errno] $errstr<br />\n";

                break;
            }
    
        /* Ne pas exécuter le gestionnaire interne de PHP */
        return false;
    }
    


    /**
     * Fait le nécessaire pour passer en mode Production ou Développement
     * 
     * @param bool $mode indique si l'application est mode Production
     */
    public static function setProdMode($mode=true){
        self::$prod = $mode;

        if (self::$prod){
            error_reporting(0);
        } else {
            //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
            error_reporting(E_ALL);
            // ini_set('xdebug.overload_var_dump','2');
            ini_set('xdebug.mode','develop');
            ini_set('xdebug.var_display_max_children','128');
            ini_set('xdebug.var_display_max_data','2048');
            ini_set('xdebug.var_display_max_depth','10');
//            ini_set('xdebug.collect_includes','1'); //     ; Noms de fichiers
//            ini_set('xdebug.collect_params','2'); //       ; Paramètres de fonctions / méthodes
            ini_set('xdebug.show_exception_trace','0');        
        }
    }

    /**
     * Fait le nécessaire pour passer en mode Production ou Développement
     * 
     * @param boolean $mode indique si l'application est mode Développement
     */
    public static function setDevMode($mode = true){
        self::setProdMode(!$mode);
    }

    public static function setSecretkey($key){
        if (strlen($key)>=SODIUM_CRYPTO_SECRETBOX_KEYBYTES){
            self::$secretkey = substr($key,0,SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        } else {
            self::$secretkey = str_pad($key,SODIUM_CRYPTO_SECRETBOX_KEYBYTES,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
        }
    }

    public static function NS($entityName=''){
        $appName = self::$appName;
        return "\\{$appName}\\Model\\{$entityName}";
    }

    public static function NSC($className=null){
        $appName = self::$appName;
        return "\\{$appName}\\{$className}";
    }

    public static function load(string $entityName, int $id){
        return self::call($entityName, 'load', [$id]);
    }

    public static function call(string $entityName, string $func, array $args){
        $func = self::NS($entityName)."::{$func}";
        $obj = call_user_func_array ($func, $args);
        return $obj;
    }

    public static function doLog($what){
        $app = \TDS\App::get();

        $who = self::$auth->isAuth?self::$auth->uid:'noAuth';
        $IP = \TDS\Utils::getIP();
        $log = join("\t", [
            $what,
            $who,
            $IP,
            date('c'),
            $_SERVER['REQUEST_METHOD'],
            $_SERVER["REQUEST_URI"],
        ])."\n";
        file_put_contents(self::$pathList['log']."/TDS-{$app::$baseName}.log", $log, FILE_APPEND);
    }


    public static function doLogSQL($sql){
        $app = \TDS\App::get();

        $who = self::$auth->isAuth?self::$auth->user->uid:'noAuth';
        $IP = \TDS\Utils::getIP();
        str_replace("\n", '\n',$sql);
        $log = join("\t", [
            "SQL",
            $who,
            $IP,
            date('c'),
            $sql,
        ])."\n";
        file_put_contents(self::$pathList['log']."/TDS-{$app::$baseName}.log", $log, FILE_APPEND);
        file_put_contents(self::$pathList['log']."/sql-{$app::$baseName}.log", $log, FILE_APPEND);
    }



    public static function simpleEncrypt($message){
        $app = \TDS\App::get();

        $block_size = 16;
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $json = json_encode($message);
        $padded = sodium_pad($json, $block_size);
        $key = str_pad('', SODIUM_CRYPTO_SECRETBOX_KEYBYTES , $app::$auth->uid);
        $encrypted = sodium_crypto_secretbox($padded, $nonce, $key);
        $bin = $nonce . $encrypted;
        $hex = bin2hex($bin);
        return $hex;
    }
    
    
    public static function simpleDecrypt($hex){
        $app = \TDS\App::get();

        $block_size = 16;
        
        $bin = hex2bin($hex);
        $nonce = mb_substr($bin, 0, 24, '8bit');
        $encrypted = mb_substr($bin, 24, null, '8bit');

        $key = str_pad('', SODIUM_CRYPTO_SECRETBOX_KEYBYTES , $app::$auth->uid);
        $padded = sodium_crypto_secretbox_open($encrypted, $nonce, $key);
        if ($padded===false){
            var_dump("Vous n'êtes sans doute pas le destinataire de ce document...");
            exit();
        }
        $json = sodium_unpad($padded, $block_size);
        $message = json_decode($json);       
        return $message;
    }

    public static function isActive($obj){
        return $obj->actif;
    }


    public function testFunction(){
        var_dump(('testFunction'));
        exit();
    }

    public static function normalizeText(String $st){
        return strtolower(iconv('utf8', 'ascii//TRANSLIT', $st));
    }

    public static function setLocale($locale){

        self::$originalLocales = explode(";", setlocale(LC_ALL, 0));
        setlocale(LC_ALL, $locale);
    }
    
    public static function restoreLocale(){
        setlocale(LC_ALL, self::$originalLocales);
        return;
        
        foreach (self::$originalLocales as $localeSetting) {
            if (strpos($localeSetting, "=") !== false) {
              list ($category, $locale) = explode("=", $localeSetting);
            }
            else {
              $category = LC_ALL;
              $locale   = $localeSetting;
            }
            setlocale($category, $locale);
        }
    }
}

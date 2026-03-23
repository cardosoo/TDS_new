<?php
use TDS\App;
use TDS\Model;

$params = getopt ("a:i::y" ,["app:", "inherit::", "year"] );
$appName = $params['a'] ?? $params['app'] ?? null;
$inheritName = $params['i'] ?? $params['inherit'] ?? null;
$year = $params['y'] ?? $params['year'] ?? "";

var_dump([
    "params"=> $params, 
    "appName" => $appName,
    "inheritName" => $inheritName
]);

if (is_null($appName)){
    var_dump("Le paramètre -a=appName  ou --app=appName est requis pour spécifier l'application");
    exit();
}

$baseName     = __DIR__."/../{$appName}";
$basePlusName = __DIR__."/../../TDS_plus/{$appName}";

var_dump($baseName);
var_dump($basePlusName);

if ( file_exists($baseName) || (file_exists($basePlusName))){
    var_dump("Il semble que l'application {$appName} existe déjà.");
    exit();
}


mkdir("{$baseName}", 0777, true);
$baseName = realpath($baseName);
mkdir("{$basePlusName}", 0777, true);
$basePlusName = realpath($basePlusName);


// création de la structure des dossiers
mkdir("{$baseName}/Classes", 0777, true);
mkdir("{$baseName}/Controllers", 0777, true);
mkdir("{$baseName}/Model", 0777, true);
mkdir("{$baseName}/twig/cache", 0777, true);
mkdir("{$baseName}/twig/templates/CRUD", 0777, true);
mkdir("{$baseName}/twig/templates/layout", 0777, true);
mkdir("{$baseName}/twig/templates/textes", 0777, true);

mkdir("{$basePlusName}/log", 0777, true);
mkdir("{$basePlusName}/photos", 0777, true);

// creation des fichiers de configuration de base
// config.php
file_put_contents("{$baseName}/config.php","<?php
use \\{$appName}\\App;

// permet de faire les initialisations
if (PHP_SAPI !== 'cli'){
    include_once '../TDS/urlRewriting.php'; // permet de faire le routage
}

// mettre ci-dessous ce qui doit être fait après les initialisation
App::setSecretkey('secret');
App::setLongName('Tableau de service de Base');
App::setWebmaster('<a href=\'mailto:Olivier.Cardoso@u-paris.fr?subject=[TDS/{$appName}&body='.urlencode(\"\\n\\n\\n\\n-- Merci de ne pas supprimer les lignes ci-dessous --\\n\".(print_r(\$_SERVER,true))).'>Message au Webmaster</a>');
App::setProdMode(false);

\$officialYear = '';
\$yearList = [];
\$year = App::setYear(\$officialYear, \$yearList);

App::\$baseUser = 'olivier';
App::\$basePwd = 'mcsuap7';
App::openDatabase();
");



// model.php
file_put_contents("{$baseName}/model.php","<?php
namespace {$appName};

use \\TDS\\Model\\Model;
use \\TDS\\Model\\Entity;
use \\TDS\\Model\\Field;
use \\TDS\\Model\\ManyToMany;
use \\TDS\\Model\\View;

if (! isset(Model::\$appName)){
    Model::\$appName = __NAMESPACE__;
}

".(! is_null($inheritName)?"include '../{$inheritName}/model.php';
Model::\$parentApp = '$inheritName';" :
"
Model::\$idName = 'id';

\$role = Model::addEntity(new Entity('Role'));
\$role->addField(new Field('name', Field::STRING));

\$user = Model::addEntity(new Entity('User'));
\$user->addField(new Field('uid', Field::STRING));
\$user->addField(new Field('name', Field::STRING));

\$actAs = Model::addEntity(new ManyToMany('actAs', \$user, \$role));

"));

// router.php
file_put_contents("{$baseName}/router.php","<?php
namespace {$appName};

use \\TDS\\Route;

\$CN = '\\\\'.__NAMESPACE__.'\\\\Controllers\\\\';

".(! is_null($inheritName)?"include '../{$inheritName}/router.php';" :
"
App::\$router->routeList = [
    'public' => [ // zone publique sans authentification
      'gen' => [
        new Route('GET', '/', \$CN.'Gen::home'),
        new Route('GET','/texte/[*:texte]', \$CN.'Gen::texte'),
      ],
      'auth' => [
        new Route('GET','/directLink/[*:hex]', \$CN.'Auth::directLink'),
      ],
      'test' => [
      ]
  ],
    'withAuth' => [ // zone avec authentification 
      'auth' => [
        new Route('GET','/auth/login', \$CN.'Auth::login'),   // c'est bizarre, mais c'est un moyen simple de déclencher l'authentification
        new Route('GET','/alive', \$CN.'Auth::alive'),        // c'est bizarre, mais c'est un moyen simple de déclencher l'authentification
        new Route('GET','/auth/logout', \$CN.'Auth::logout'),
      ],
      'user' => [
      ],
      'search' => [
      ],
    ],
    'private' => [ // zone privée avec authentification et présence dans la base de données
    ],
    'restrict' => [ // zone à accès restreint en fonction des droits de l'utilisation authentifié
      'Admin' => [
          new Route('GET','/admin', \$CN.'Admin::home'),
      ],
      'SuperAdmin' => [
      ],
    ],
  ];  
  "));


$inheritNamespace = $inheritName ?? 'TDS';

file_put_contents("{$baseName}/Classes/App.php","<?php
namespace {$appName};

use \\{$appName}\\Model\\User;

class App extends \\{$inheritNamespace}\\App {

".(! is_null($inheritName)?"" : "
    public static function loadFromUid(\$uid){
        return User::loadOneWhere(\"uid = '{\$uid}'\");
    }

    public static function getRoleList(\$user){
        \$roleList = [];
        foreach(\$user->actasList as \$role){
            \$roleList[\$role->role->name]=true;
        }
        return \$roleList;
    }")."
}");


file_put_contents("{$baseName}/Classes/Viewer.php","<?php
namespace {$appName};

class Viewer extends \\{$inheritNamespace}\\Viewer {

    function __construct() {
        parent::__construct();
        \$this->loader->prependPath(__DIR__.'/../twig/templates');
    }

    protected function getAppGlobals(){
        \$r =  parent::getAppGlobals(); 
        return \$r;
    }

}
");


file_put_contents("{$baseName}/Classes/Router.php","<?php
namespace {$appName};

use \\{$appName}\App;
use \\TDS\Authenticate;

class Router extends \\{$inheritNamespace}\\Router {

    // Ce serait bien de trouver un moyen de rendre ce truc indépendant 
    // de la classe finale... on doit pouvoir s'en sortir à partir du
    // nom de l'appli peut-être mais on va perdre la cascade alors.
    // ou alors si cela se trouve cela fonctionne tel quel ?
    // à voir plus tard.
    protected static function routeAsset(\$asset){
        if (App::\$router->doRouteAsset(\$asset, __DIR__. \"/../assets\" )){
            return true;
        }
        return parent::routeAsset(\$asset);
    }

}
");

file_put_contents("{$baseName}/Controllers/Gen.php","<?php
namespace {$appName}\\Controllers;

use \\{$appName}\\App;

class Gen extends \\{$inheritNamespace}\\Controller {

    public static function home(){
        \$app = \TDS\App::get();
        \$app::\$router->redirect(\"/{\$app::\$appName}/texte/introduction\");
    }

    public static function texte(\$texte){
        \$app = \TDS\App::get();
        \$texte = filter_var(\$texte, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
        \$app::\$cmpl['withMathjax'] = true;
        \$app::\$cmpl['withMarkdown'] = true;
        
        echo \$app::\$viewer->render('textes.html.twig', [ 'md' => \"textes/{\$texte}.md.twig\" ]);
    }

}");

file_put_contents("{$baseName}/Controllers/Auth.php","<?php
namespace {$appName}\\Controllers;

use \\{$appName}\\App;

class Auth extends \\TDS\\Controller {

    public static function login(){
        \\{$appName}\\Controllers\\Gen::home();
    }

    public static function alive(){
        exit();
    }

    public static function logout(){
        App::\$auth->deconnexion();
    }

    public static function directLink(\$hex){
        \$message = App::\$auth->directLink(\$hex);
        if ( time()-\$message->timestamp < (60*60*24)*30 ){  // on limite l'utilisation du lien à 30 jours...
            App::\$auth->forceAuth(\$message->uid);
            if (App::\$auth->isInBase()){
                header(\"Location: /texte/introduction\");
                exit();
            }
            header(\"Location: /\");
            exit(); 
        }
        var_dump(\"Après l'heure c'est plus l'heure\");
        var_dump(\"Le lien est périmé\");
    }
}
");

file_put_contents("{$baseName}/Controllers/Admin.php","<?php
namespace {$appName}\\Controllers;

use \\{$appName}\\App;

class Admin extends \\TDS\\Controller {

    public static function home(){
        var_dump(\"Rien pour le moment\");
        exit();
    }
}
");



file_put_contents("{$baseName}/twig/templates/textes/introduction.md.twig", "

<div class='w3-card  w3-round w3-xlarge w3-center'>
Bienvenue dans  {{App.appName}}
</div>

");
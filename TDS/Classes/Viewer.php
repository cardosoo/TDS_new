<?PHP
namespace TDS;

use \Twig\TwigFilter;
use \Twig\Environment;
use \Twig\Extension\CoreExtension;
use \Twig\Loader\FilesystemLoader;
use \Twig\Extension\DebugExtension;
// use \Twig\Extra\Intl\IntlExtension;
use \Twig\Extra\String\StringExtension;
use \Twig\Extra\Markdown\DefaultMarkdown;
use \Twig\Extra\Markdown\MarkdownRuntime;
use \Twig\Extension\StringLoaderExtension;
use \Twig\Extra\Markdown\MarkdownExtension;
use \Twig\RuntimeLoader\RuntimeLoaderInterface;


class Viewer {

    public $load;
    public Environment $twig;
    private bool $output = true;

    public function __construct(){
        $app = \TDS\App::get();

        $this->loader = new FilesystemLoader( __DIR__.'/../twig/templates');
        $this->twig = new Environment($this->loader, [
            'cache' => false, //'twig/cache',
            'debug' => ! $app::$prod, // si on est pas en prod on utilise le debug (sinon non !)
        ]);
        
//        $this->twig->addExtension(new IntlExtension());      // pour les dates
        $this->twig->addExtension(new MarkdownExtension());  // pour le markdown
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addExtension(new StringLoaderExtension());
        $this->twig->addExtension(new StringExtension());
        $this->twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
            public function load($class) {
                if (MarkdownRuntime::class === $class) {
                    return new MarkdownRuntime(new DefaultMarkdown());
                }
            }
        });
        
        $this->twig->getExtension(CoreExtension::class)->setNumberFormat(2, ',', '');
        
        $this->twig->addFilter(new TwigFilter('u8', 'utf8_encode'));
        $this->twig->addFilter(new TwigFilter('f2', '\TDS\Utils::fNombre'));
        $this->twig->addFilter(new TwigFilter('e2', '\TDS\Utils::eNombre'));
        $this->twig->addFilter(new TwigFilter('json_decode', '\TDS\Utils::json_decode'));
        $this->twig->addFilter(new TwigFilter('cm', '\TDS\Utils::clearMarkdown'));
        
        $this->output = true;

//        $this->twig->addGlobal('pub', $app::$pub);
//        $this->twig->addGlobal('debug', ! $app::$prod);
                
    }

    protected function getAppGlobals(){
        $app = \TDS\App::get();
        return [
            "appName" => $app::$appName,
            "longAppName" => $app::$longAppName,
            "webmaster" => $app::$webmaster,
            "currentYear" => $app::$currentYear,
            "officialYear" => $app::$officialYear,
            "yearList" => $app::$yearList,
            "historyYearList" => $app::$historyYearList,
            "isCurrentYear" =>$app::$isCurrentYear, 
            "debug" => ! $app::$prod,
            "pub" => $app::$pub,
            "cmpl" => $app::$cmpl,
            "auth" => $app::$auth,
            "sqlList" => $app::$sqlList,
            "DB" => $app::$db,
            "toCRUD" => $app::$toCRUD,
            'router' => $app::$router,
            'appMail' => $app::$mail,
            'app' => new $app(),
        ];
    }

    public function render(string $template, array $variables = []){
        if (! $this->output) return;
        $r = $this->getAppGlobals();
        $this->twig->addGlobal('App', $r);
        return $this->twig->render($template, $variables);
    }

    public function setNoOutput(){
        $res = $this->output;
        $this->output = false;
        return $res;
    }
    public function setOutput($output){
        $this->output = $output;
    }


}
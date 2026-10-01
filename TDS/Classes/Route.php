<?PHP
namespace TDS;

class Route{
    public string $method;
    public string $route;
    public string $target;
    public ?string $name;

    public function __construct(string $method, string $route, string $target, ?string $name=null){
        $app = \TDS\App::get();
        $this->method = $method;
        $this->route = $route;
//        $this->target = $app::$router->getNamespace().$target;
        $this->setTarget($target);
        $this->name = $name; 
    }

    public function hasName(string $routeName){
        return $routeName == $this->name;
    }

    public function setTarget(string $target, $debug=false){
        $app = \TDS\App::get();


        $target = $app::$router->getNamespace().$target;
        if ($debug){
            var_dump(['app' => $app, 'namespace' => $app::$router->getNamespace(), 'target' => $target]);
        }
        $this->target = $target;
    }
}
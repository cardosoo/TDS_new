<?PHP
namespace TDS;

class Route{
    public $method;
    public $route;
    public $target;
    public $name;

    public function __construct(string $method, string $route, string $target, string $name=null){
        $app = \TDS\App::get();
        $this->method = $method;
        $this->route = $route;
//        $this->target = $app::$router->getNamespace().$target;
        $this->setTarget($target);
        $this->name = $name; 
    }

    public function hasName($routeName){
        return $routeName == $this->name;
    }

    public function setTarget($target){
        $app = \TDS\App::get();
        $this->target = $app::$router->getNamespace().$target;
    }
}
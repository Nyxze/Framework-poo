<?php 
namespace m2i\framework;
use m2i\framework\Request;

class Router{

    private string $url;

    private string $queryString ='';
    
    private array $routes = [];
    private array $params = [];

    public string $controllerName;
    public string $methodName;
    
    public function __construct(array $routes){

        $this->routes = $routes;
        $uri = filter_input(INPUT_SERVER,"REQUEST_URI");
        $parts = explode("?",$uri);
        $this->url = $parts[0];
        if(count($parts)>1){

            $this->queryString = $parts[1];
        }
        $this->parseUrl();

    }
   
    private function parseUrl(){


        foreach($this->routes as $item=>$val){
            $regex = "#^{$item}$#";
            if(preg_match($regex,$this->url,$matches)){
                array_shift($matches);
                $this->params=$matches;
                $this->controllerName = $val[0];
                $this->methodName = $val[1];
                $found = true;
                break;
            }

        }
        if(! $found){
            throw new NotFoundException("Round not found");
        }
    }

    function run(array $container){

        $controller = new $this->controllerName(new Request($this->queryString),$container);
        $action = $this->methodName;
        $controller->$action(...$this->params);


    }

}



?>
<?php 
namespace m2i\framework;


class AbstractController{

    protected Request $request;

    public function __construct(Request $request, array $container){
        $this->container = $container;
        $this->request = $request;
    }
}

?>
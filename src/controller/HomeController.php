<?php 
namespace m2i\app\controller;

use m2i\framework\AbstractController;

class HomeController extends AbstractController{

    public function index(){
        echo $this->request->get("id");
        echo json_encode($this->container["dao.post"]->findAll()->getAllAsArray());
       
        echo "home";
    }
    public function list(){
        echo "News";
    }

    public function details($id,$title){

        echo json_encode($this->container["dao.post"]->findOneById($id)->getOneAsObject());
    }

}

?>
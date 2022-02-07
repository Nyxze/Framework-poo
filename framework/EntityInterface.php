<?php 
namespace m2i\framework;
interface EntityInterface{

    public function getId():?Int;
    public function setId(int $id):self;
    
}
?>
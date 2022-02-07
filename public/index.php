<?php

use League\CommonMark\CommonMarkConverter;
use m2i\app\dao\PostDAO;
use m2i\app\dao\UserDAO;
use m2i\app\model\User;
use m2i\framework\Router;
use m2i\app\controller\HomeController;
use m2i\app\model\Post;
require "../vendor/autoload.php";

$markDown = new CommonMarkConverter();

$user = new User();
$user->setUserName("Renne");
$pdo = new PDO ("mysql:host=localhost;dbname=forum_2022;charset=utf8","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
// $userDAO = new UserDAO($pdo);
// $userDAO->save($user);
// var_dump($userDAO->findAll()->getAllAsObject());

// $message = "# Hello
// - dsad
// - dsadas
// ";

// echo $markDown->convert($message);
// 

$routes = [

    "/acceuil"=>[HomeController::class,"index"],
    "/news"=>[HomeController::class,"list"],
    "/details/(\d+)/([a-z]+)"=>[HomeController::class,"details"]

];
$container = [];
$container["pdo"] = $pdo;
$container["dao.post"] = new PostDAO($pdo);

$router = new Router($routes);
$router->run($container);



$user = new User();
$user->setUserName("Yoloswag");

$postDAO = new PostDAO($pdo);
$post = new Post();
$post->setTitle("ALED OSKOUR")->setContent("GBSOIN DED")->setParentId(1)->setUser($user);


<?php 
namespace m2i\app\dao;
use m2i\app\model\User;
use m2i\framework\AbstractDAO;
use \PDO;
class UserDAO extends AbstractDAO {

    public function __construct(PDO $pdo)

    {
        parent::__construct($pdo,"users",User::class);    
    }

}


?>
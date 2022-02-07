<?php 
namespace m2i\app\dao;
use m2i\app\model\Post;
use m2i\app\model\User;
use m2i\framework\AbstractDAO;
use m2i\framework\EntityInterface;
use \PDO;
class PostDAO extends AbstractDAO {

    private ?UserDAO $userDAO = null; 
    public function __construct(PDO $pdo)

    {
        parent::__construct($pdo,"posts",Post::class);
        
            
    }

     /**
     * Get the value of userDAO
     *
     * @return ?UserDAO
     */
    public function getUserDAO(): ?UserDAO
    {   if($this->userDAO == null){
        $this->userDAO = new UserDAO($this->pdo);
    }
        return $this->userDAO;
    }

    public function hydrate(array $data){
        $post = parent::hydrate($data);
    
        $userDAO = $this->getUserDAO();
        $post->setUser($userDAO->findOneById($data["user_id"])->getOneAsObject());
      

        $post->setAnswers(
            $this->find(["parent_id"=>$data["id"]])
                    ->getAllAsObject()
        );


        return $post;

    }

    protected function manageAssociation(Post $post):void{  
        $user = $post->getUser();
        if($user){
            $userDAO = $this->getUserDAO();
            $userDAO->save($user);

        }
        

    }

    public function update(EntityInterface $entity):void{
        $this->manageAssociation($entity);
        parent::update($entity);

    }
    public function insert(EntityInterface $entity):void{
        $this->manageAssociation($entity);
        parent::insert($entity);

    }


   
}


?>
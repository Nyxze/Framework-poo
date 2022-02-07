<?php
namespace m2i\framework;

use DateTime;
use \PDO;
use \PDOException;
use \PDOStatement;


abstract class  AbstractDAO {

    protected PDO $pdo;
    protected array $foreignKeys =  [];
    protected PDOStatement $statement;
    protected string $tableName;
    protected string $entityClassName;

    public function __construct(PDO $pdo,string $tableName, string $entityClassName) {
        $this->pdo = $pdo;
        $this->tableName = $this->getSecureValue($tableName);
        $this->entityClassName = $entityClassName;
    }

    private function getSecureValue($value) {

        return str_replace([";","`"],["",""],$value);
    }
    public function findAll(array $orderBy = [], array $limit = []): self {
        $sql = "SELECT * FROM `{$this->tableName}` ";

        $sql .= $this->getOrderBy($orderBy);
        
        if(count($limit)> 0){
            $limit = array_map(function($item){ 
                return (int) $item;
            }, $limit);

            $sql .= " LIMIT {$limit["limit"]}";

            if(array_key_exists("offset", $limit)){
                $sql .= " OFFSET {$limit["offset"]}";
            }
        }

        $this->statement = $this->pdo->prepare($sql);
        $this->statement->execute();
        return $this;
    }

    private function getOrderBy(array $orderBy){
        $sql = "";
        if (count($orderBy) > 0) {
            $sql = " ORDER BY ";
            $orderCols = [];
            foreach ($orderBy as $colName => $order) {
                $orderCols[] = "$colName $order";
            }
            $sql .= implode(", ", $orderCols);
        }
        return $sql;
    }

    private function getLimit(array $limit){
        $sql = "";
        if (count($limit) > 0) {
           $limit = array_map(function ($item){ 
               return (int) $item;
        
        },$limit);
        $sql.= "LIMIT {$limit["limit"]}";
    }
        return $sql;
    }

    public function findOneById(int $id): self {
        $sql = "SELECT * FROM  `{$this->tableName}` WHERE id=?";
        $this->statement = $this->pdo->prepare($sql);
        $this->statement->execute([$id]);
        return $this;
    }

    public function find( array $search = [], 
                          array $orderBy = [], array $limit = []): self {
        $sql = "SELECT * FROM  `{$this->tableName}` ";
        $searchFields = [];
        if(count($search) > 0){
            $sql .= "WHERE ";
            $searchFields = array_map(function($item){
                return "{$item} = :{$item}";
            }, array_keys($search));

            $sql .= implode(" AND ", $searchFields);
        }

        $sql .= $this->getOrderBy($orderBy);
        $sql .= $this->getLimit($limit);
        $this->statement = $this->pdo->prepare($sql);
        $this->statement->execute($search);
        return $this; 
    }

    public function getOneAsArray(): array {
        $data = $this->statement->fetch(PDO::FETCH_ASSOC);
        if($data){
            return $data;
        } else {
            throw new NotFoundException("Pas de données pour cette requête");
        }
    }

    public function getOneAsObject(){
        $data = $this->getOneAsArray();
        return $this->hydrate($data);
    }

    public function getAllAsArray(): array {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAsObject(): array{
        $data = $this->getAllAsArray();
        $objectList = [];
        foreach($data as $row){
            $objectList[] = $this->hydrate($row);
        }
        return $objectList;
    }

    public function snakeToCamelCase(string $name){
        $nameParts = explode("_", $name);
        $camel = array_shift($nameParts);
        $nameParts = array_map(function($item){ return ucfirst($item);}, $nameParts);

        return $camel . implode("", $nameParts);
    }
    public function camelToSnakeCase(string $name):string{
        $pattern = "/([a-z])([A-Z])/";
        $snake = preg_replace_callback($pattern, function($matches){


            return $matches[1]."_".lcfirst($matches[2]);
        },$name);
        return $snake;
    }

    public function hydrate(array $data){
        $entity = new $this->entityClassName;
        foreach($data as $key => $value){
            $methodName = "set". $this->snakeToCamelCase($key);
            if(method_exists($entity, $methodName)){
                $entity->$methodName($value);
            }
        }

        return $entity;
    }

    public function deleteOneById(int $id){
        $sql = "DELETE FROM `{$this->tableName}` WHERE id= ?";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$id]);
        return $statement->rowCount()>0;

    }
    public function save(EntityInterface $entity):void{

        if($entity->getId()){
            // Update
            $this->update($entity);

        }else{
            $this->insert($entity);
            // Insert
        }
    }

    protected function insert(EntityInterface $entity):void{

        $columns = $this->getEntityProperties($entity);
  
        $data = $this->getDataFromEntity($columns,$entity);
        $columnList = implode(', ', $columns);
        $placeholderList = ":".implode(',  :', $columns);
        $sql = "INSERT INTO `{$this->tableName}`
        ($columnList) VALUES ($placeholderList)";
        
        $statement= $this->pdo->prepare($sql);
        $statement->execute($data);
        $entity-> setId($this->pdo->lastInsertId());
    }


    protected function getEntityProperties(EntityInterface $entity):array{
        $methodList = get_class_methods($entity);
        $methodList = array_filter($methodList,function($item){
            return $item !="getId" && str_starts_with($item,"get");


        });
        $propsList = array_map(function($item){
            
             
            $name =  lcfirst(substr($item, 3));
            
            if(in_array($name,$this->foreignKeys)){
                $name.= "_id";
            }
            return $this->camelToSnakeCase($name);

        }, $methodList);

        return $propsList;
    
    }

    public function getDataFromEntity($cols,$entity){

        $data = [];

        foreach($cols as $item){
            $propName = $this->snakeToCamelCase($item);
            $methodName = "get".ucfirst($propName);
            if(method_exists($entity,$methodName)){
                $val = $entity->$methodName();
                if($val instanceof DateTime){
                    $val = $val->format("Y-m-d");
                }
                $data[$item] = $val;

            }else{
                if(str_ends_with($item,"_id")){
                    $methodName = substr($methodName,0,strlen($methodName)-2);
                    $associatedEntity = $entity->$methodName();
                    if($associatedEntity){
                        $data[$item] = $associatedEntity->getId();
                    }

                }else{

                    $data[$item]=null;
                }
            }


        }
        return $data;
    }
    
    public function update (EntityInterface $entity):void{
        $columns = $this->getEntityProperties($entity);
        $data = $this->getDataFromEntity($columns,$entity);
        $data["id"]=$entity->getId();
        $placeholders = array_map(function($item){ 
            return "$item=:$item";
        },$columns);
        $placeholderList = implode("," ,$placeholders);
        $sql = "UPDATE `{$this->tableName}` SET $placeholderList WHERE id= :id";
        $statement= $this->pdo->prepare($sql);
        $statement->execute($data);

    }


    }


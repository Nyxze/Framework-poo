<?php
namespace m2i\framework;

class Request
{

    private array $query = [];

    public function __construct(string $queryString)
    {

        // name=aadsad&dasda split sur & ensuite sur =
        $keyValuePairs = explode('&', $queryString);
        foreach ($keyValuePairs as $item) {
            $parts = explode('=', $item);
            if (count($parts) == 2) {
                $this->query[$parts[0]] = $parts[1];

            }

        }
        var_dump($this->query);
    }


    /**
     * Get the value of query
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    public function get(string $key):?string{
    
        if(array_key_exists($key,$this->query)){
            return $this->query[$key] ;
        }else{

            return null;
        }


    }
}

<?php
namespace Blog\Model;
    
/* 
 * base model class
 */
use Doctrine\DBAL\Connection;

abstract class Model
{
    protected $conn;
    protected $tableName;
    /**
     * 
     * @param \Doctrine\DBAL\Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }
    
    /**
     * @param array $fields key=>value pairs to insert
     *
     * @return int inserted id
     */
    public function insert($fields,$load=false)
    {
        if (!is_array($fields) || empty($fields)) {
            return false;
        }

        $this->conn->insert($this->tableName, $fields);
        $insertedId = $this->conn->lastInsertId();

        if($insertedId !== false && $load) {
            $this->getById($insertedId);
        }

        return $insertedId;
    }
    /**
     * load record into model by db identifier
     */
    abstract function getById($id);
    
    /**
     * Magic method to trap getters and setters that do not exist for the specified property, explicitly defined methods will override this
     * methods supported: get..., set..., has...
     * Defined for model flexibility
     * 
     * @param type $method
     * @param type $args
     * @return \Model\Model
     * @throws Exception
     */
    public function __call($method, $args)
    {
        $property = lcfirst($method);
        $value = isset($args[0]) ? $args[0] : null;
        switch (substr($method, 0, 3)) {
            case 'get':
                if (property_exists($this, $property)) {
                    return $this->$property;
                }
                break;

            case 'set':
                if (property_exists($this, $property)) {
                    $this->$property = $value;
                    return $this;
                }
                break;

            case 'has':
                return property_exists($this, $property);
                break;
        }

        throw new Exception("Method '$method' does not exist nor does property '$property.'");
    }
    /**
     * Finds parameter and calls its getter if it exists, otherwise, returns the property
     * Defined for model flexibility
     * 
     * @param string $name property name
     * @return mixed value of the referenced property
     */
    function __get($name)
    {
        $property = lcfirst($name);
        $method = 'get'.  ucfirst($name);
        if(method_exists($this, $method))
                return $this->$method();
        else if(property_exists($property))
                return $this->$property;
        else
            throw new Exception("Method '$method' does not exist nor does property '$property.'");
    }
}

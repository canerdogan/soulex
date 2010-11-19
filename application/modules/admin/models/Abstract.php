<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Abstract object
 * It has flexible interfaces
 * to set/get mapper objects and class variables
 *
 * @author miholeus
 */
class Admin_Model_Abstract
{
    /**
     *
     * @var Admin_Model_DataMapper_Abstract
     */
    protected $_mapper = null;
    /**
     * Needs to be overrided
     */
    protected $_mapperClass;

    public function __construct(array $options = null)
    {
        if(is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __call($name, $args)
    {
        $accessor = substr($name, 0, 3);
        $property = substr($name, 3);
        switch($accessor) {
            case 'get':
                if('mapper' == strtolower($property) || !$property = $this->validateAttribute($property)) {
                    throw new BadMethodCallException('Getting property error: Invalid property ' . $name . '!');
                }
                return $this->$property;
            break;
            case 'set':
                if('mapper' == strtolower($property) || !$property = $this->validateAttribute($property)) {
                    throw new BadMethodCallException('Setting property error: Invalid property ' . $name . '!');
                }
                $this->$property = $args[0];
            break;
            default:
                throw new BadMethodCallException("Calling to unknown method or property "
                        . $name);
        }
    }

    protected function validateAttribute($name)
    {
        if (in_array("_" . strtolower($name),
            array_keys(get_class_vars(get_class($this))))) {
            return strtolower($name);
        }
        return false;
    }
    /**
     * Returns array of all protected properties that can be
     * accessed through get*() methods
     *
     * @return array
     */
    public function toArray()
    {
        $arr = array();
        $reflect = new Zend_Reflection_Class($this);
        $getters = $reflect->getMethods(Zend_Reflection_Method::IS_PUBLIC);

        $properties = $reflect->getProperties(
                Zend_Reflection_Property::IS_PROTECTED);
        $propertiesNames = array();
        foreach($properties as $property) {
            if(substr($property->getName(), 0, 1) == "_") {
                if(substr($property->getName(), 1, 6) != "mapper") {
                    $propertiesNames[] = substr($property->getName(), 1);
                }
            }
        }

        foreach($getters as $method) {
            if(substr($method->getName(), 0, 3) == "get") {
                if(PHP_VERSION_ID > 50302) {
                    $propName = lcfirst(substr($method->getName(), 3));
                } else {
                    $propName = substr($method->getName(), 3);
                    $propName{0} = strtolower($propName{0});
                }
                $methodName = $method->getName();

                // check if there is protected property that
                // corresponds to method name
                if(in_array($propName, $propertiesNames)) {
                    $arr[$propName] = $this->$methodName();
                }
            }
        }

        return $arr;
    }

    /**
     * Sets new mapper as Admin_Model_DataMapper_Abstract
     *
     * @param string $mapper
     * @return Admin_Model_DataMapper_Abstract
     */
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    /**
     *
     * @return Admin_Model_DataMapper_Abstract
     */
    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new $this->_mapperClass);
        }
        return $this->_mapper;
    }
    /**
     * Using setXXX() methods to set values
     * Option keys with underscore are replaced with string
     * in which first occurence of underscore is deleted and
     * next letter is upper cased
     *
     * Examples:
     * <title> = setTitle()
     * <menu_id> = setMenuId()
     *
     * @param array $options of object variables
     *
     * @return Admin_Model_Abstract
     */
    public function setOptions(array $options)
    {
        $reflect = new Zend_Reflection_Class($this);
        $props = $reflect->getProperties(
                Zend_Reflection_Property::IS_PRIVATE |
                Zend_Reflection_Property::IS_PUBLIC  |
                Zend_Reflection_Property::IS_PROTECTED);
        foreach($options as $key => $value) {
            if(false !== ($pos = strpos($key, '_'))) {
                $underscore_left = substr($key, 0, $pos);
                $underscore_right = ucfirst(substr($key, $pos + 1));
                $key = $underscore_left . $underscore_right;
            }
            $method = 'set' . ucfirst($key);
            if(in_array($key, $props)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    /**
     *
     * @param string $spec the column and direction to sort by
     * @return Admin_Model_User
     */
    public function order($spec)
    {
        $this->getMapper()->order($spec);
        return $this;
    }
    /**
     *
     * @return Zend_Paginator
     */
    public function paginate()
    {
        $adapter = $this->getMapper()->fetchPaginator();
        return new Zend_Paginator($adapter);
    }
}

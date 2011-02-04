<?php
/**
 * @package   NewClassic
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * FixtureLoader loads fixtures of different models
 *
 * @author miholeus
 */
class FixtureLoader
{
    protected static $_instance = null;
    /**
     * Store loaded data
     * 
     * @var array
     */
    protected $_storage = array();
    private function __construct(){}

    public static function getInstance()
    {
        if(null == self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * Load selected fixture
     *
     * @param string $fixture path
     * @return array loaded fixture
     */
    public function load($fixture)
    {
        if(isset($this->_storage[$fixture])) {
            return $this->_storage[$fixture];
        }
        $path = dirname(__FILE__) . '/_fixtures/' . $fixture . '.php';
        if(file_exists($path)) {
            $this->_storage[$fixture] = include($path);
            return $this->_storage[$fixture];
        }
        throw new Exception("Wrong fixture path " . $path);
    }
}
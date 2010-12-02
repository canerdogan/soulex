<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Tests set/get methods of Admin_Model_Menu class,
 * also toArray() method and setOptions() method
 */
class Admin_Model_MenuTest extends ControllerTestCase
{
    protected $data;

    protected function setUp()
    {
        parent::setUp();
        $this->data = array(
            'id' => 2,
            'title' => 'test_title',
            'menutype' => 'testing menutype',
            'description' => 'testing description'
        );
    }

    public function testAccessorsAndMutators()
    {
        $object = new Admin_Model_Menu();

        foreach(array_keys($this->data) as $prop) {
            $getPropName = "get" . ucfirst($prop);
            $setPropName = "set" . ucfirst($prop);
            $this->assertNull($object->$getPropName());
            // setting property value
            $object->$setPropName($this->data[$prop]);
            $this->assertEquals($this->data[$prop], $object->$getPropName());
        }
    }

    public function testBadGetSetMethodsThrowExceptions()
    {
        $object = new Admin_Model_Menu();
        try {
            $this->assertNull($object->getFoo());
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Getting property error(.*)/', $e->getMessage());
        }

        try {
            $this->assertNull($object->setFoo("bar"));
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Setting property error(.*)/', $e->getMessage());
        }
    }

    public function testIdOnlySetOnce()
    {
        $object = new Admin_Model_Menu();
        $object->setId(2);
        $this->assertEquals(2, $object->getId());
        $object->setId(10);
        $this->assertEquals(2, $object->getId());
    }

    /**
     * If chaining is not supported you'll get fatall error
     */
    public function testSettersCanGenerateChainCalss()
    {
        $object = new Admin_Model_Menu();

        $object->setTitle($this->data['title'])
            ->setMenutype($this->data['menutype'])
            ->setDescription($this->data['description']);
        /**
         * if no error was thrown, it's okay
         */
        $this->assertTrue(true);
    }

    public function testToArrayIsCorrect()
    {
        $object = new Admin_Model_Menu();
        $object->setId(2)
            ->setTitle($this->data['title'])
            ->setMenutype($this->data['menutype'])
            ->setDescription($this->data['description']);
        $this->assertEquals($this->data, $object->toArray());
    }

    public function testSetOptions()
    {
        $object = new Admin_Model_Menu($this->data);
        $this->assertEquals($this->data, $object->toArray());
    }

    public function testObjectImplementsIterator()
    {
        $object = new Admin_Model_Menu($this->data);
        // testing offsetExists method
        $this->assertTrue(isset($object['title']));
        // testing offsetGet method
        foreach($this->data as $key => $value) {
            $this->assertEquals($value, $object[$key]);
        }
        // testing offsetSet method
        $object['title'] = 'superman';
        $this->assertEquals('superman', $object->getTitle());
        // testing offsetUnset method
        unset($object['title']);
        $this->assertType('null', $object->getTitle());
    }
}
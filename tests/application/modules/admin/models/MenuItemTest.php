<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of MenuItemTest
 *
 * @author miholeus
 */
class Admin_Model_MenuItemTest extends ControllerTestCase
{
   protected $data;

    protected function setUp()
    {
        parent::setUp();
        $this->data = array(
            'id' => 2,
            'menu_id' => '1',
            'label' => 'testing label',
            'uri' => '/just-a-test.html',
            'position' => 100,
            'published' => true,
            'lft' => 1,
            'rgt' => 2,
            'parent_id' => 0,
            'level' => 1
        );
    }

    public function testAccessorsAndMutators()
    {
        $object = new Admin_Model_MenuItem();

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
        $object = new Admin_Model_MenuItem();
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
        $object = new Admin_Model_MenuItem();
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
        $object = new Admin_Model_MenuItem();

        $object->setMenu_id($this->data['menu_id'])
            ->setLabel($this->data['label'])
            ->setUri($this->data['uri'])
            ->setPosition($this->data['position'])
            ->setPublished($this->data['published'])
            ->setLft($this->data['lft'])
            ->setRgt($this->data['rgt'])
            ->setParent_id($this->data['parent_id'])
            ->setLevel($this->data['level']);
        /**
         * if no error was thrown, it's okay
         */
        $this->assertTrue(true);
    }

    public function testToArrayIsCorrect()
    {
        $object = new Admin_Model_MenuItem();
        $object->setId(2)
            ->setMenu_id($this->data['menu_id'])
            ->setLabel($this->data['label'])
            ->setUri($this->data['uri'])
            ->setPosition($this->data['position'])
            ->setPublished($this->data['published'])
            ->setLft($this->data['lft'])
            ->setRgt($this->data['rgt'])
            ->setParent_id($this->data['parent_id'])
            ->setLevel($this->data['level']);
        $this->assertEquals($this->data, $object->toArray());
    }

    public function testSetOptions()
    {
        $object = new Admin_Model_MenuItem($this->data);
        $this->assertEquals($this->data, $object->toArray());
    }

    public function testObjectImplementsIterator()
    {
        $object = new Admin_Model_MenuItem($this->data);
        // testing offsetExists method
        $this->assertTrue(isset($object['label']));
        // testing offsetGet method
        foreach($this->data as $key => $value) {
            $this->assertEquals($value, $object[$key]);
        }
        // testing offsetSet method
        $object['label'] = 'superman';
        $this->assertEquals('superman', $object->getLabel());
        // testing offsetUnset method
        unset($object['label']);
        $this->assertType('null', $object->getLabel());
    }
}

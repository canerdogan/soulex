<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Tests set/get methods of Admin_Model_ContentNode class,
 * also toArray() method and setOptions() method
 *
 * @author miholeus
 */
class Admin_Model_ContentNodeTest extends ControllerTestCase
{
    protected $data;

    protected function setUp() {
        parent::setUp();
        $this->data = array(
            'id' => 2,
            'name' => 'foo',
            'value' => 'bar',
            'isInvokable' => false,
            'params' => array(),
            'page_id' => '1'
        );
    }
    public function testAccessorsAndMutators()
    {
        $node = new Admin_Model_ContentNode();
        $this->data = array(
            'id' => 2,
            'name' => 'foo',
            'value' => 'bar',
            'isInvokable' => false,
            'page_id' => '1'
        );

        foreach(array_keys($this->data) as $prop) {
            $getPropName = "get" . ucfirst($prop);
            $setPropName = "set" . ucfirst($prop);
            $this->assertNull($node->$getPropName());
            // setting property value
            $node->$setPropName($this->data[$prop]);
            $this->assertEquals($this->data[$prop], $node->$getPropName());
        }

        $this->assertFalse($node->getParams());
    }

    public function testBadGetSetMethodsThrowExceptions()
    {
        $node = new Admin_Model_ContentNode();
        try {
            $this->assertNull($node->getFoo());
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Getting property error(.*)/', $e->getMessage());
        }

        try {
            $this->assertNull($node->setFoo("bar"));
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Setting property error(.*)/', $e->getMessage());
        }
    }

    public function testIdOnlySetOnce()
    {
        $node = new Admin_Model_ContentNode();
        $node->setId(2);
        $this->assertEquals(2, $node->getId());
        $node->setId(10);
        $this->assertEquals(2, $node->getId());
    }
    /**
     * If chaining is not supported you'll get fatall error
     */
    public function testSettersCanGenerateChainCalss()
    {
        $node = new Admin_Model_ContentNode();

        $node->setName('foo')
            ->setValue('bar')
            ->setPageId('1')
            ->setIsInvokable(false)
            ->setParams(array());
        /**
         * if no error was thrown, it's okay
         */
        $this->assertTrue(true);
    }

    public function testToArrayIsCorrect()
    {
        $node = new Admin_Model_ContentNode();
        $node->setId(2)
            ->setName('foo')
            ->setValue('bar')
            ->setIsInvokable(false)
            ->setParams(array())
            ->setPageId(1);

        $this->assertEquals($this->data, $node->toArray());
    }

    public function testSetOptions()
    {
        $node = new Admin_Model_ContentNode($this->data);
        $this->assertEquals($this->data, $node->toArray());
    }

    public function testObjectImplementsIterator()
    {
        $node = new Admin_Model_ContentNode($this->data);
        // testing offsetExists method
        $this->assertTrue(isset($node['id']));
        // testing offsetGet method
        foreach($this->data as $key => $value) {
            $this->assertEquals($value, $node[$key]);
        }
        // testing offsetSet method
        $node['name'] = 'brrrr';
        $this->assertEquals('brrrr', $node->getName());
        // testing offsetUnset method
        unset($node['name']);
        $this->assertType('null', $node->getName());
    }
}

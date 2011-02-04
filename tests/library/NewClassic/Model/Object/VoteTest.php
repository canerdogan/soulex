<?php
/**
 * @package   NewClassic
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of VoteTest
 *
 * @author miholeus
 */
class NewClassic_Models_Object_VoteTest extends ControllerTestCase
{
    protected $data;
    protected $model;

    protected function setUp()
    {
        parent::setUp();
        $this->data = array(
            'id' => 1,
            'users_id' => 1,
            'objects_id' => 1,
            'date' => date("Y-m-d H:i:s"),
            'userComment' => 'this is test comment'
        );
        $this->model = new NewClassic_Models_Object_Model();
    }
    /**
     * Test getters/setters of object
     */
    public function testAccessorsAndMutators()
    {
        foreach(array_keys($this->data) as $prop) {
            $getPropName = "get" . ucfirst($prop);
            $setPropName = "set" . ucfirst($prop);
            $this->assertNull($this->model->$getPropName());
            // setting property value
            $this->model->$setPropName($this->data[$prop]);
            $this->assertEquals($this->data[$prop], $this->model->$getPropName());
        }
    }
    /**
     * Test that not existing methods will throw exceptions
     */
    public function testBadGetSetMethodsThrowExceptions()
    {
        try {
            $this->assertNull($this->model->getFoo());
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Getting property error(.*)/', $e->getMessage());
        }

        try {
            $this->assertNull($this->model->setFoo("bar"));
            $this->fail("No exception was thrown");
        } catch (BadMethodCallException $e) {
            $this->assertRegExp('/Setting property error(.*)/', $e->getMessage());
        }
    }
    /**
     * Test that id can only be set once, usually happens when data mapper
     * creates new object from data received in database
     */
    public function testIdOnlySetOnce()
    {
        $this->model->setId(2);
        $this->assertEquals(2, $this->model->getId());
        $object->setId(10);
        $this->assertEquals(2, $this->model->getId());
    }

    /**
     * If chaining is not supported you'll get fatall error
     */
    public function testSettersCanGenerateChainCalss()
    {
        foreach($this->data as $key => $value) {
            $this->assertType($this->model, $this-$this->model->{"set" . ucfirst($key)}($value));
        }
        /**
         * if no error was thrown, it's okay
         */
        $this->assertTrue(true);
    }
    /**
     * Test toArray method
     */
    public function testToArrayIsCorrect()
    {
        foreach($this->data as $key => $value) {
            $this-$this->model->{"set" . ucfirst($key)}($value);
        }
        $this->assertEquals($this->data, $this->model->toArray());
    }
    /**
     * Test setOptions method
     */
    public function testSetOptions()
    {
        $this->assertEquals($this->data, $this->model->toArray());
    }
    /**
     * Test object implements Iterator Interface
     */
    public function testObjectImplementsIterator()
    {

        foreach($this->data as $key => $value) {
            $this->model[$key] = $value;
            $this->assertEquals($this->data[$key], $this->model[$key]);
            unset($this->model[$key]);
            $this->assertType('null', $this->model[$key]);
        }
    }
}
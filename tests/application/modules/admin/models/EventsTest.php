<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Tests set/get methods of Admin_Model_News class,
 * also toArray() method and setOptions() method
 */
class Admin_Model_EventsTest extends ControllerTestCase
{
    protected $data;

    protected function setUp()
    {
        parent::setUp();
        $this->data = array(
            'id' => 2,
            'title' => 'test_title',
            'short_description' => 'short_description',
            'detail_description' => 'detail_description',
            'img_preview' => '/not-existing-image.jpg',
            'published' => '1',
            'created_at' => '2010-10-19 17:00:00',
            'updated_at' => '2010-10-20 18:00:00',
            'published_at' => '2010-10-19 17:15:00'
        );
    }

    public function testAccessorsAndMutators()
    {
        $object = new Admin_Model_Events();

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
        $object = new Admin_Model_Events();
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
        $object = new Admin_Model_Events();
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
        $object = new Admin_Model_Events();

        $object->setTitle($this->data['title'])
            ->setShort_description($this->data['short_description'])
            ->setDetail_description($this->data['detail_description'])
            ->setImg_preview($this->data['img_preview'])
            ->setPublished($this->data['published'])
            ->setCreated_at($this->data['created_at'])
            ->setUpdated_at($this->data['updated_at'])
            ->setPublished_at($this->data['published_at']);

        /**
         * if no error was thrown, it's okay
         */
        $this->assertTrue(true);
    }

    public function testToArrayIsCorrect()
    {
        $object = new Admin_Model_Events();
        $object->setId(2)
            ->setTitle($this->data['title'])
            ->setShort_description($this->data['short_description'])
            ->setDetail_description($this->data['detail_description'])
            ->setImg_preview($this->data['img_preview'])
            ->setPublished($this->data['published'])
            ->setCreated_at($this->data['created_at'])
            ->setUpdated_at($this->data['updated_at'])
            ->setPublished_at($this->data['published_at']);
        $this->assertEquals($this->data, $object->toArray());
    }

    public function testSetOptions()
    {
        $object = new Admin_Model_Events($this->data);
        $this->assertEquals($this->data, $object->toArray());
    }

    public function testObjectImplementsIterator()
    {
        $object = new Admin_Model_Events($this->data);
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
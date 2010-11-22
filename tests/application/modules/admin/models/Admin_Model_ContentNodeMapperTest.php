<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of Admin_Model_ContentNodeMapperTest
 *
 * @author miholeus
 */
class Admin_Model_ContentNodeMapperTest extends ControllerTestCase
{
    /**
     *
     * @var Admin_Model_ContentNode
     */
    private $_object;
    /**
     *
     * @var Admin_Model_ContentNodeMapper
     */
    private $_mapper;
    protected $data;
    /**
     *
     * @var Admin_Model_ContentNodeMapper
     */
    protected $mapperClass = 'Admin_Model_ContentNodeMapper';
    protected $objectClass = 'Admin_Model_ContentNode';
    protected $table = 'content_nodes';

    protected function setUp()
    {
        parent::setUp();
        $this->data = array(
            'id' => 2,
            'name' => 'foo',
            'value' => 'bar',
            'isInvokable' => false,
            'params' => array(),
            'page_id' => '1'
        );
        $this->_object = new $this->objectClass($this->data);
        $mapper = new $this->mapperClass;
        $data = $this->data;
        $data['params'] = serialize($data['params']);
        $mapper->getDbTable()->insert($data);
        $this->_mapper = $mapper;
    }

    public function testLoadNodeInfo()
    {
        $node = new $this->objectClass;
        $node->setName($this->data['name'])->setPageId($this->data['page_id']);
        $this->_mapper->loadNodeInfo($node);

        $this->assertEquals($this->data['id'], $node->getId());
        $this->assertEquals($this->data['value'], $node->getValue());
        $this->assertEquals($this->data['isInvokable'], (bool)$node->getIsInvokable());
        $this->assertEquals($this->data['params'], unserialize($node->getParams()));
    }

    public function testLoadNodeInfoThrowsExceptionOnNotExistingPage()
    {
        $node = new $this->objectClass;
        $node->setName($this->data['name'])->setPageId(99);
        try {
            $this->_mapper->loadNodeInfo($node);
            $this->fail("Not of not existing page should not be loaded");
        } catch (InvalidArgumentException $e) {
            // SUCCESS
            $this->assertTrue(true);
        }
    }

    public function testCopyNodesToPages()
    {
        $mdlContentNode = new $this->_object(array(
            'pageId'    => 1,
            'name'      => 'foo'
        ));

        $allPages = array();
        array_push($allPages, array('id' => 2));
        array_push($allPages, array('id' => 3));
        array_push($allPages, array('id' => 4));
        array_push($allPages, array('id' => 5));

        $isSucceeded = $this->_mapper->copyToPages($allPages, $mdlContentNode);
        $this->assertTrue($isSucceeded);
        foreach($allPages as $page) {
//            $where = 'page_id = ?';
//            $select = $this->_mapper->getDbTable()->getDefaultAdapter()->select()
//                    ->where($where, $page);
//            $result = $this->_mapper->getDbTable()->getDefaultAdapter()->fetchAll($select);
            
        }
    }

    protected function tearDown()
    {
        parent::tearDown();
        $mapper = new $this->mapperClass;
        $mapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE ' . $this->table);
    }
}

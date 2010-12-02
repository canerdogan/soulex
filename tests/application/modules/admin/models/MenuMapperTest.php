<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of MenuMapperTest
 *
 * @author miholeus
 */
class Admin_Model_MenuMapperTest extends ControllerTestCase
{
    /**
     *
     * @var Admin_Model_Menu
     */
    private $_object;
    /**
     *
     * @var Admin_Model_MenuMapper
     */
    private $_objectMapper;

    protected function setUp()
    {
        parent::setUp();
        $this->_object = new Admin_Model_Menu();
        $this->_object->setTitle('test_title')
            ->setMenutype('menuleft')
            ->setDescription('test description');
        $objectMapper = new Admin_Model_MenuMapper();
        $objectMapper->save($this->_object);
        $this->_objectMapper = $objectMapper;
    }

    

    protected function tearDown()
    {
        parent::tearDown();
        $objectMapper = new Admin_Model_MenuMapper();
        $objectMapper->getDbTable()->getDefaultAdapter()->query('TRUNCATE TABLE menus');
    }
}

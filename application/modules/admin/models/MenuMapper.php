<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_MenuMapper maps Menu objects with database layer
 *
 * @author miholeus
 */
class Admin_Model_MenuMapper extends Admin_Model_DataMapper_Standard
{
    /**
     *
     * @var Admin_Model_DbTable_Menu
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_Menu';
    /**
     *
     * @var Admin_Model_Menu
     */
    protected $_object = 'Admin_Model_Menu';
    /**
     *
     * @var Admin_Model_MenuCollection
     */
    protected $_collection = 'Admin_Model_MenuCollection';

    protected function prepareDataForSave(Admin_Model_Abstract $object)
    {
        return array(
            'title'                 => $object['title'],
            'menutype'              => $object['menutype'],
            'description'           => $object['description']
        );
    }
}

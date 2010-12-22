<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of InsUserMapper
 *
 * @author miholeus
 */
class Admin_Model_InsUserMapper extends Admin_Model_DataMapper_Standard
{
    /**
     *
     * @var Admin_Model_DbTable_Events
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_InsUser';
    /**
     *
     * @var Admin_Model_Events
     */
    protected $_object = 'Admin_Model_InsUser';
    /**
     *
     * @var Admin_Model_EventsCollection
     */
    protected $_collection = 'Admin_Model_InsUserCollection';

    protected function prepareDataForSave(Admin_Model_Abstract $object)
    {

    }

}

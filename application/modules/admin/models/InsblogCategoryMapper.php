<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of InsblogCategoryMapper
 *
 * @author miholeus
 */
class Admin_Model_InsblogCategoryMapper extends Admin_Model_DataMapper_Standard
{
    /**
     *
     * @var Admin_Model_DbTable_InsadviceCategory
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_InsblogCategory';
    /**
     *
     * @var Admin_Model_InsadviceCategory
     */
    protected $_object = 'Admin_Model_InsblogCategory';
    /**
     *
     * @var Admin_Model_InsadviceCategoryCollection
     */
    protected $_collection = 'Admin_Model_InsblogCategoryCollection';

    protected function prepareDataForSave(Admin_Model_Abstract $object)
    {
        return array(
            'name'              => $object['name']
        );
    }
    /**
     * Simple search by title field using like operator
     *
     * @param string $value search value
     * @return void
     */
    public function search($value)
    {
        if(!empty($value)) {
            $value = str_replace('\\', '\\\\', $value);
            $value = addcslashes($value, '_%');
            $this->_select = $this->getSelect();

            $this->_select->where('name LIKE ?', '%' . $value . '%');
        }
        return $this;
    }
    public function deleteBulk(array $ids)
    {
        if(count($ids) > 0) {
            foreach($ids as $id) {
                $this->delete($id);
            }
        }
    }
}
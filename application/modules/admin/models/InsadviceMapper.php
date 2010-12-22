<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of InsadviceMapper
 *
 * @author miholeus
 */
class Admin_Model_InsadviceMapper extends Admin_Model_DataMapper_Standard
{
    /**
     *
     * @var Admin_Model_DbTable_Insadvice
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_Insadvice';
    /**
     *
     * @var Admin_Model_Insadvice
     */
    protected $_object = 'Admin_Model_Insadvice';
    /**
     *
     * @var Admin_Model_InsadviceCollection
     */
    protected $_collection = 'Admin_Model_InsadviceCollection';
    /**
     *
     * @param Admin_Model_Abstract $object
     * @return array data to be saved
     */
    protected function prepareDataForSave(Admin_Model_Abstract $object)
    {
        return array(
            'name'              => $object['name'],
            'date'              => $object['date'],
            'teaser'            => $object['teaser'],
            'text'              => $object['text'],
            'file'              => $object['file'],
            'cat_id'            => $object['cat_id'],
            'user_id'           => $object['user_id'],
            'draft'             => $object['draft']
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
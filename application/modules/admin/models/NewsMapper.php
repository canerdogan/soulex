<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_NewsMapper
 *
 * @author miholeus
 */
class Admin_Model_NewsMapper extends Admin_Model_DataMapper_Standard
{
    /**
     *
     * @var Admin_Model_DbTable_News
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_News';
    /**
     *
     * @var Admin_Model_Menu
     */
    protected $_object = 'Admin_Model_News';
    /**
     *
     * @var Admin_Model_MenuCollection
     */
    protected $_collection = 'Admin_Model_NewsCollection';

    protected function prepareDataForSave(Admin_Model_Abstract $object)
    {
        return $object->toArray();
    }


    public function save(Admin_Model_News $news)
    {
        $data = $this->prepareDataForSave($news);

        if (null === ($id = $news->getId())) {
            $data['created_at'] = date("Y-m-d H:i:s");
            $this->getDbTable()->insert($data);
            $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
            $news->setId($insertedId);
        } else {
            $data['updated_at'] = date("Y-m-d H:i:s");
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    /**
     * Mass news deletion
     *
     * @param array $ids
     */
    public function deleteBulk($ids)
    {
        if(is_array($ids) && count($ids) > 0) {
            foreach($ids as $id) {
                $this->delete($id);
            }
        }
    }
    /**
     * Sets published in where clause
     *
     * @param int $published
     * @return Admin_Model_NewsMapper
     */
    public function published($published)
    {
        /**
         * isset added to prevent the clause when user is updated
         * and $enabled value comes as null
         */
        if($published != '*' && isset($published)) {
            $published = (int)$published;
            $this->_select = $this->getSelect();
            $this->_select->where('published = ?', $published);
        }
        return $this;
    }
    /**
     * Simple search by title field using like operator
     *
     * @param string $value search value
     * @return Admin_Model_NewsMapper
     */
    public function search($value)
    {
        if(!empty($value)) {
            $value = str_replace('\\', '\\\\', $value);
            $value = addcslashes($value, '_%');
            $this->_select = $this->getSelect();
            $this->_select->where('title LIKE ?', '%' . $value . '%');
        }
        return $this;
    }
}

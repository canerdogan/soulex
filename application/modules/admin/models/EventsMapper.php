<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_EventsMapper
 *
 * @author miholeus
 */
class Admin_Model_EventsMapper extends Admin_Model_DataMapper_Standard
{
    /**
     *
     * @var Admin_Model_DbTable_Events
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_Events';
    /**
     *
     * @var Admin_Model_Events
     */
    protected $_object = 'Admin_Model_Events';
    /**
     *
     * @var Admin_Model_EventsCollection
     */
    protected $_collection = 'Admin_Model_EventsCollection';

    protected function prepareDataForSave(Admin_Model_Abstract $object)
    {
        return array(
            'title'                 => $object['title'],
            'short_description'     => $$object['short_description'],
            'detail_description'    => $object['detail_description'],
            'img_preview'           => $object['img_preview'],
            'published'             => $object['published'],
            'updated_at'            => $object['updated_at'],
            'published_at'          => $object['published_at']
        );
    }

    public function save(Admin_Model_Events $Events)
    {
        $data = $this->prepareDataForSave($Events);

        if (null === ($id = $Events->getId())) {
            $data['created_at'] = date("Y-m-d H:i:s");
            $this->getDbTable()->insert($data);
            $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
            $Events->setId($insertedId);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function deleteBulk(array $ids)
    {
        if(count($ids) > 0) {
            foreach($ids as $id) {
                $this->delete($id);
            }
        }
    }
    /**
     * Sets published in where clause
     *
     * @param int $published
     * @return void
     */
    public function published($published)
    {
        /**
         * isset added to prevent the clause when user is updated
         * and $enabled value comes as null
         */
        if($published != '*' && isset($published)) {
            $this->_select = $this->getSelect();
            $this->_select->where('published = ?', $published);
        }
        return $this;
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
            $this->_select->where('title LIKE ?', '%' . $value . '%');
        }
        return $this;
    }
}

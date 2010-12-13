<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_Events
 *
 * @method Admin_Model_Events setId(int $id)
 * @method Admin_Model_Events setTitle(string $title)
 * @method Admin_Model_Events setShort_description(string $description)
 * @method Admin_Model_Events setDetail_description(string $description)
 * @method Admin_Model_Events setPublished(bool $published)
 * @method Admin_Model_Events setCreated_at(string $date)
 * @method Admin_Model_Events setUpdated_at(string $date)
 * @method Admin_Model_Events setPublished_at(string $date)
 *
 * @method int getId()
 * @method string getTitle()
 * @method string getShort_description()
 * @method string getDetail_description()
 * @method bool getPublished()
 * @method string getCreated_at()
 * @method string getUpdated_at()
 * @method string getPublished_at()
 *
 * @author miholeus
 */
class Admin_Model_Events extends Admin_Model_Abstract
{
    protected $id;
    protected $title;
    protected $short_description;
    protected $detail_description;
    protected $img_preview;
    protected $published;
    protected $created_at;
    protected $updated_at;
    protected $published_at;

    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }
}

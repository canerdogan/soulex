<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_Menu Types
 * @method Admin_Model_Menu setId(int $id);
 * @method Admin_Model_Menu setTitle(string $title);
 * @method Admin_Model_Menu setMenutype(string $type);
 * @method Admin_Model_Menu setDescription(string $description);
 *
 * @method int getId()
 * @method string getTitle()
 * @method string getMenutype()
 * @method string getDescription()
 *
 * @author miholeus
 */
class Admin_Model_Menu extends Admin_Model_Abstract
{
    protected $id;
    protected $title;
    protected $menutype;
    protected $description;

    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }
}

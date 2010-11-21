<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * UserCollection class
 *
 * @author miholeus
 */
class Admin_Model_UserCollection extends Admin_Model_DataMapper_Collection
{
    public function targetClass()
    {
        return 'Admin_Model_User';
    }
}
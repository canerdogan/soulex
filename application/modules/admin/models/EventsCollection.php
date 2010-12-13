<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of EventsCollection
 *
 * @author miholeus
 */
class Admin_Model_EventsCollection extends Admin_Model_DataMapper_Collection
{
    public function targetClass()
    {
        return 'Admin_Model_Events';
    }
}

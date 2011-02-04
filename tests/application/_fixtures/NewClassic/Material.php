<?php
/**
 * @package   NewClassic
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */
return array(
    'name' => 'test name',
    'text' => 'some stupid text',
    'userCreated' => 1,
    'userActivated' => 1,
    'publishDate' => date("Y-m-d H:i:s"),
    'access' => serialize(array("users" => array(1)))
);
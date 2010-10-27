<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of ZFBugsTest
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../ControllerTestCase.php';

class ZFBugsTest extends ControllerTestCase
{
    /*
     * bug 8193
     */
    public function testGetInvokeArgReturnsCorrectly()
    {
        $this->dispatch('/');
        $this->assertType('Zend_Application_Bootstrap_Bootstrap',
                $this->_frontController->getParam('bootstrap')); // FAILURE
    }
}

<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */


/**
 * EventsController manages requests in Events section
 *
 * @author miholeus
 */
class Frontend_EventsController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $this->view->pageurl = 'events';
    }
    /**
     * Show events on main page
     */
    public function mainpageAction()
    {
        $eventsMapper = new NewClassic_Model_Events_Mapper();
        $this->view->events = $eventsMapper->fetchAll(null, 'date DESC', 3);

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('events/mainpage.phtml');
    }
    /**
     * Show event's types as left menu on events page
     */
    public function menuleftAction()
    {
        $mapper = new NewClassic_Model_Events_TypeMapper();
        $this->view->items = $mapper->fetchAll();

        $this->view->selectedUri = $this->_request->getPathInfo();

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('events/menuleft.phtml');
    }
    /**
     * Show list of events
     */
    public function listAction()
    {
        $eventsMapper = new NewClassic_Model_Events_Mapper();
        $this->view->events = $eventsMapper->fetchAll(null, 'date DESC', 5);

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('events/list.phtml');
    }
    /**
     * Show events by type
     */
    public function eventsbytypeAction()
    {
        $this->_loadPage();

        $id = $this->_getParam('typeId');

        $typesMapper = new NewClassic_Model_Events_TypeMapper();
        try {
            $this->view->eventType = $typesMapper->findById($id);
        } catch (Exception $e) {
            $this->view->eventType = null;
        }

        $eventsMapper = new NewClassic_Model_Events_Mapper();
        $this->view->events = $eventsMapper->selectItems($id);

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('events/list.phtml');
    }
    /**
     * Load events page except some nodes on it
     */
    protected function _loadPage()
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('index');
        Zend_Registry::set('layout_changed', true);

        $pageManager = new Soulex_Helper_PageManager(135, $this->_helper);
        $pageManager->excludeNodes('block5');
        $pageManager->loadNodes();
    }
}

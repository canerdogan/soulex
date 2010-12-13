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
    public function singleAction()
    {
        $id = $this->_getParam(1);
        $mdlEvents = new Admin_Model_EventsMapper();
        $this->view->events = $mdlEvents->findById($id);

        $this->view->title = $this->view->events->getTitle();
        $this->view->headTitle($this->view->events->getTitle(), 'SET');

        $this->_helper->actionStack('menuleft', 'menu', 'frontend', array(
            '_responseSegment' => 'menuleft'
        ));
        $this->_helper->actionStack('menutop', 'menu', 'frontend', array(
            '_responseSegment' => 'menutop'
        ));

    }
    public function listAction()
    {
        $limit = 20;
        $page = $this->_request->getParam('page', 1);

        $eventsService = new Admin_Model_EventsMapper();
        $paginator = $eventsService->published('1')
                    ->order('published_at DESC')->paginate();

        $paginator->setItemCountPerPage($limit);

        $paginator->setCurrentPageNumber($page);
        // pass the paginator to the view to render
        $this->view->paginator = $paginator;

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('events/list.phtml');
    }

    public function sidebarAction()
    {
        $mdlEvents = new Admin_Model_EventsMapper();
        $this->view->events = $mdlEvents->published('1')
                ->limit(3)->order('published_at DESC')->paginate();

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);
    }

}

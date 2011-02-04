<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */


/**
 * NewsController manages requests in News section
 *
 * @author miholeus
 */
class Frontend_NewsController extends Zend_Controller_Action
{
    public function mainpageAction()
    {
        $newsMapper = new NewClassic_Model_News_Mapper();
        $this->view->news = $newsMapper->fetchAll(null, 'date DESC', 2);

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);
        
        echo $this->view->render('news/mainpage.phtml');
    }
    public function singleAction()
    {
        $id = $this->_getParam(1);
        $mdlNews = new Admin_Model_NewsMapper();
        $this->view->news = $mdlNews->findById($id);

        $this->view->title = $this->view->news->getTitle();
        $this->view->headTitle($this->view->news->getTitle(), 'SET');

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

        $newsMapper = new Admin_Model_NewsMapper();
        $paginator = $newsMapper->published(1)->order('published_at DESC')
                ->paginate();

        $paginator->setItemCountPerPage($limit);

        $paginator->setCurrentPageNumber($page);
        // pass the paginator to the view to render
        $this->view->paginator = $paginator;

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('news/list.phtml');
    }

    public function sidebarAction()
    {
        $newsMapper = new Admin_Model_NewsMapper();
        $this->view->news = $newsMapper->fetchAll('published = 1', 3, 'published_at DESC');

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);
    }
}

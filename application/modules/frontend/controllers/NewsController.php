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
    public function preDispatch()
    {
        $this->view->pageurl = 'novosti-arhitektury-stroitelstva-remonta-dizayna';
    }
    /**
     * Show news on index page
     */
    public function mainpageAction()
    {
        $newsMapper = new NewClassic_Model_News_Mapper();
        $this->view->news = $newsMapper->fetchAll(null, 'date DESC', 2);

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('news/mainpage.phtml');
    }
    /**
     * Show news on news page in left menu
     */
    public function menuleftAction()
    {


        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('news/menuleft.phtml');
    }
    /**
     * Show all news on news page
     */
    public function listAction()
    {
        $limit = 30;// totall elements on page
        $page = $this->_getParam('page', 1);

        $newsMapper = new NewClassic_Model_News_Mapper();

        try {
            $paginator = $newsMapper
                ->selectNews()
                ->order('date DESC')->paginate();

            $paginator->setItemCountPerPage($limit);
            $paginator->setCurrentPageNumber($page);
            Zend_Registry::set('pagination_limit', $limit);
            $this->view->paginator = $paginator;
        } catch (Exception $e) {
            // @todo log error here
            $this->view->paginator = null;
            $this->getResponse()->setHttpResponseCode(404);
        }

        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);

        echo $this->view->render('news/list.phtml');
    }
    /**
     * Show news detail page
     */
    public function detailAction()
    {
        $this->loadPage();

        $id = $this->_getParam('newsId');

        $newsMapper = new NewClassic_Model_News_Mapper();
        $this->view->news = $newsMapper->getInfoById($id);
        if(null === $this->view->news) {
            // @todo log error here
            $this->getResponse()->setHttpResponseCode(404);
        }

        $this->_helper->viewRenderer->setResponseSegment('block5');
    }
    /**
     * Load news page except some nodes on it
     *
     * @todo realize page loader as action helper
     */
    protected function loadPage()
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('index');
        Zend_Registry::set('layout_changed', true);

        $pageManager = new Soulex_Helper_PageManager(119, $this->_helper);
        $pageManager->excludeNodes('block5');
        $pageManager->loadNodes();
    }
}

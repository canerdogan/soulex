<?php

/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_NewsController processes requests to news
 *
 * @author miholeus
 */
class Admin_NewsController extends Soulex_Controller_Abstract {

    public function indexAction()
    {
        $newsMapper = new Admin_Model_NewsMapper();

        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array(); // view property for where statements
        $limit = $this->_getParam('limit', 20);

        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $newsMapper->published($post['filter_published'])
                            ->search($post['filter_search'])
                            ->order($order)->paginate();
            $this->view->filter['enabled'] = $post['filter_published'];

            try {
                if (is_array($post['cid'])) {
                    if(count($post['cid']) != $post['boxchecked']) {
                        throw new LengthException("Checksum is not correct");
                    }
                    try {
                        $newsMapper->deleteBulk($post['cid']);
                        return $this->_redirect('/admin/news');
                    } catch (Exception $e) {
                        throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
                    }
                }
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("News deletion failed with the following error: "
                        . $e->getMessage());
            }
        } else {
            $paginator = $newsMapper->order($order)->paginate();
        }

        // show items per page
        if ($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }

        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);

        $this->view->render('news/index.phtml');
    }

    public function editAction()
    {
        $frmNews = new Admin_Form_News();

        if ($this->getRequest()->isPost() &&
                $frmNews->isValid($this->getRequest()->getPost())) {
            $data = array(
                'id' => $frmNews->getValue('id'),
                'title' => $frmNews->getValue('title'),
                'short_description' => $frmNews->getValue('short_description'),
                'detail_description' => $frmNews->getValue('detail_description'),
                'published' => $frmNews->getValue('published'),
                'updated_at' => date("Y-m-d H:i:s"),
                'published_at' => $frmNews->getValue('published_at')
            );

            try {
                $news = new Admin_Model_News($data);
                $newsMapper = new Admin_Model_NewsMapper();
                $newsMapper->save($news);

                $this->disableContentRender();
                return $this->_forward('index');
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("News update failed with the following error: "
                        . $e->getMessage());
            }
        } else {
            $newsMapper = new Admin_Model_NewsMapper();
            $currentNews = $newsMapper->findById($this->getRequest()->getParam('id'));
            $frmNews->populate($currentNews->toArray());
        }

        $this->view->form = $frmNews;

        $this->renderSubmenu(false);
        $this->view->render('news/edit.phtml');
    }

    public function createAction()
    {
        $frmNews = new Admin_Form_News();

        if ($this->getRequest()->isPost() &&
                $frmNews->isValid($this->getRequest()->getPost())) {
            $data = array(
                'title' => $frmNews->getValue('title'),
                'short_description' => $frmNews->getValue('short_description'),
                'detail_description' => $frmNews->getValue('detail_description'),
                'published' => $frmNews->getValue('published'),
                'created_at' => date("Y-m-d H:i:s"),
                'published_at' => $frmNews->getValue('published_at')
            );

            try {
                $news = new Admin_Model_News($data);
                $newsMapper = new Admin_Model_NewsMapper();
                $newsMapper->save($news);

                $this->disableContentRender();
                return $this->_forward('index');
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("News creation failed with the following error: "
                        . $e->getMessage());
            }
        }

        $this->view->form = $frmNews;

        $this->renderSubmenu(false);
        $this->view->render('news/create.phtml');
    }

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'title');
        $direction = $this->_getParam('direction', 'desc');
        /**
         * sets default order if model does not have proper field
         */
        if (!is_callable(array(new Admin_Model_News(),
                    'get' . ucfirst($order)))) {
            $order = 'title';
        }

        if (!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }

}

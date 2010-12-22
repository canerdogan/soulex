<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of InsadvicecatController
 *
 * @author miholeus
 */
class Admin_InscompanycatController extends Soulex_Controller_Abstract
{
    public function preDispatch()
    {
        $this->mapper = new Admin_Model_InscompanyCategoryMapper();
        $this->model = new Admin_Model_InscompanyCategory();
        $this->form = new Admin_Form_InsCategory();
    }

    public function indexAction()
    {
        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getParam('limit', 20);

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $this->mapper->search($post['filter_search'])
                                ->order($order)->paginate();

            try {
                if (is_array($post['cid'])) {
                    if(count($post['cid']) != $post['boxchecked']) {
                        throw new LengthException("Checksum is not correct");
                    }
                    try {
                        $this->mapper->deleteBulk($post['cid']);
                        return $this->_redirect('/admin/inscompanycat');
                    } catch (Exception $e) {
                        throw new RuntimeException($e->getMessage(), $e->getCode());
                    }
                }
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Company category deletion failed with the following error: "
                        . $e->getMessage());
            }
        } else {
            $paginator = $this->mapper->order($order)->paginate();
        }

        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }
        // get the page number that is passed in the request.
        //if none is set then default to page 1.
        $page = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($page);
        // pass the paginator to the view to render
        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);

        $this->view->render('inscompanycat/index.phtml');
    }

    public function createAction()
    {
        if($this->_request->isPost()
                && $this->form->isValid($this->_request->getPost())) {

            $this->form->removeElement('id');
            $this->model->setOptions($this->form->getValues());
            try {
                $this->mapper->save($this->model);

                return $this->_redirect('/admin/inscompanycat');
            } catch (Exception $e) {
                $this->renderSubinsadvice(false);
                $this->renderError("Company category creation failed with the following error: "
                        . $e->getMessage());
            }
        }

        $this->view->form = $this->form;
        $this->renderSubmenu(false);
        $this->view->render('inscompanycat/create.phtml');
    }
    public function editAction()
    {
        $id = $this->_getParam('id');

        if($this->_request->isPost()
                && $this->form->isValid($this->_request->getPost())) {
            try {

                $this->model->setOptions($this->form->getValues());
                $this->mapper->save($this->model);
                $this->disableContentRender();
                return $this->_forward('index');
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Inscompany update failed with the following error: "
                        . $e->getMessage());
            }
        }

        $mdl = $this->mapper->findById($id);

        $this->form->populate($mdl->toArray());
        $this->view->form = $this->form;

        $this->renderSubmenu(false);

        $this->view->render('inscompanycat/edit.phtml');
    }
    /**
     * Delete objects by its id
     */
    public function deleteAction()
    {
		$id = $this->getRequest()->getParam('id');

		$this->mapper->delete($id);
		$this->_redirect('/admin/inscompanycat');
    }

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'name');
        $direction = $this->_getParam('direction', 'desc');
        /**
         * sets default order if model does not have proper field
         */
        if(!is_callable(array(new Admin_Model_InscompanyCategory(),
            'get' . ucfirst($order)))) {
            $order = 'name';
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}
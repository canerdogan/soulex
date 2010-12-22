<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of AdviceController
 *
 * @author miholeus
 */
class Admin_InsadviceController extends Soulex_Controller_Abstract
{
    /**
     * Show all insadvices
     */
    public function indexAction()
    {
        $mapper = new Admin_Model_InsadviceMapper();

        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getParam('limit', 20);

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $mapper->search($post['filter_search'])
                                ->order($order)->paginate();

            try {
                if (is_array($post['cid'])) {
                    if(count($post['cid']) != $post['boxchecked']) {
                        throw new LengthException("Checksum is not correct");
                    }
                    try {
                        $mapper->deleteBulk($post['cid']);
                        return $this->_redirect('/admin/insadvice');
                    } catch (Exception $e) {
                        throw new RuntimeException($e->getMessage(), $e->getCode());
                    }
                }
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Advice deletion failed with the following error: "
                        . $e->getMessage());
            }
        } else {
            $paginator = $mapper->order($order)->paginate();
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

        $this->view->render('insadvice/index.phtml');
    }
    /**
     * Show form for insadvice creation and process postback requests
     * to save new insadvice
     *
     * @return void
     */
    public function createAction()
    {
        $frmInsadvice = new Admin_Form_Insadvice();
        $catMapper = new Admin_Model_InsadviceCategoryMapper();
        $userMapper = new Admin_Model_InsUserMapper();
        $frmInsadvice->addOptions('cat_id', $catMapper->fetchAll(), array(
            'key' => 'id',
            'value' => 'name'
        ));
        $frmInsadvice->addOptions('user_id', $userMapper->fetchAll(), array(
            'key' => 'id',
            'value' => array('firstname', 'lastname')
        ));
        if($this->_request->isPost()
                && $frmInsadvice->isValid($this->_request->getPost())) {

            $mdlInsadvice = new Admin_Model_Insadvice();
            $insadviceMapper = new Admin_Model_InsadviceMapper();
            $frmInsadvice->removeElement('id');
            $mdlInsadvice->setOptions($frmInsadvice->getValues());
            try {
                $insadviceMapper->save($mdlInsadvice);

                return $this->_redirect('/admin/insadvice');
            } catch (Exception $e) {
                $this->renderSubinsadvice(false);
                $this->renderError("Insadvice creation failed with the following error: "
                        . $e->getMessage());
            }
        }

        $this->view->form = $frmInsadvice;
        $this->renderSubmenu(false);
        $this->view->render('insadvice/create.phtml');
    }
    /**
     * Show form for editing insadvice and process postback request to
     * save info about insadvice
     *
     * @return void
     */
    public function editAction()
    {
        $id = $this->_getParam('id');

        $frmInsadvice = new Admin_Form_Insadvice();
        $catMapper = new Admin_Model_InsadviceCategoryMapper();
        $userMapper = new Admin_Model_InsUserMapper();
        $frmInsadvice->addOptions('cat_id', $catMapper->fetchAll(), array(
            'key' => 'id',
            'value' => 'name'
        ));
        $frmInsadvice->addOptions('user_id', $userMapper->fetchAll(), array(
            'key' => 'id',
            'value' => array('firstname', 'lastname')
        ));
        if($this->_request->isPost()
                && $frmInsadvice->isValid($this->_request->getPost())) {
            try {
                $mdlInsadvice = new Admin_Model_Insadvice($frmInsadvice->getValues());
                $insadviceMapper = new Admin_Model_InsadviceMapper();
                $insadviceMapper->save($mdlInsadvice);
                $this->disableContentRender();
                return $this->_forward('index');
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Insadvice update failed with the following error: "
                        . $e->getMessage());
            }
        }

        $insadviceMapper = new Admin_Model_InsadviceMapper();
        $this->view->insadvice = $insadviceMapper->findById($id);

        $frmInsadvice->populate($this->view->insadvice->toArray());
        $this->view->form = $frmInsadvice;

        $this->renderSubmenu(false);

        $this->view->render('insadvice/edit.phtml');
    }
    /**
     * Delete insadvice by its id
     */
    public function deleteAction()
    {
        $insadviceMapper = new Admin_Model_InsadviceMapper();
		$id = $this->getRequest()->getParam('id');

		$insadviceMapper->delete($id);
		$this->_redirect('/admin/insadvice');
    }

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'name');
        $direction = $this->_getParam('direction', 'desc');
        /**
         * sets default order if model does not have proper field
         */
        if(!is_callable(array(new Admin_Model_Insadvice(),
            'get' . ucfirst($order)))) {
            $order = 'name';
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}
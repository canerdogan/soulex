<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_PageController processes requests to pages
 *
 * @author miholeus
 */
class Admin_PageController extends Soulex_Controller_Abstract
{
    public function indexAction()
    {
        $mdlPage = new Model_Page();

        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getLimitParam();

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $mdlPage->selectPublished($post['filter_published'])
                                ->search($post['filter_search'])
                                ->order($order)->paginate();

            $this->view->filter['published'] = $post['filter_published'];

            if(isset($post['cid'])) {
                if(is_array($post['cid'])
                        && count($post['cid']) == $post['boxchecked']) {
                    $mdlPage->deleteBulk($post['cid']);
                    return $this->_redirect('/admin/page');
                } else {
                    throw new Exception('FCS  is not correct! Wrong request!');
                }
            }
        } else {
            $paginator = $mdlPage->order($order)->paginate();
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

        $this->view->render('page/index.phtml');
    }

    public function editAction()
    {
        $mdlPage = new Model_Page();

		$id = $this->getRequest()->getParam('id');

		$frmPage = new Admin_Form_Pages();

		if($this->_request->isPost()) {

			if($frmPage->isValid($this->_request->getPost())) {

                $mdlPage->update($id, $this->_request->getPost());

				return $this->_redirect('/admin/page');
			}
			$pageData = $frmPage->getValues();
		} else {
			$pageData = $mdlPage->find($id);
		}

        $nodeData = $pageData['_data'];
        unset($pageData['_data']);

		$frmPage->populate($pageData);

        $this->view->nodes = array();

        if(count($nodeData) > 0) {
            foreach($nodeData as $nodeName => $nodeValue) {
                if($frmPage->getElement($nodeName) !== null) {
                    // setting node switcher value
                    $frmPage->getElement('nodes' . $nodeName . 'type')
                            ->setValue($nodeValue['isInvokable']);
                    if($nodeValue['isInvokable'] == 1) {
                        $_nodeData = unserialize($nodeValue['value']);
                        $frmPage->setDynamicNodeData($nodeName, $_nodeData);
                    } else {
                        $frmPage->getElement($nodeName)->setValue($nodeValue['value']);
                    }
                } else { // add new elements to form
    //                $frmPage->addTextAreaControl($nodeName, $nodeValue['value']);
                    $this->view->nodes[] = array(
                        'id'        => $nodeValue['id'],
                        'name'      => $nodeName,
                        'value'     => $nodeValue['value'],
                        'pageId'    => $id
                    );
                }
            }
        }

//        // create new node button
//        $frmPage->addNewNodeButton();

		$this->view->form = $frmPage;

        $this->renderSubmenu(false);

        $this->view->render(('page/edit.phtml'));

    }

    public function createAction()
    {
        $mdlPage = new Model_Page();

		$frmPage = new Admin_Form_Pages();

		if($this->_request->isPost() && $frmPage->isValid($this->_request->getPost())) {
            $mdlPage = new Model_Page();

            $mdlPage->create($this->_request->getPost());

            return $this->_redirect('/admin/page');
		}

		$this->view->form = $frmPage;

        $this->renderSubmenu(false);

        $this->view->render('page/create.phtml');

    }

	public function deleteAction()
	{
        $mdlPage = new Model_Page();
		$id = $this->getRequest()->getParam('id');
        
		$mdlPage->delete($id);
		$this->_redirect('/admin/page');
	}

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'title');
        $direction = $this->_getParam('direction', 'desc');
        /**
         * sets default order if model does not have proper field
         */
//        if(!is_callable(array('Model_Page',
//            'get' . ucfirst($order)))) {
//            $order = 'title';
//        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}
?>

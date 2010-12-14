<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * ContentnodeController processes requests to content nodes
 * load/copy/delete nodes actions.
 *
 * @author miholeus
 */
class Admin_ContentnodeController extends Zend_Controller_Action
{
    /**
     * Loads Info of Current Node on Page
     */
    public function loadnodeAction()
    {
        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender();
        $pageId = $this->_getParam('page');
        $node = $this->_getParam('node');

        $mdlContentNode = new Admin_Model_ContentNode(array(
            'pageId'    => $pageId,
            'name'      => $node
        ));
        try {
            $mdlContentNodeMapper = new Admin_Model_ContentNodeMapper();
            $mdlContentNodeMapper->loadNodeInfo($mdlContentNode);
        } catch (Exception $e) {
            // log error
        }
        
        $this->view->node = $mdlContentNode;
    }
    /**
     * Copy node info to all pages
     * 
     * echo string json message whether copying succeeded or not
     */
    public function copynodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $post = $this->_request->getPost();
        if(empty($post['page']) || empty($post['node'])) {
            $isSucceeded = false;
        } else {
            $pageId = $post['page'];
            $node = $post['node'];

            $mdlContentNode = new Admin_Model_ContentNode(array(
                'pageId'    => $pageId,
                'name'      => $node
            ));
            $mdlContentNodeMapper = new Admin_Model_ContentNodeMapper();

            $mdlPage = new Model_Page();
            $allPages = $mdlPage->fetchAll();
            $isSucceeded = $mdlContentNodeMapper->copyToPages($allPages, $mdlContentNode);
        }
        echo Zend_Json_Encoder::encode(array('success' => $isSucceeded));
    }
    /**
     * Delete node by its id
     * echo json message whether operation succeeded or not
     */
    public function deletenodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $post = $this->_request->getPost();
        if(!isset($post['nodeId'])) {
            $isSucceeded = false;
        } else {
            $id = $post['nodeId'];

            $contentNodeMapper = new Admin_Model_ContentNodeMapper();
            // @todo return values for delete action
            $isSucceeded = (bool)$contentNodeMapper->delete($id);
        }
        echo Zend_Json_Encoder::encode(array('success' => $isSucceeded));
    }
}

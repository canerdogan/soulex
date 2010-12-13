<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_EventController processes requests to events
 *
 * @author miholeus
 */
class Admin_EventsController extends Soulex_Controller_Abstract
{
    public function indexAction()
    {
        $eventsMapper = new Admin_Model_EventsMapper();

        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getParam('limit', 20);

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $eventsMapper->published($post['filter_published'])
                                ->search($post['filter_search'])
                                ->order($order)->paginate();
            $this->view->filter['published'] = $post['filter_published'];

            try {
                if (is_array($post['cid'])) {
                    if(count($post['cid']) != $post['boxchecked']) {
                        throw new LengthException("Checksum is not correct");
                    }
                    try {
                        $eventsMapper->deleteBulk($post['cid']);
                        return $this->_redirect('/admin/events');
                    } catch (Exception $e) {
                        throw new RuntimeException($e->getMessage(), $e->getCode());
                    }
                }
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Events deletion failed with the following error: "
                        . $e->getMessage());
            }
        } else {
            $paginator = $eventsMapper->order($order)->paginate();
        }

        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }

        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);
        
        $this->view->render('events/index.phtml');
    }

    public function editAction()
    {
        $frmEvents = new Admin_Form_Events();

        if($this->getRequest()->isPost() &&
            $frmEvents->isValid($this->getRequest()->getPost())) {
            $data = array(
                'id' => $frmEvents->getValue('id'),
                'title' => $frmEvents->getValue('title'),
                'short_description' => $frmEvents->getValue('short_description'),
                'detail_description' => $frmEvents->getValue('detail_description'),
                'img_preview' => $frmEvents->getValue('img_preview'),
                'published' => $frmEvents->getValue('published'),
                'updated_at' => date("Y-m-d H:i:s"),
                'published_at' => $frmEvents->getValue('published_at')
            );
            $events = new Admin_Model_Events($data);
            $eventsMapper = new Admin_Model_EventsMapper();
            $eventsMapper->save($events);

            $this->disableContentRender();

            return $this->_forward('index');
        } else {
            $eventsMapper = new Admin_Model_EventsMapper();
            $currentEvents = $eventsMapper->findById($this->getRequest()->getParam('id'));
            $frmEvents->populate(array(
               'id' => $currentEvents->getId(),
                'title' => $currentEvents->getTitle(),
                'short_description' => $currentEvents->getShort_description(),
                'detail_description' => $currentEvents->getDetail_description(),
                'img_preview' => $currentEvents->getImg_preview(),
                'published' => $currentEvents->getPublished(),
                'published_at' => $currentEvents->getPublished_at()
            ));
        }

        $this->view->form = $frmEvents;

        $this->renderSubmenu(false);
        $this->view->render('events/edit.phtml');
    }

    public function createAction()
    {
        $frmEvents = new Admin_Form_Events();

        if($this->getRequest()->isPost() &&
               $frmEvents->isValid($this->getRequest()->getPost()) ) {
            $data = array(
                'title' => $frmEvents->getValue('title'),
                'short_description' => $frmEvents->getValue('short_description'),
                'detail_description' => $frmEvents->getValue('detail_description'),
                'published' => $frmEvents->getValue('published'),
                'img_preview' => $frmEvents->getValue('img_preview'),
                'published_at' => $frmEvents->getValue('published_at')
            );
            $events = new Admin_Model_Events($data);
            $eventsMapper = new Admin_Model_EventsMapper();
            $eventsMapper->save($events);

            $this->disableContentRender();

            return $this->_forward('index');
        }

        $this->view->form = $frmEvents;

        $this->renderSubmenu(false);
        $this->view->render('events/create.phtml');
    }
    
    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'title');
        $direction = $this->_getParam('direction', 'desc');
        /**
         * sets default order if model does not have proper field
         */
        if(!is_callable(array('Admin_Model_News',
            'get' . ucfirst($order)))) {
            $order = 'title';
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}

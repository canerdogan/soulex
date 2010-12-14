<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * MenuitemController processes requests to menu items
 *
 * @author miholeus
 */
class Admin_MenuitemController extends Soulex_Controller_Abstract
{
    /**
     * Show menu items including different search criteria
     * 
     * @return void
     */
    public function indexAction()
    {
        $menuItemMapper = new Admin_Model_MenuItemMapper();
        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getParam('limit', 20);

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $menuItemMapper->published($post['filter_state'])
                                ->menuId($post['filter_menuid'])
                                ->level($post['filter_level'])
                                ->search($post['filter_search'])
                                ->order($order)->paginate();

            $this->view->filter['state'] = $post['filter_state'];
            $this->view->filter['menuid'] = $post['filter_menuid'];
            $this->view->filter['level'] = $post['filter_level'];

            if(isset($post['cid'])) {
                if(is_array($post['cid'])
                        && count($post['cid']) == $post['boxchecked']) {
                    $menuItemMapper->delete($post['cid']);
                    return $this->_redirect('/admin/menuitem');
                } else {
                    throw new Exception('FCS  is not correct! Wrong request!');
                }
            }
        } else {
            $paginator = $menuItemMapper->menuId($this->_getParam('menuid'))
                    ->order($order)->paginate();
            $this->view->filter['menuid'] = $this->_getParam('menuid');
        }

        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }

        $page = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($page);
        // pass the paginator to the view to render
        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);
        
        $maxMenuLevel = $menuItemMapper->findMaxLevel();

        $this->view->menuLevels = array_combine(array_values(range(1, $maxMenuLevel)),
                range(1, $maxMenuLevel));

        $menuMapper = new Admin_Model_MenuMapper();
        $menus = $menuMapper->fetchAll();

        $view_menus = array();
        foreach($menus as $menu) {
            $view_menus[$menu->getId()] = $menu->getTitle();
        }
        $this->view->menus = $view_menus;

        $this->view->render('menuitem/index.phtml');
    }
    /**
     * Show menu item form and create a new item while postback
     * 
     * @return void
     */
    public function createAction()
    {
        $frmMenuItem = new Admin_Form_MenuItem();
        $menuMapper = new Admin_Model_MenuMapper();
        $menus = $menuMapper->fetchAll();
        foreach($menus as $menu) {
            $frmMenuItem->addElementOption('menu_id', $menu->getId(), $menu->getTitle());
        }

        $mdlMenuItem = new Admin_Model_MenuItem();
        $menuItemMapper = new Admin_Model_MenuItemMapper();
        $items = $menuItemMapper->fetchAllGrouppedByParentId();
        $mdlMenuItem->processTreeElementForm($items, $frmMenuItem, 'parent_id');

        if($this->_request->isPost() &&
                $frmMenuItem->isValid($this->_request->getPost())) {
            $frmMenuItem->removeElement('id');

            try {
                $mdlMenuItem = new Admin_Model_MenuItem($frmMenuItem->getValues());
                $menuItemMapper->save($mdlMenuItem);
                return $this->_redirect('/admin/menuitem');
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("MenuItem creation failed with the following error: "
                    . $e->getMessage());
            }
        }

        $this->view->form = $frmMenuItem;
        $this->renderSubmenu(false);
        $this->view->render('menuitem/create.phtml');
    }
    /**
     * Show menu item form for editing info and update menu item
     * 
     * @return void
     */
    public function editAction()
    {
        $id = $this->_getParam('id');
        $frmMenuItem = new Admin_Form_MenuItem();
        $menuMapper = new Admin_Model_MenuMapper();
        $menus = $menuMapper->fetchAll();
        foreach($menus as $menu) {
            $frmMenuItem->addElementOption('menu_id', $menu->getId(), $menu->getTitle());
        }

        $mdlMenuItem = new Admin_Model_MenuItem();
        $menuItemMapper = new Admin_Model_MenuItemMapper();
        try {
            $menuItem = $menuItemMapper->findById($id);
            $items = $menuItemMapper->fetchAllGrouppedByParentId();
            $mdlMenuItem->processTreeElementForm($items, $frmMenuItem, 'parent_id',
                    $menuItem->getParent_id());
            $frmMenuItem->getElement('parent_id')->removeMultiOption($id);
            $frmMenuItem->populate(array(
                'id' => $menuItem->getId(),
                'label' => $menuItem->getLabel(),
                'uri' => $menuItem->getUri(),
                'menu_id' => $menuItem->getMenu_id(),
                'position' => $menuItem->getPosition(),
                'published' => $menuItem->getPublished()
            ));
        } catch(Exception $e) {
            $this->renderError($e->getMessage());
        }

        if($this->_request->isPost() &&
                $frmMenuItem->isValid($this->_request->getPost())) {
            try {
                $mdlMenuItem = new Admin_Model_MenuItem($frmMenuItem->getValues());
                $menuItemMapper->save($mdlMenuItem);

                return $this->_redirect('/admin/menuitem');
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("MenuItem update failed with the following error: "
                    . $e->getMessage());
            }
        }


        $this->view->form = $frmMenuItem;
        $this->renderSubmenu(false);
        $this->view->render('menuitem/edit.phtml');
    }
    /**
     * Delete menu item by its id
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $menuItemMapper = new Admin_Model_MenuItemMapper();
        $menuItemMapper->delete($id);
        $this->_redirect('/admin/menuitem');
    }

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'lft');
        $direction = $this->_getParam('direction', 'asc');
        /**
         * sets default order if model does not have proper field
         */
        if(!is_callable(array(new Admin_Model_MenuItem(),
            'get' . ucfirst($order)))) {
            $order = 'lft';
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}

<?php
/**
 * i-PMS - internet Project Management System
 * Copyright (C) 2011 by Laurent Declercq
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @category    iPMS
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Posts controller
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Blog_PostsController extends Zend_Controller_Action
{
    /**
     * List post
     *
     * @return void
     */
    public function indexAction()
    {



        $model = new Blog_Model_DbTable_Posts();
        // Todo get max post per pages from user settings (currently hardcoded to 15)
        $pageablePosts = $model->getPageablePostsList($this->_request->getParam('page', 1));
        $this->view->assign('paginator', $pageablePosts);
    }

    /**
     * Show a specific post
     *
     * @return void
     */
    public function showAction()
    {
        $id = (int) $this->_getParam('id');

        $model = new Blog_Model_DbTable_Posts();
        $row = $model->fetchRow($model->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false)
            ->where('posts.id = ?', $id)
            ->join('users', '`users`.`id` = `posts`.`author_id`', array('username', 'firstname', 'lastname'))
        );

        if (!$row) {
            $this->getResponse()->setHttpResponseCode(404);
            throw new Zend_Controller_Action_Exception('Post not found!', 404);
        }

        $this->view->assign('post', $row);
    }

    /**
     * Create a new post
     *
     * @return void
     */
    public function addAction()
    {
        $form = new Blog_Form_Post();
        $identity = Zend_Auth::getInstance()->getIdentity()->id;

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $data = $form->getValues('postForm');
            $data['author_id'] = $identity['id'];

            $model = new Blog_Model_DbTable_Posts();
			print_r($data);
            $id = $model->insert($data);

            $this->_redirect("/posts/{$id}");
        }

        $this->view->assign('form', $form);
    }

    /**
     * Update a post
     *
     * @return void
     */
    public function editAction()
    {
        $id = (int) $this->_request->getParam('id');
        $model = new Blog_Model_DbTable_Posts();

        if (null == ($row = $model->find($id)->current())) {
            throw new Zend_Controller_Action_Exception("Post not found!", 404);
        } else {
            $form = new Blog_Form_Post();

            if ($this->_request->isPost() && $form->isValid($this->_request->getPost('postForm'))) {
                $row->setFromArray($form->getValues('postForm'))->save();
                $this->_redirect("posts/{$id}");
            } else {
                $form->populate($row->toArray());
            }

            $form->setAction("/posts/{$id}/edit");
            $this->view->assign('form', $form);
        }
    }

    /**
     * Deletes a post and its comments
     *
     * @return void
     */
    public function deleteAction()
    {
        $model = new Blog_Model_DbTable_Posts();
        $row = $model->find((int) $this->_request->getParam('id', 0))->current();

		if(null !== $row) {
			$row->delete();
		}

		$this->_redirect('/');
    }
}

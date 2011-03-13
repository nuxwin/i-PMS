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
 * @author      Laurent Declercq <laurent.declercq@i-mscp.net>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Posts controller
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0.0
 */
class PostsController extends Zend_Controller_Action
{

    /**
     * List post
     *
     * @return void
     */
    public function indexAction()
    {
	$model = new Model_DbTable_Posts();
	$pageablePosts = $model->getPageablePostsList((int) $this->_request->getParam('page', 1), 1);
	$this->view->assign('paginator', $pageablePosts);
    }

    /**
     * Show a post
     * 
     * @return void
     */
    public function showAction()
    {
	$id = (int) $this->_getParam('id', 0);

	$model = new Model_DbTable_Posts();
	$row = $model->fetchRow($model->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
				->setIntegrityCheck(false)
				->where('posts.id = ?', $id)
				->join('users', '`users`.`id` = `posts`.`author_id`', array('username', 'firstname', 'lastname'))
	);

	if (!$row) {
	    $this->getResponse()->setHttpResponseCode(404);
	    throw new Zend_Controller_Action_Exception('Post not found!', 404);
	    return;
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
	$form = new Form_Post();
	$identity = Zend_Auth::getInstance()->getIdentity()->id;

	if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
	    $data = $form->getValues();
	    $data['author_id'] = $identity['id'];

	    $model = new Model_DbTable_Posts();
	    $id = $model->insert($data);

	    $this->_redirect('/posts/id/' . $id);
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
	$postId = (int) $this->_request->getParam('id');

	$postsModel = new Model_DbTable_Posts();

	if (null == ($postRow = $postsModel->find($postId)->current())) {
	    throw new Zend_Controller_Action_Exception("Post not found!", 404);
	} else {
	    $form = new Form_Post();

	    if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
		$postRow->setFromArray($form->getValues())->save();
		$this->_redirect('posts/view/id/' . $postId);
	    } else {
		$form->populate($postRow->toArray());
	    }

	    $form->setAction('/posts/edit');
	    $this->view->assign('postForm', $form);
	}
    }

    /**
     * Delete a post and his related comments
     *
     * @return void
     */
    public function deleteAction()
    {
	$postModel = new Model_DbTable_Posts();
	$postModel->delete((int) $this->_request->getParam('id'));
    }

}

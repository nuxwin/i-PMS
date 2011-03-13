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
 * @package     iPMS_Controllers
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     SVN: $Id$
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Comments controller
 *
 * @category    iPMS
 * @package     iPMS_Controllers
 * @subpackage  comments
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.0
 */
class CommentsController extends Zend_Controller_Action
{

    /**
     * List all comments linked to one object
     *
     * @return void
     */
    public function indexAction()
    {
	$parent = $this->_request->getParam('parent');
	$comments = new Model_DbTable_Comments();
	$comments = $comments->getComments($parent);
	$this->view->assign(array('comments' => $comments));
    }

    /**
     * Add comment
     *
     * @return void
     */
    public function addAction()
    {

	$parentId = (int) $this->_request->getParam('id');

	// Getting comment form
	$form = new Form_Comments();

	if ($this->_request->isPost() && $form->isValid($this->_request->getParams())) {
	    if (null == $parentId) {
		throw new Zend_Controller_Action_Exception("Comment parent ID not found!", 404);
	    }

	    $model = new Model_DbTable_Comments();
	    $model->insert($Form->getValues());
	    $form->reset();
	}

	$form->setAction($parentController . '/' . $parentId . '/comments');
	$this->view->assign('form', $form);
    }

    /**
     * Delete comment
     *
     * @throws Zend_Controller_Action_Exception
     * @return void
     */
    public function deleteAction()
    {
	$commentId = (int) $this->_request->getParam('id');

	if (null == $commentId) {
	    throw new Zend_Controller_Action_Exception("Post ID not found!", 404);
	} else {
	    $commentsModel = new Model_DbTable_Comments();
	    $commentsModel->delete($commentId);
	}

	// Todo redirect to origin
	$this->_redirect('/');
    }

}

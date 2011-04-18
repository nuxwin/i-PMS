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
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
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
 * @version     0.0.1
 */
class CommentsController extends Zend_Controller_Action
{

	/**
	 * List all comments belong to one object
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$parent = $this->_request->getParam('parent');
		$comments = new Model_DbTable_Comments();
		$comments = $comments->getComments($parent);
		$this->view->assign('comments', $comments);
	}

	/**
	 * Add comment
	 *
	 * @return void
	 */
	public function addAction()
	{
		$parentController = $this->_request->getParam('pCtrl', null);
		$parentId = $this->_request->getParam('pId', null);

		if(!is_null($parentController) && !is_null($parentId)) {
			$form = new Form_Comments();

			if ($this->_request->isPost() && $form->isValid($this->_request->getParams('commentsForm'))) {
				$model = new Model_DbTable_Comments();
				$model->insert($form->getValues());
				$form->reset();
			}

			$form->setAction($form->getAction() . "/?pCtrl={$parentController}&pId={$parentId}");
			$this->view->assign('form', $form);
		} else {
			$this->_redirect('/');
		}
	}

	/**
	 * Delete comment
	 *
	 * @throws Zend_Controller_Action_Exception
	 * @return void
	 */
	public function deleteAction()
	{
		$id = (int)$this->_request->getParam('id');

		$model = new Model_DbTable_Comments();
		$model->delete($id);

		$this->_redirect('/');
	}

}

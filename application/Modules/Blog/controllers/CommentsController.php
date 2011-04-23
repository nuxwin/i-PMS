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
class Blog_CommentsController extends Zend_Controller_Action
{

	/**
	 * @var Zend_Controller_Action_Helper_Url
	 */
	protected $urlHelper = null;

	/**
	 * @return void
	 */
	public function init()
	{
		$this->urlHelper = $this->_helper->getHelper('Url');
	}

	/**
	 * List all comments belong to one object
	 *
	 * @return void
	 */
	public function indexAction()
	{
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

		$parent = $request->getParam('parent');
		$commentsModel = new Blog_Model_DbTable_Comments();
		$comments = $commentsModel->getComments($parent);
		$this->view->assign('comments', $comments);
	}

	/**
	 * Add comment
	 *
	 * @return void
	 */
	public function addAction()
	{
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

		$parentController =$request->getParam('pCtrl', null);
		$parentId = $request->getParam('pid', null);

		if(null !== $parentController && null !== $parentId) {
			$form = new Blog_Form_Comments();

			if ($request->isPost() && $form->isValid($request->getParam('commentsForm'))) {
				$commentsModel = new Blog_Model_DbTable_Comments();
				$commentsModel->insert($form->getValues(true));
				$form->reset();
			}

			$form->setAction($form->getAction() . "/?pCtrl={$parentController}&pid={$parentId}");
			$this->view->assign('form', $form);
		} else {
			$this->_redirect($this->urlHelper->url(array(), 'home'));
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
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

		$cid = intval($request->getParam('cid'));

		$commentsModel = new Blog_Model_DbTable_Comments();
		$commentsModel->delete($cid);

		$this->_redirect('/');
	}

}

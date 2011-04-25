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
class Comment_CommentsController extends Zend_Controller_Action
{

	/**
	 * Commented object model classname
	 *
	 * @var string
	 */
	protected $_parentModelClass = null;

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

		$pid = intval($request->getParam('pid'));
		$commentsModel = new Comment_Model_DbTable_Comments();
		$comments = $commentsModel->getComments($pid);
		$this->view->assign('comments', $comments);
	}

	/**
	 * Add a comment
	 *
	 * @return void
	 */
	public function addAction()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		// Retrieve id of commented object
		$pid = intval($request->getParam('pid'));

		$ns = new Zend_Session_Namespace('Comment_CommentsController');
		$ns->setExpirationHops(1);

		$form = new Comment_Form_Comment();

		if(isset($ns->commentFormData)) {
			$form->setDefaults($ns->commentFormData);
			foreach($ns->commentFormErrorsMessages as $elementName => $error) {
				$form->getElement($elementName)->addErrors($error);
			}
		}

		$fromRoute = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
		$toRoute = $this->urlHelper->url();
		if('comment_add' !== $fromRoute) {
			$ns->commentFromRoute = $fromRoute;
			$toRoute = $toRoute . '/comment';
		}
		$form->setAction($toRoute);

		if ($request->isPost()) {
			$parentModel = new $this->_parentModelClass;
			$parentObject = $parentModel->find($pid)->current();

			if (!$parentObject) {
				throw new Zend_Controller_Action_Exception(
					'Unable to find commented object in database', 500);
			}

			if($form->isValid($request->getParam('commentForm'))) {
				$commentsModel = new Comment_Model_DbTable_Comments();
				$data = $form->getValues(true);
				$data['pid'] = $pid;
				$identity = Zend_Auth::getInstance();
				if($identity->hasIdentity()) {
					$data['uid'] = $identity->getIdentity()->uid;
					$data['name'] = $identity->getIdentity()->username;
					$data['email'] = $identity->getIdentity()->email;
					$data['created_on'] = time();
				}
				$commentsModel->insert($data);
			} else {
				$ns->commentFormData = $form->getValues();
				$ns->commentFormErrorsMessages = $form->getMessages(null, true);
			}

			$this->_redirect($this->urlHelper->url(array('pid' => $pid), $ns->commentFromRoute));
		}

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
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

		$cid = intval($request->getParam('cid'));

		$commentsModel = new Comment_Model_DbTable_Comments();
		$commentsModel->delete(array('cid = ?' => $cid));

		// TODO redirect to previous route
		$this->_redirect('/');
	}

	/**
	 * Retrieve parent model classname from the route
	 * 
	 * @throws Zend_Controller_Action_Exception
	 * @return string parent model classname
	 */
	public function preDispatch()
	{
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

		if($request->isPost() && $request->getActionName() == 'add') {
			$models = $request->getUserParam('models');
			$pcontroller = $request->getUserParam('pcontroller');

			if(array_key_exists($pcontroller, $models)) {
				$this->_parentModelClass = $models[$pcontroller];
			} else {
				throw new Zend_Controller_Action_Exception('Unable to find parent model', 500);
			}
		}
	}
}

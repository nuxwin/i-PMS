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
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Forums controller
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Forum_ForumController extends Zend_Controller_Action {

	/**
	 * @var Zend_Controller_Action_Helper_Url
	 */
	protected $urlHelper = null;

	/**
	 * Initialize controller
	 *
	 * @return void
	 */
	public function init()
	{
		$this->urlHelper = $this->_helper->getHelper('Url');
	}

	/**
	 * Forums index
	 *
	 * @return void
	 */
	public function indexAction()
	{
		// @todo review ordering
		$model = new Forum_Model_DbTable_Forum();
		$forums = $model->fetchAll(null, array('lastposter_id', 'order'));
		$this->view->assign('forums', $forums->toArray());
	}

	/**
	 * Show a forum
	 *
	 * @throws Zend_Controller_Exception
	 * @return void
	 * @todo pagination
	 */
	public function showAction()
	{

		if($this->_request->isPost()) {
			echo '<pre>';
			print_r($this->_request->getParams());
			echo '</pre>';
			exit;
		}

		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		$fid = intval($request->getParam('fid'));
		$forumModel = new Forum_Model_DbTable_Forum();
		$forum = $forumModel->find($fid)->current();

		if(!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Forum with id '%d' was not found"), $fid), 404);
		}

		$select = $forumModel->select()
			->setIntegrityCheck(false)
			->from('fthreads')
			->joinLeft(array('u' => 'users'), 'u.id = fthreads.uid')
			->order(array('fthreads.is_sticky DESC', 'fthreads.lastpost_date DESC'));

		$threads = $forum->findDependentRowset('Forum_Model_DbTable_Thread', null, $select);

		$this->view->assign(array('forum' => $forum->toArray(), 'threads' => $threads->toArray()));
	}

	/**
	 * Add a new forum
	 *
	 * @return void
	 * @todo prevent double entries
	 */
	public function addAction()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		$form = new Forum_Form_Forum();

		if($request->isPost() && $form->isValid($request->getParam('forumForm'))) {
			$model = new Forum_Model_DbTable_Forum();
			$model->insert($form->getValues('forumForm'));
			$this->_redirect($this->urlHelper->url(array(), 'forum_add'));
		}

		$this->view->assign('form', $form);
	}

	/**
	 * Edit a forum
	 *
	 * @throws Zend_Controller_Exception
	 * @return void
	 */
	public function editAction()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		$fid = intval($request->getParam('fid'));
		$model = new Forum_Model_DbTable_Forum();

		$forum = $model->find($fid)->current();

		if(!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Forum with id '%d' was not found"), $fid), 404);
		}

		$form = new Forum_Form_Forum();

		if($request->isPost() && $form->isValid($this->_getParam('forumForm'))) {
			$forum->setFromArray($form->getValues())->save();
			$this->_redirect($this->urlHelper->url(array('fid' => $fid), 'forum_show'));
		}

		$this->view->assign('form', $form);
	}

	/**
	 * Deletes a forum and all its threads
	 *
	 * @return void
	 */
	public function deleteAction()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		$fid = intval($request->getParam('fid'));
		$model = new Forum_Model_DbTable_Forum();
		$forum = $model->find($fid)->current();

		if($forum) {
			$forum->delete();
		}

		$this->_redirect($this->urlHelper->url(array(), 'forum_index'));
	}
}

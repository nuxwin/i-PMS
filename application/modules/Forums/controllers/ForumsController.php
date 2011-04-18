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
class Forums_ForumsController extends Zend_Controller_Action {

	/**
	 * Forums index
	 *
	 * @return void
	 */
	public function indexAction()
	{
		// @todo review ordering
		$model = new Forums_Model_DbTable_Forums();
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
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		$fid = intval($request->getParam('fid'));
		$model = new Forums_Model_DbTable_Forums();
		$forum = $model->find($fid)->current();

		if(!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Forum with id '%d' was not found"), $fid), 404);
		}

		$select = $model->select()
			->setIntegrityCheck(false)
			->from('forums_threads')
			->joinLeft(array('u' => 'users'), 'u.id = forums_threads.uid')
			->order(array('forums_threads.is_sticky DESC', 'forums_threads.lastpost_date DESC'));

		$threads = $forum->findDependentRowset('Forums_Model_DbTable_Threads', null, $select);

		$this->view->assign(array(
			'forum' => $forum->toArray(), 'threads' => $threads->toArray())
		);
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

		$form = new Forums_Form_Forum();

		if($request->isPost() && $form->isValid($request->getParam('forumForm'))) {
			$model = new Forums_Model_DbTable_Forums();
			$model->insert($form->getValues('forumForm'));

			$this->_redirect("/forums");
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
		$model = new Forums_Model_DbTable_Forums();

		$forum = $model->find($fid)->current();

		if(!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Forum with id '%d' was not found"), $fid), 404);
		}

		$form = new Forums_Form_Forum();

		if($request->isPost() && $form->isValid($this->_getParam('forumForm'))) {
			$forum->setFromArray($form->getValues())->save();
			$this->_redirect("/forums/{$fid}");
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
		$model = new Forums_Model_DbTable_Forums();
		$forum = $model->find($fid)->current();

		if($forum) {
			$forum->delete();
		}

		$this->_redirect('/forums');
	}
}

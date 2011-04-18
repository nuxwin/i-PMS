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
 * Threads controller
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class Forums_ThreadsController extends Zend_Controller_Action
{
	/**
	 * Sub-action for the show Action
	 * 
	 * @var string
	 */
	protected $showAction = 'thread';

	/**
	 * Show a thread
	 *
	 * @return void
	 */
	public function showAction()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();
		$tid = intval($request->getParam('tid'));

		// Get the thread details from the database
		// TODO get these data from a cache
		$threadsModel = new Model_DbTable_ForumsThreads();
		$thread = $threadsModel->find($tid)->current();

		// Getting thread information
		if(!$thread) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Thread with id '%s' not found", $tid)), 404);
		}

		// Get the forum details from the database
		// TODO get these data from a cache
		$forumsModel = new Forums_Model_DbTable_Forums();
		$forum = $forumsModel->find($thread['fid'])->current();

		if(!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate(sprintf("Forum with id '%s' not found", $thread['fid']))), 404);
		}

		// Fetch all posts belonging to the thread
		$postsModel = new Forums_Model_DbTable_Posts();
		$posts = $postsModel->fetchAll(array('tid = ?' => $tid), 'created_on');

		// Updating views counter
		$thread['count_views'] = $thread['count_views'] + 1;
		$thread->save();

		$this->view->assign(array(
		                         'forum' => $forum->toArray(),
		                         'thread' => $thread->toArray(),
		                         'posts' => $posts->toArray()));
 	}

	/**
	 * Jump to the last post in a thread
	 * 
	 * @return void
	 */
	public function lastpostAction()
	{
		$this->showAction = 'laspost';
		$this->_forward('show');
	}

	/**
	 * Add a new thread
	 * 
	 * @throws Zend_Controller_Action_Exception
	 * @return void
	 */
	public function addAction()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		$fid = intval($request->getParam('fid'));
		$forumModel = new Forums_Model_DbTable_Forums();
		$forum = $forumModel->find($fid)->current();

		if(!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Unable to create new thread - Forum with id '%d' was not found"), $fid), 500);
		}

		$form = new Forums_Form_Thread();
		$form->setForm('add');
		$form->setAction($request->getRequestUri());

		if($request->isPost() && $form->isValid($request->getParam('threadForm'))) {
			try {
				$time = time();
				$db = $forumModel->getDefaultAdapter();

				// Leave autocommit mode and begin a transaction
				$db->beginTransaction();

				// Preparing new thread for insertion in database
				$threadsModel = new Forums_Model_DbTable_Threads();
				$thread = $threadsModel->createRow($form->getValues(true));

				// Setting some field manually
				$thread->setFromArray(array(
				                           'fid' => $fid,
				                           'uid' => 1, // testing
				                           'username' => 'nuxwin', // testing
				                           'created_on' => $time,
				                           'lastpost_date' => $time,
				                           'lastposter_username' => 'nuxwin', // testing
				                           'lastposter_id' => 1,
				                           'count_views' => 0,
				                           'count_replies' => 0));

				// Inserting new thread in database
				$tid = $thread->save();

				// Inserting related post in database
				$postsModel = new Forums_Model_DbTable_Posts();
				$pid = $postsModel->insert(array(
				                                'tid' => $tid,
				                                'fid' => $fid,
				                                'uid' => 1, // testing
				                                'username' => 'nuxwin', // testing
				                                'created_on' => $time,
				                                'reply_to' => 0,
				                                'subject' => $form->getValue('subject'),
				                                'message' => $form->getValue('message')));

				// Now that we known the id for this first post, update the threads table.
				$thread['firstpost_id'] = $pid;
				$thread->save();

				// Updating some fields in forum database
				$forum->setFromArray(array(
				                          'count_threads' => $forum['count_threads'] + 1,
				                          'count_posts' => $forum['count_posts'] + 1,
				                          'lastpost_date' => $time,
				                          'lastposter_username' => 'nuxwin', // testing
				                          'lastposter_id' => 1, // testing
				                          'lastthread_id' => $tid,
				                          'lastthread_subject' => $form->getValue('subject')))->save();

				// Commit the transaction and return to autocommit mode
				$db->commit();

			} catch(Exception $e) {

				// Roll back the transaction and return to autocommit mode
				$db->rollBack();

				throw new Zend_Controller_Action_Exception(
					$this->view->translate('Unable to create new thread - ' . $e->getMessage()), 500, $e);
			}

			$this->_redirect("/forums/thread/{$tid}");
		}

		$this->view->assign(array('forum' => $forum, 'form' => $form));
	}

	/**
	 * Reply to a thread or to a specific post in a thread
	 * 
	 * @throws Zend_Controller_Action_Exception
	 * @return void
	 */
	public function replyAction()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();
		$tid = intval($request->getParam('tid'));

		$modelThread = new Forums_Model_DbTable_Threads();
		$thread = $modelThread->find($tid)->current();

		if(!$thread) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Thread with id '%s' not found", $tid)), 404);
		}

		// Try to retrieve post pid (case where we answer to a specific post)
		$pid = intval($request->getParam('pid'));

		$form = new Forums_Form_Thread();
		$form->setForm('reply');

		if($request->isPost() && $form->isValid($request->getParam('replyForm'))) {
			try {
				$time = time();
				$db = $modelThread->getDefaultAdapter();

				// Leave autocommit mode and begin a transaction
				$db->beginTransaction();

				// Inserting post in database
				$postsModel = new Forums_Model_DbTable_Posts();
				$postsModel->createRow($form->getValues(true));

				// Setting some field manually
				$thread->setFromArray(array(
				                           'tid' => $tid,
				                           'fid' => $fid,
				                           'uid' => 1, // testing
				                           'username' => 'nuxwin', // testing
				                           'created_on' => $time,
				                           'reply_to' => $tid)); // TODO can be a post id too

				$pid = $thread->save();

				// Updating replies counter in forums_threads table
				$thread['count_replies'] = $thread['count_replies'] + 1;
				$thread->save();

				// Updating posts counters in forums table
				$forum['count_posts'] = $forum['count_posts'] + 1;
				$forum->save();

				// TODO update lasposter_username in forums table
				// TODO update laspost_date in forums table
				// TODO update lastposter_id in forums and forums_threads tables

				// Commit the transaction and return to autocommit mode
				$db->commit();

			} catch(Exception $e) {

				// Roll back the transaction and return to autocommit mode
				$db->rollBack();

				throw new Zend_Controller_Action_Exception(
					$this->view->translate('Unable to create new post - ' . $e->getMessage()), 500, $e);
			}

			$this->_redirect("/forums/thread/{$tid}/#pid{$pid}");
		}

		$form->getElement('subject')
			->setValue('RE: ' . $thread['subject']);

		// TODO reply to a specific post
		$form->setAction("/forums/thread/{$tid}/reply");

		$this->view->assign(array('thread' => $thread, 'form' => $form));
	}

	/**
	 * Delete a thread
	 *
	 * @throws Zend_Controller_Action_Exception
	 * @return void
	 */
	public function deleteThread()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();
		$tid = intval($request->getParam('tid'));

		$modelThread = new Forums_Model_DbTable_Threads();
		$thread = $modelThread->find($tid)->current();

		if(!$thread) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Thread with id '%s' not found", $tid)), 404);
		}

		// TODO Forum posts counter must be updated
		// TODO Forum threads counter must be updated
		// TODO User(s) posts counter must be updated
	}

	/**
	 * Delete a post in a thread
	 * 
	 * @return void
	 */
	public function deletePost()
	{
		// TODO Forum posts counter must be updated
		// TODO Thread replies counter must be updated
		// TODO User posts counter must be updated
	}
}

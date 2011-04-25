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
class Forum_ThreadController extends Zend_Controller_Action
{

	/**
	 * @var Zend_Controller_Action_Helper_Url
	 */
	protected $urlHelper = null;

	/**
	 * Sub-action for the show Action
	 * 
	 * @var string
	 */
	protected $showAction = 'thread';

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
		$threadsModel = new Forum_Model_DbTable_Thread();
		$thread = $threadsModel->find($tid)->current();

		// Getting thread information
		if(!$thread) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Thread with id '%d' was not found", $tid)), 404);
		}

		// Get the forum details from the database
		// TODO get these data from a cache
		$forumsModel = new Forum_Model_DbTable_Forum();
		$forum = $forumsModel->find($thread['fid'])->current();

		if(!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				sprintf("Unable to show thread: Parent forum with ID '%d' was not found", $thread['fid'])), 500);
		}

		// Fetch all posts belonging to the thread
		// TODO pagination
		$postsModel = new Forum_Model_DbTable_Post();
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

		// TODO get these data from a cache
		$forumModel = new Forum_Model_DbTable_Forum();
		$forum = $forumModel->find($fid)->current();

		if(!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Unable to create new thread - Forum with ID '%d' was not found"), $fid), 500);
		}

		$form = new Forum_Form_Thread();
		$form->setForm('add');
		$form->setAction($request->getRequestUri());

		if($request->isPost() && $form->isValid($request->getParam('threadForm'))) {
			try {
				$time = time();
				$db = $forumModel->getDefaultAdapter();

				// Leave autocommit mode and begin a transaction
				$db->beginTransaction();

				// Preparing new thread for insertion in database
				$threadsModel = new Forum_Model_DbTable_Thread();
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

				// Inserting first post in database
				$postsModel = new Forum_Model_DbTable_Post();
				$pid = $postsModel->insert(array(
				                                'tid' => $tid,
				                                'fid' => $fid,
				                                'uid' => 1, // testing
				                                'username' => 'nuxwin', // testing
				                                'created_on' => $time,
				                                'reply_to' => 0,
				                                'subject' => $form->getValue('subject'),
				                                'message' => $form->getValue('message')));

				// Now that we known the ID for this first post, update the forums_threads table.
				$thread['firstpost_id'] = $pid;
				$thread->save();

				// Updating some fields in forums table
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

				throw new Zend_Controller_Action_Exception(sprintf(
					'Unable to create new thread: ', $e->getMessage()), 500, $e);
			}

			$this->_redirect($this->urlHelper->url(array('tid' => $tid), 'forum_thread_show'));
		}

		$this->view->assign(array(
		                         'forum' => $forum,
		                         'form' => $form));
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

		// TODO get these data from a cache
		$modelThread = new Forum_Model_DbTable_Thread();
		$thread = $modelThread->find($tid)->current();

		if(!$thread) {
			throw new Zend_Controller_Action_Exception(sprintf(
				$this->view->translate("Unable to reply: Parent thread with ID '%d' was not found", $tid)), 500);
		}

		// TODO get these data from a cache
		$forumModel = new Forum_Model_DbTable_Forum();
		$forum = $forumModel->find($thread['fid'])->current();

		if (!$forum) {
			throw new Zend_Controller_Action_Exception(sprintf(
				"Unable to reply: Parent forum with ID '%d' was not found!", $thread['fid']), 500);
		}

		$fid = $forum['fid'];
		$tid = $thread['tid'];

		// Try to retrieve post pid (case where we answer to a specific post)
		// $pid = intval($request->getParam('pid'));

		$form = new Forum_Form_Thread();
		$form->setForm('reply');

		if($request->isPost() && $form->isValid($request->getParam('replyForm'))) {
			try {
				$time = time();
				$db = $modelThread->getDefaultAdapter();

				// Leave autocommit mode and begin a transaction
				$db->beginTransaction();

				// Inserting new post in database
				$postsModel = new Forum_Model_DbTable_Post();
				$posts = $postsModel->createRow($form->getValues(true));

				// Sets some fields manually
				// TODO 'reply_to' can be a post ID too
				$pid = $posts->setFromArray(array(
				                          'tid' => $tid,
				                          'reply_to' => $tid, // Can post some problem if we grab tid like this
				                          'fid' => $fid,
				                          'uid' => 1, // testing
				                          'username' => 'nuxwin', // testing
				                          'created_on' => $time,
				                     ))->save();

				// Updating some fields in forums_threads table
				$thread->setFromArray(array(
				                           'lastpost_date' => $time,
				                           'lastposter_username' => 'nuxwin', // testing
				                           'lastposter_id' => '1', // testing
				                           'count_replies' => $thread['count_replies'] + 1))->save();

				// Updating some fields in forums table
				$forum->setFromArray(array(
				                          'count_posts' => $forum['count_posts'] + 1,
				                          'lastpost_date' => $time,
				                          'lastposter_username' => 'nuxwin', // testing,
				                          'lastposter_id' => 1)); // testing


				// Commit the transaction and return to autocommit mode
				$db->commit();

			} catch(Exception $e) {

				// Roll back the transaction and return to autocommit mode
				$db->rollBack();

				throw new Zend_Controller_Action_Exception(sprintf(
					'Unable to insert new post in database: %s', $e->getMessage()), 500, $e);
			}

			$this->_redirect($this->urlHelper->url(array('tid' => $tid), 'forum_thread_show') . "#pid{$pid}");
		}

		// Preparing post subject
		$form->getElement('subject')->setValue('RE: ' . $thread['subject']);

		// TODO reply to a specific post
		$form->setAction($this->urlHelper->url(array('tid' => $tid), 'forum_post_reply'));

		$this->view->assign(array('thread' => $thread->toArray(), 'form' => $form));
	}

	/**
	 * Delete a thread
	 *
	 * @throws Zend_Controller_Action_Exception
	 * @return void
	 */
	public function deleteThreadAction()
	{

		echo $this->urlHelper->url(array(), 'forum_add');

		//echo $this->_helper->url('delete.thread', null, null, array('tid' => '100'));
		exit;

		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		echo '<pre>';
		echo "deleteThreadAction\n";
		print_r($request->getParams());
		exit;

		// TODO Delete rows from dependent tables (posts)
		// TODO Forum posts counter must be updated
		// TODO Forum threads counter must be updated
		// TODO Forum lastpost_date, lastposter_username, lastposter_id, lastthread_id, lastthread_subject must be checked and updated if neede
		// TODO User(s) posts counter must be updated
	}

	/**
	 * Delete a post in a thread
	 * 
	 * @return void
	 */
	public function deletePostAction()
	{
		/**
		 * @var $request Zend_Controller_Request_Http
		 */
		$request = $this->getRequest();

		$pid = intval($request->getParam('pid'));

		// Get post info
		$modelPosts = new Forum_Model_DbTable_Post();
		$post = $modelPosts->find($pid)->current();

		if(!$post) {
			throw new Zend_Controller_Action_Exception(sprintf(
				"Unable to delete post: Post with ID '%d' not found", $pid), 500);
		}

		// Getting thread info
		$tid = $post['pid'];

		// TODO get these data from a cache
		$threadModel = new Forum_Model_DbTable_Thread();
		$thread = $threadModel->find($tid)->current();

		if(!$thread) {
			throw new Zend_Controller_Action_Exception(sprintf(
				"Unable to delete post: Parent thread with ID '%d' was not found", $tid), 500);
		}

		// Forward to deleteThreadAction if post to delete is the firstpost in the thread
		if($thread['firstpost_id'] == $pid) {
			$this->_forward('delete.thread', null, null, array('tid' => $tid));
		} else {

			// Get forum info
			$fid = $post['fid'];
			$forumModel = new Forum_Model_DbTable_Forum();
			$forum = $forumModel->find($fid)->current();

			if(!$forum) {
				throw new Zend_Controller_Action_Exception(sprintf(
					"Unable to delete post: Parent forum with ID '%d' was not found", $tid), 500);
			}


			// TODO  permissions checking (only admin, moderator and owner can delete post
			// TODO post cannot be delete if thread is closed and user is not moderator that can delete post in the parent forum
			// TODO post cannot be deleted if post deletion is not allowed in the parent forum
			// TODO forward to deleteThreadAction if post to delete is the firstpost in the thread OK
			// TODO Forum posts counter must be updated
			// TODO Forum lastpost_date, lastposter_username, lastposter_id must be checked and updated if needed
			// TODO Thread lastpost_date, lastposter_username, lastposter_id must be checked and updated if needed
			// TODO Thread replies counter must be updated
			// TODO User(s) posts counter must be updated

		}
	}
}

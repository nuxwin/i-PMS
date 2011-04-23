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
     * List post
     *
     * @return void
     */
    public function indexAction()
    {
	    $this->view->addScriptPath(THEME_PATH . '/default/templates/partials');
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

        $postModel = new Blog_Model_DbTable_Posts();
        // Todo get max post per pages from user settings (currently hardcoded to 15)
        $pageablePosts = $postModel->getPageablePostsList($request->getParam('page'));
        $this->view->assign('paginator', $pageablePosts);
    }

    /**
     * Show a specific post
     *
     * @return void
     */
    public function showAction()
    {
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

        $pid = intval($request->getParam('pid'));

        $postModel = new Blog_Model_DbTable_Posts();
        $post = $postModel->fetchRow($postModel->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false)
            ->where('posts.pid = ?', $pid)
            ->joinLeft('users', '`users`.`uid` = `posts`.`uid`', array('username', 'firstname', 'lastname'))
        );

        if (!$post) {
            throw new Zend_Controller_Action_Exception('Post not found!', 404);
        }

        $this->view->assign('post', $post->toArray());
    }

    /**
     * Create a new post
     *
     * @return void
     */
    public function addAction()
    {
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

        $form = new Blog_Form_Post();

	    $identity = Zend_Auth::getInstance();
	    if($identity->hasIdentity()) {
            $userId = Zend_Auth::getInstance()->getIdentity()->id;
	    } else {
		    $userId = 0; // Unregistered user
	    }

        if ($request->isPost() && $form->isValid($request->getPost())) {
            $postData = $form->getValues('postForm');
            $postData['uid'] = $userId;

            $postModel = new Blog_Model_DbTable_Posts();
            $pid = $postModel->insert($postData);

	        $this->_redirect($this->urlHelper->url(array('pid' => $pid), 'post_show'));
        }

        $this->view->assign('form', $form);
    }

    /**
     * Edit a post
     *
     * @return void
     */
    public function editAction()
    {
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

        $pid = intval($request->getParam('pid'));
        $postModel = new Blog_Model_DbTable_Posts();

	    $post = $postModel->find($pid)->current();

        if (!$post) {
            throw new Zend_Controller_Action_Exception('Post was not found!', 404);
        }

        $form = new Blog_Form_Post();

        if ($request->isPost() && $form->isValid($request->getPost('postForm'))) {
	        $post->setFromArray($form->getValues(true))->save();
	        $this->_redirect($this->urlHelper->url(array('pid' => $pid), 'post_show'));
        } else {
	        $form->setDefaults($post->toArray());
        }

	    $form->setAction($this->urlHelper->url(array('pid' => $pid), 'post_edit'));
        $this->view->assign('form', $form);
    }

    /**
     * Deletes a post and its comments
     *
     * @return void
     */
    public function deleteAction()
    {
	    /**
	     * @var $request Zend_Controller_Request_Http
	     */
	    $request = $this->getRequest();

	    $pid = intval($request->getParam('pid'));
        $postModel = new Blog_Model_DbTable_Posts();
        $post = $postModel->find($pid)->current();

		if($post) {
			$post->delete();
		}

		$this->_redirect($this->urlHelper->url(array(), 'posts'));
    }
}

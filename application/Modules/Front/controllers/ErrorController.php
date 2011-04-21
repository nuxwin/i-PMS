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
 * @category	iPMS
 * @copyright	2011 by Laurent Declercq
 * @author		Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link		http://www.i-pms.net i-PMS Home Site
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Error controller
 *
 * @author  Laurent Declercq <l.declercq@nuxwin.com>
 * @version 0.0.1
 */
class ErrorController extends Zend_Controller_Action
{

	/**
	 * Initialize controller
	 *
	 * @return void
	 */
	public function init()
	{
		$this->_helper->layout()
			->setLayoutPath(THEME_PATH . '/default/templates/layouts') // Ensure we have correct layout path
			->setLayout('error');
	}

	/**
	 * Show error page
	 *
	 * @return void
	 */
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');

		if (!$errors || !$errors instanceof ArrayObject) {
			$this->view->message = 'You have reached the error page';
			return;
		}

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$priority = Zend_Log::NOTICE;
				//$this->view->message = 'Page not found';
				$this->view->message = "The resource you are looking for (or one of its dependencies) " .
									   "could have been removed, had its name changed, or is temporarily unavailable." .
				                       " Please review the following URL and make sure that it is spelled correctly: " .
										$_SERVER['SERVER_NAME'] . $errors->request->getRequestUri();
				;
				break;
			default:
				// application error
				$this->getResponse()->setHttpResponseCode(500);
				$priority = Zend_Log::CRIT;
				$this->view->message = 'Application error';
				break;
		}

		// Log exception, if logger available
		if ($log = $this->getLog()) {
			$log->log($this->view->message, $priority, $errors->exception);
			$log->log('Request Parameters', $priority, $errors->request->getParams());
		}

		// conditionally display exceptions
		if ($this->getInvokeArg('displayExceptions') == true) {
			$this->view->exception = $errors->exception;
		}

		$errors->request->setParam('layoutFullContent', htmlentities($errors->request->getParam('layoutFullContent')));
		$this->view->request = $errors->request;
	}

	/**
	 * Returns Zend_Log instance if one was sets
	 *
	 * @return mixed instance of Zend_Log or false if no one was sets
	 */
	public function getLog()
	{
		$bootstrap = $this->getInvokeArg('bootstrap');
		if (!$bootstrap->hasResource('Log')) {
			return false;
		}
		$log = $bootstrap->getResource('Log');
		return $log;
	}

	/**
	 *
	 * @return void
	 */
	public function denyAction()
	{
		$this->getResponse()->setHttpResponseCode(403);
	}

	/**
	 * Action for 404 error
	 *
	 * @throws Zend_Controller_Action_Exception
	 * @return void
	 */
	public function notFoundAction()
	{
		throw new Zend_Controller_Action_Exception('Page not found!', '404');
	}
}

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
 * Plugin to initialize some resources after that routing was finished (application-wide)
 *
 * @author  Laurent Declercq
 * @version 0.0.1
 * @todo Move this in a plugin resource (view)
 */
class iPMS_Controller_Plugin_Init extends Zend_Controller_Plugin_Abstract
{

	/**
	 * Default layout path
	 *
	 * @var string
	 */
	protected $defaultLayoutPath = '/default/templates/layouts';

	/**
	 * Do some initialization tasks
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		$module = strtolower($request->getModuleName());

		switch ($module) {
			case 'dashboard':
				$layoutPath = APPLICATION_PATH . '/modules/Dashboard/layouts';
				break;
			default:
				$layoutPath = THEME_PATH . $this->defaultLayoutPath;
		}

		/**
		 * @var $bootstrap Bootstrap
		 */
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');

		/**
		 * @var $layout Zend_Layout
		 */
		$layout = $bootstrap->getResource('Layout');
		$layout->setLayoutPath($layoutPath);

		/**
		 * @var $view Zend_View
		 */
		$view = $bootstrap->getResource('view');

		// Adding common helpers and filters paths
		$view->addHelperPath('iPMS/View/Helper', 'iPMS_View_Helper_');
		$view->addFilterPath('iPMS/View/Filter', 'iPMS_View_Filter_');
	}
}

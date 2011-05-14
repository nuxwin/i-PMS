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
 * @package     iPMS
 * @category    Tools
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

// Error reporting
error_reporting(E_ALL|E_STRICT);

/**
 * Check PHP version (5.3.0 or newer )
 */
if (version_compare(phpversion(), '5.3.3', '<') === true) {
	die('Error: Your PHP version is ' . phpversion() . ". i-PMS requires PHP 5.3.3 or newer.\n");
}

defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../..'));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', ROOT_PATH . '/application');

// Define application environment
defined('APPLICATION_ENV') ||
	define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode( PATH_SEPARATOR, array(ROOT_PATH . '/library', get_include_path())));

require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$app = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

// Init only needed resources
$app->getBootstrap()->bootstrap(array(
                                     'config',  // Setting configuration
                                     'cli',     // Specific initialization for cli mode
                                     'doctrine' // Initialize Doctrine ORM
                                ));

$classLoader = new \Doctrine\Common\ClassLoader('Symfony', 'Doctrine');
$classLoader->register();

require_once 'Zend/Registry.php';

$helperSetToLoad = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper(Zend_Registry::get('d.e.m'))));

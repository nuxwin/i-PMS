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
 * @subpackage  Kernel
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

namespace iPMS\Kernel;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

use iPMS\Kernel\Module\ModuleInterface;

use iPMS\Kernel\Config\FileLocator;
use iPMS\Kernel\DependencyInjection\MergeExtensionConfigurationPass;
use iPMS\Kernel\DependencyInjection\AddClassesToCachePass;
use iPMS\Kernel\DependencyInjection\Extension as DIExtension;

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\ClassLoader\ClassCollectionLoader;

/**
 * The Kernel is the heart of the iPMS application
 *
 * This kernel was builds over the Zend_Application component to allow to mix
 * components that come from the Zend and Symfony frameworks.It add some features
 * such as Dependency Injection, class caching...
 *
 * @package     iPMS
 * @subpackage  Kernel
 * @author      Fabien Potencier <fabien@symfony.com>
 * @modifiedBy  Laurent Declercq <l.declercq@nuxwin.com> for iPMS
 * @version     0.0.1
 */
abstract class Kernel extends \Zend_Application implements KernelInterface
{
	/**
	 * Modules to registers
	 *
	 * @var array
	 */
	protected $_modules;

	/**
	 * Module map
	 *
	 * @var array
	 */
	protected $_moduleMap;

	/**
	 * Application root directory
	 *
	 * @var string
	 */
	protected $_rootDir;

	/**
	 * @var Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $_container;

	/**
	 * Compiled classes
	 *
	 * @var array
	 */
	protected $_classes;

	/**
	 * The kernel name
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Tells whether or not the debug is activated
	 *
	 * @var bool
	 */
	protected $_debug;

	/**
	 * Tells whether or not the application was already booted
	 *
	 * @var boolean
	 */
	protected $_booted;

	/**
	 * Constructor
	 *
	 * Initialize application. Potentially initializes include_paths, PHP settings, and bootstrap class
	 *
	 * @param string $environment
	 * @param string|array|Zend_Config $options String path to configuration file, or array/Zend_Config of
	 * configuration options
	 * @throws Zend_Application_Exception When invalid options are provided
	 * @return void
	 */
	public function __construct($environment, $options = null)
	{
		$this->_debug = ($environment == 'development') ? true : false;
		$this->_rootDir = $this->getRootDir();
		$this->_name = preg_replace('/[^a-zA-Z0-9_]+/', '', basename($this->_rootDir));
		$this->_classes = array();

		parent::__construct($environment, $options);
	}

	/**
	 *
	 */
	public function __clone()
	{
		$this->_booted = false;
		$this->_container = null;
	}

	/**
	 * Bootstrap application
	 *
	 * @param null|string|array $resource
	 * @return Zend_Application
	 */
	public function bootstrap($resource = null)
	{
		// Do nothing if the system is already booted
		if (true === $this->_booted) {
			return;
		}

		// initialize the service container
		if (null === $this->_container) {
			// init modules
			$this->initializeModules();
			$this->initializeContainer();
			$this->getBootstrap()->setContainer($this->_container);
		}

		parent::bootstrap($resource);

		if (null === $resource) {
			$this->_booted = true;
		}

		return $this;
	}

	/**
	 * Loads the PHP class cache.
	 *
	 * @param string $name The cache name prefix
	 * @param string $extension File extension of the resulting file
	 */
	public function loadClassCache($name = 'classes', $extension = '.php')
	{
		if (!$this->_booted) {
			$this->bootstrap();
		}

		if ($this->_classes) {
			ClassCollectionLoader::load($this->_classes, $this->getCacheDir(), $name, $this->_debug, true, $extension);
		}

		return $this;
	}

	/**
	 * Initializes the data structures related to the module management.
	 *
	 *  - the module property maps a module name to the module instance,
	 *  - the moduleMap property maps a module name to the module inheritance hierarchy (most derived module first).
	 *
	 * @throws \LogicException if two modules share a common name
	 * @throws \LogicException if a module tries to extend a non-registered module
	 * @throws \LogicException if a module tries to extend itself
	 * @throws \LogicException if two modules extend the same ancestor
	 */
	protected function initializeModules()
	{
		$this->_modules = $topMostModules = $directChildren = array();

		foreach ($this->registerModules() as $module) {
			$name = $module->getName();

			if (isset($this->_modules[$name])) {
				throw new \LogicException(sprintf('Trying to register two modules with the same name "%s"', $name));
			}

			$this->_modules[$name] = $module;

			if ($parentName = $module->getParent()) {
				if (isset($directChildren[$parentName])) {
					throw new \LogicException(sprintf('Module "%s" is directly extended by two modules "%s" and "%s".', $parentName, $name, $directChildren[$parentName]));
				}

				if ($parentName == $name) {
					throw new \LogicException(sprintf('Module "%s" can not extend itself.', $name));
				}

				$directChildren[$parentName] = $name;
			} else {
				$topMostBundles[$name] = $module;
			}
		}

		// look for orphans
		if (count($diff = array_values(array_diff(array_keys($directChildren), array_keys($this->_modules))))) {
			throw new \LogicException(sprintf('Module "%s" extends module "%s", which is not registered.', $directChildren[$diff[0]], $diff[0]));
		}

		// inheritance
		$this->_moduleMap = array();

		foreach ($topMostModules as $name => $module) {
			$moduleMap = array($module);
			$hierarchy = array($name);

			while (isset($directChildren[$name])) {
				$name = $directChildren[$name];
				array_unshift($moduleMap, $this->_modules[$name]);
				$hierarchy[] = $name;
			}

			foreach ($hierarchy as $module) {
				$this->_moduleMap[$module] = $moduleMap;
				array_pop($moduleMap);
			}
		}
	}

	/**
	 * Initializes the service container
	 *
	 * The cached version of the service container is used when fresh, otherwise the container is built.
	 */
	protected function initializeContainer()
	{
		// Retrieve container class name (applicationDevelopmentDebugProjectContainer)
		$class = $this->getContainerClass();

		// Getting cache instance ('data/cache/applicationDevelopmentDebugProjectContainer.php', true)
		$cache = new ConfigCache($this->getCacheDir() . '/' . $class . '.php', $this->_debug);

		$fresh = true;

		// On vérifie que le cache est toujours valide et si ce n'est pas le cas, on le reconstruit.
		// En mode debug, le cache est toujours reconstruit from scratch
		if (!$cache->isFresh()) {
			// On construit le container
			$container = $this->buildContainer();

			// On dump le container
			$this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

			$fresh = false;
		}

		// On inclu la class du container généré
		require_once $cache;

		$this->_container = new $class();
		$this->_container->set('kernel', $this);

		// TODO cache warmer
		//if (!$fresh && 'cli' !== php_sapi_name()) {
		//    $this->_container->get('cache_warmer')->warmUp($this->_container->getParameter('application.cache_dir'));
		//}
	}

	/**
	 * Gets the container class
	 *
	 * @return string The container class
	 */
	protected function getContainerClass()
	{
		return $this->_name . ucfirst($this->_environment) . ($this->_debug ? 'Debug' : '') . 'ProjectContainer';
	}

	/**
	 * Gets the container's base class.
	 *
	 * All names except Container must be fully qualified.
	 *
	 * @return string
	 */
	protected function getContainerBaseClass()
	{
		return 'Container';
	}

	/**
	 * Builds the service container.
	 *
	 * @return ContainerBuilder The compiled service container
	 */
	protected function buildContainer()
	{
		$container = new ContainerBuilder(new ParameterBag($this->getKernelParameters()));

		$extensions = array();

		foreach ($this->_modules as $module) {
			$module->build($container);

			if ($extension = $module->getContainerExtension()) {
				$container->registerExtension($extension);
				$extensions[] = $extension->getAlias();
			}

			if ($this->_debug) {
				$container->addObjectResource($module);
			}
		}

		$container->addObjectResource($this);

		// Ensure these extensions are implicitly loaded
		$container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));

		if (null !== $cont = $this->registerContainerConfiguration($this->getContainerLoader($container))) {
			$container->merge($cont);
		}

		// Creating both cache and logs directories if needed
		foreach (array('cache', 'logs') as $name) {
			$dir = $container->getParameter(sprintf('kernel.%s_dir', $name));
			if (!is_dir($dir)) {
				if (false === @mkdir($dir, 0777, true)) {
					exit(sprintf("Unable to create the %s directory (%s)\n", $name, dirname($dir)));
				}
			} elseif (!is_writable($dir)) {
				exit(sprintf("Unable to write in the %s directory (%s)\n", $name, $dir));
			}
		}

		$container->addCompilerPass(new AddClassesToCachePass($this));
		$container->compile();

		$this->addClassesToCache($container->getParameter('kernel.compiled_classes'));

		return $container;
	}

	/**
	 * Returns a loader for the container
	 *
	 * @param ContainerInterface $container The service container
	 *
	 * @return DelegatingLoader The loader
	 */
	protected function getContainerLoader(ContainerInterface $container)
	{
		$locator = new FileLocator($this);
		$resolver = new LoaderResolver(array(
		                                    new XmlFileLoader($container, $locator),
		                                    new YamlFileLoader($container, $locator),
		                                    new IniFileLoader($container, $locator),
		                                    new PhpFileLoader($container, $locator),
		                                    new ClosureLoader($container),
		                               ));

		return new DelegatingLoader($resolver);
	}

	/**
	 * Returns the kernel parameters.
	 *
	 * @return array An array of kernel parameters
	 */
	protected function getKernelParameters()
	{
		$modules = array();
		foreach ($this->_modules as $name => $module) {
			$modules[$name] = get_class($module);
		}

		return array_merge(
			array(
			     'kernel.root_dir' => $this->_rootDir,
			     'kernel.environment' => $this->_environment,
			     'kernel.debug' => $this->_debug,
			     'kernel.name' => $this->_name,
			     'kernel.cache_dir' => $this->getCacheDir(),
			     'kernel.logs_dir' => $this->getLogDir(),
			     'kernel.modules' => $modules,
			     'kernel.charset' => 'UTF-8',
			     'kernel.container_class' => $this->getContainerClass(),
			),
			$this->getEnvParameters()
		);
	}

	/**
	 * Dumps the service container to PHP code in the cache
	 *
	 * @param ConfigCache $cache The config cache
	 * @param ContainerBuilder $container The service container
	 * @param string $class The name of the class to generate
	 * @param string $baseClass The name of the container's base class
	 */
	protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
	{
		// cache the container
		$dumper = new PhpDumper($container);
		$content = $dumper->dump(array('class' => $class, 'base_class' => $baseClass));
		if (!$this->_debug) {
			$content = self::stripComments($content);
		}

		$cache->write($content, $container->getResources());
	}

	/**
	 * Removes comments from a PHP source string.
	 *
	 * We don't use the PHP php_strip_whitespace() function
	 * as we want the content to be readable and well-formatted.
	 *
	 * @param string $source A PHP string
	 *
	 * @return string The PHP string with the comments removed
	 */
	static public function stripComments($source)
	{
		if (!function_exists('token_get_all')) {
			return $source;
		}

		$output = '';
		foreach (token_get_all($source) as $token) {
			if (is_string($token)) {
				$output .= $token;
			} elseif (!in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
				$output .= $token[1];
			}
		}

		// replace multiple new lines with a single newline
		$output = preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $output);

		return $output;
	}

	/**
	 * Gets the environment parameters
	 *
	 * Only the parameters starting with "iPMS__" are considered.
	 *
	 * @return array An array of parameters
	 */
	protected function getEnvParameters()
	{
		$parameters = array();
		foreach ($_SERVER as $key => $value) {
			if ('iPMS__' === substr($key, 0, 9)) {
				$parameters[strtolower(str_replace('__', '.', substr($key, 9)))] = $value;
			}
		}

		return $parameters;
	}

	/**
	 * Adds classes to cache
	 *
	 * @param array $classes
	 */
	public function addClassesToCache(array $classes)
	{
		$this->_classes = array_unique(array_merge($this->_classes, $classes));
	}

	/**
	 * Shutdowns the kernel
	 *
	 * This method is mainly useful when doing functional testing.
	 */
	public function shutdown()
	{
		$this->_booted = false;

		foreach ($this->getModules() as $module) {
			$module->shutdown();
			$module->setContainer(null);
		}

		$this->_container = null;
	}

	/**
	 * Gets the registered module instances.
	 *
	 * @return array An array of registered module instances
	 */
	public function getModules()
	{
		return $this->_modules;
	}

	/**
	 * Checks if a given class name belongs to an active module
	 *
	 * @param string $class A class name
	 *
	 * @return Boolean true if the class belongs to an active module, false otherwise
	 */
	public function isClassInActiveModule($class)
	{
		foreach ($this->getModules() as $module) {
			if (0 === strpos($class, $module->getNamespace())) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns a module and optionally its descendants by its name.
	 *
	 * @param string  $name Module name
	 * @param Boolean $first Whether to return the first module only or together with its descendants
	 * @return ModuleInterface|Array A ModuleInterface instance or an array of ModuleInterface instances if $first is false
	 * @throws \InvalidArgumentException when the bundle is not enabled
	 */
	public function getModule($name, $first = true)
	{
		if (!isset($this->_moduleMap[$name])) {
			throw new \InvalidArgumentException(sprintf(
				'Module "%s" does not exist or it is not enabled. Maybe you forgot to add it in the registerModules() function of your %s.php file?', $name, get_class($this)));
		}

		if (true === $first) {
			return $this->_moduleMap[$name][0];
		}

		return $this->_moduleMap[$name];
	}

	/**
	 * Returns the file path for a given resource
	 *
	 * A Resource can be a file or a directory.
	 *
	 * The resource name must follow the following pattern:
	 *
	 *     @<ModuleName>/path/to/a/file.something
	 *
	 * where ModuleName is the name of the module
	 * and the remaining part is the relative path in the module.
	 *
	 * If $dir is passed, and the first segment of the path is "Resources",
	 * this method will look for a file named:
	 *
	 *     $dir/<ModuleName>/path/without/Resources
	 *
	 * before looking in the module resource folder.
	 *
	 * @param string $name A resource name to locate
	 * @param string $dir A directory where to look for the resource first
	 * @param Boolean$first Whether to return the first path or paths for all matching bundles
	 * @return string|array The absolute path of the resource or an array if $first is false
	 * @throws \InvalidArgumentException if the file cannot be found or the name is not valid
	 * @throws \RuntimeException if the name contains invalid/unsafe
	 * @throws \RuntimeException if a custom resource is hidden by a resource in a derived module
	 */
	public function locateResource($name, $dir = null, $first = true)
	{
		if ('@' !== $name[0]) {
			throw new \InvalidArgumentException(sprintf('A resource name must start with @ ("%s" given).', $name));
		}

		if (false !== strpos($name, '..')) {
			throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $name));
		}

		$name = substr($name, 1);
		list($moduleName, $path) = explode('/', $name, 2);

		$isResource = 0 === strpos($path, 'Resources') && null !== $dir;
		$overridePath = substr($path, 9);
		$resourceModule = null;
		$modules = $this->getModules($moduleName, false);
		$files = array();

		foreach ($modules as $module) {
			if ($isResource && file_exists($file = $dir . '/' . $module->getName() . $overridePath)) {
				if (null !== $resourceModule) {
					throw new \RuntimeException(sprintf('"%s" resource is hidden by a resource from the "%s" derived module. Create a "%s" file to override the module resource.',
					                                    $file,
					                                    $resourceModule,
					                                    $dir . '/' . $modules[0]->getName() . $overridePath
					));
				}

				if ($first) {
					return $file;
				}
				$files[] = $file;
			}

			if (file_exists($file = $module->getPath() . '/' . $path)) {
				if ($first && !$isResource) {
					return $file;
				}
				$files[] = $file;
				$resourceModule = $module->getName();
			}
		}

		if (count($files) > 0) {
			return $first && $isResource ? $files[0] : $files;
		}

		throw new \InvalidArgumentException(sprintf('Unable to find file "@%s".', $name));
	}

	/**
	 * Gets the name of the kernel
	 *
	 * @return string The kernel name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Checks if debug mode is enabled.
	 *
	 * @return Boolean true if debug mode is enabled, false otherwise
	 */
	public function isDebug()
	{
		return $this->_debug;
	}

	/**
	 * Gets the application root dir
	 *
	 * @return string The application root dir
	 */
	public function getRootDir()
	{
		if (null === $this->_rootDir) {
			$r = new \ReflectionObject($this);
			$this->_rootDir = dirname($r->getFileName());
		}

		return $this->_rootDir;
	}

	/**
	 * Gets the current container.
	 *
	 * @return ContainerInterface A ContainerInterface instance
	 */
	public function getContainer()
	{
		return $this->_container;
	}

	/**
	 * Gets the cache directory
	 *
	 * @return string The cache directory
	 */
	public function getCacheDir()
	{
		return $this->_rootDir . '/../data/cache/' . $this->_environment;
	}

	/**
	 * Gets the log directory
	 *
	 * @return string The log directory
	 */
	public function getLogDir()
	{
		return $this->_rootDir . '/../data/logs';
	}

	/**
	 * Implements Serialisable interface
	 *
	 * @return string serialized data
	 */
	public function serialize()
	{
		return serialize(array($this->_environment, $this->_debug));
	}

	/**
	 * Implements Serialisable interface
	 *
	 * @param  $data data to unserialize
	 * @return void
	 */
	public function unserialize($data)
	{
		list($environment, $debug) = unserialize($data);

		$this->__construct($environment, $debug);
	}
}

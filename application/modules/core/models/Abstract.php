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
 * @subpackage  Core
 * @category    Model
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Base class for persistable doctrine models
 *
 * @package     iPMS
 * @subpackage  Core
 * @category    Model
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 */
abstract class Core_Model_Abstract
{
	/**
	 * Provides access to change class properties - Looks for function set{$name} first
	 *
	 * @param  $name
	 * @param  $value
	 * @return Core_Model_Abstract provide fluent interface, returns self
	 */
	public function __set($name, $value)
	{
		$method = 'set' . ucfirst($name);

		if (method_exists($this, $method)) {
			return $this->{$method}($value);
		} else {
			$this->{$name} = $value;
		}

		return $this;
	}

	/**
	 * Provides access to properties via getter and setter methods even if they don't exist
	 *
	 * @throws Core_Model_Exception
	 * @param  $name getter / setter methods name
	 * @param  $value value argument for setter methods
	 * @return Core_Model_Abstract|mixed
	 */
	public function __call($name, $value)
	{
		$var = lcfirst(substr($name, 3));

		if (property_exists($this, $var)) {
			if (substr($name, 0, 3) == 'get') {
				return $this->__get($var);
			} else if (substr($name, 0, 3) == 'set') {
				return $this->__set($var, $value);
			}
		}

		throw new Core_Model_Exception(sprintf('Method `%s` does not exist on %s.', $name, get_class($this)));
	}

	/**
	 * Sets all data in the model from an array
	 *
	 * @param  array $data
	 * @return Core_Model_Abstract Provides fluent interface, returns self
	 */
	public function setFromArray(array $data)
	{
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->__set($key, $value);
			}
		}
	}

	/**
	 * Disable setter for identifier
	 *
	 * @throws Core_Model_Exception
	 * @return void
	 */
	public function setId()
	{
		throw new Core_Model_Exception('Id is not allowed to be set.');
	}

	/**
	 * Allows isset() on properties access by magic getters.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		if (property_exists($this, $name)) {
			return true;
		}
		return false;
	}
}

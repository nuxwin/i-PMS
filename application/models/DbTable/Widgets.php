<?php

class Model_DbTable_Widgets extends Zend_Db_Table_Abstract
{
	/**
	 * Database table to operate
	 *
	 * @var string
	 */
    protected $_name = 'widgets';

	/**
	 * Primary key
	 *
	 * @var string
	 */
	protected $_primary = 'id';
}

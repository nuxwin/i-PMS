<?php

/**
 * 
 */
class Model_Token extends Zend_Db_Table_Abstract
{

    /**
     * Database table to operate
     *
     * @var string
     */
    protected $_name = 'tokens';
    /**
     * Primary key
     *
     * @var string
     */
    protected $_primary = 'user_id';
    /**
     * Table relations
     *
     * @var array
     */
    protected $_referenceMap = array(
	'User' => array(
	    SELF::COLUMNS => 'author_id',
	    SELF::REF_TABLE_CLASS => 'Model_DbTable_Users',
	    SELF::REF_COLUMNS => 'id',
	    SELF::ON_DELETE => SELF::CASCADE
	)
    );
    protected static $validityTime = '1 day';

    /**
     * Creates a new Token object and saves it to the database, if validations pass.
     *
     * The resulting object is returned whether the object was saved successfully to the database or not.
     *
     * @param array $attributes
     * @return Model_Token
     */
    public function create(array $attributes = array())
    {

	self::deletePreviousTokens();

	$object = new self();

	$object->createRow($attributes)->save();

	return $object;

	$this->value = $this->generateTokenValue();
    }

    /**
     * Return true if token has expired
     *
     * @return void
     */
    public function isExpired()
    {
	
    }

    /**
     * Delete all expired tokens
     * 
     * @return void
     */
    public function destroyExpired()
    {

    }

    /**
     * Generate new token value
     *
     * @return void
     */
    private function generateTokenValue()
    {
	
    }

    /**
     * Removes obsolete tokens (same user and action)
     * 
     * @static
     * @return void
     */
    static private function deletePreviousTokens()
    {

    }

    /**
     * Generate CSRF token
     *
     * Generates CSRF token and stores both in {@link $_hash} and element
     * value.
     *
     * @return void
     */
    protected function _generateHash()
    {
	return md5(mt_rand(1, 1000000) . $this->getSalt() . $this->getName() . mt_rand(1, 1000000));
    }

}


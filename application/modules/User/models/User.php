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
 * @subpackage  User
 * @category    Models
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * User_Model_User
 *
 * @package     iPMS
 * @subpackage  User
 * @category    Models
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @Table(name="users")
 * @Entity
 */
class User_Model_User extends Core_Model_Abstract
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $username
     *
     * @Column(name="username", type="string", length=32, precision=0, scale=0, nullable=false, unique=false)
     */
    private $username = 'Unregistered User';

    /**
     * @var string $password
     *
     * @Column(name="password", type="string", length=32, precision=0, scale=0, nullable=false, unique=false)
     */
    private $password;

    /**
     * @var string $role
     *
     * @Column(name="role", type="string", length=15, precision=0, scale=0, nullable=false, unique=false)
     */
    private $role = 'guest';

    /**
     * @var boolean $isActive
     *
     * @Column(name="is_active", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $isActive;

    /**
     * @var datetime $lastLoginOn
     *
     * @Column(name="last_login_on", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $lastLoginOn;

    /**
     * @var string $firstname
     *
     * @Column(name="firstname", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $firstname;

    /**
     * @var string $lastname
     *
     * @Column(name="lastname", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $lastname;

    /**
     * @var string $email
     *
     * @Column(name="email", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $email;

    /**
     * @var string $avatar
     *
     * @Column(name="avatar", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $avatar;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

	/**
	 * Get nicename
	 *
	 * @return string
	 */
	public function getNicename()
	{
		if(null !== $this->firstname && null !== $this->lastname) {
			return sprintf('%s %s', $this->firstname, $this->lastname);

		}
		return $this->username;
	}

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set role
     *
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Get role
     *
     * @return string $role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Get isActive
     *
     * @return boolean $isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set lastLoginOn
     *
     * @param datetime $lastLoginOn
     */
    public function setLastLoginOn($lastLoginOn)
    {
        $this->lastLoginOn = $lastLoginOn;
    }

    /**
     * Get lastLoginOn
     *
     * @return datetime $lastLoginOn
     */
    public function getLastLoginOn()
    {
        return $this->lastLoginOn;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string $firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string $lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * Get avatar
     *
     * @return string $avatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}

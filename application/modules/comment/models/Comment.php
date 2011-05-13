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
 * @subpackage  Comment
 * @category    Model
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Comment_Model_Comment
 *
 * @Table(name="comments")
 * @Entity
 */
class Comment_Model_Comment
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
     * @var text $content
     *
     * @Column(name="content", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    private $content;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=120, precision=0, scale=0, nullable=false, unique=false)
     */
    private $name;

    /**
     * @var string $email
     *
     * @Column(name="email", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $email;

    /**
     * @var string $website
     *
     * @Column(name="website", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $website;

    /**
     * @var datetime $createdOn
     *
     * @Column(name="created_on", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $createdOn;

    /**
     * @var Blog_Model_Post
     *
     * @ManyToOne(targetEntity="Blog_Model_Post")
     * @JoinColumns({
     *   @JoinColumn(name="post_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $post;

    /**
     * @var User_Model_User
     *
     * @ManyToOne(targetEntity="User_Model_User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $user;


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
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
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
     * Set website
     *
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * Get website
     *
     * @return string $website
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set createdOn
     *
     * @param datetime $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * Get createdOn
     *
     * @return datetime $createdOn
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set post
     *
     * @param Blog_Model_Post $post
     */
    public function setPost(\Blog_Model_Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get post
     *
     * @return Blog_Model_Post $post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param User_Model_User $user
     */
    public function setUser(\User_Model_User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return User_Model_User $user
     */
    public function getUser()
    {
        return $this->user;
    }
}

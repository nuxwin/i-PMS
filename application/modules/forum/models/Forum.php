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
 * @subpackage  Forum
 * @category    Model
 * @copyright   2011 by Laurent Declercq
 * @author      Laurent Declercq <laurent.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Forums
 *
 * @Table(name="forums")
 * @Entity
 */
class Forum_Model_Forum
{
    /**
     * @var smallint $id
     *
     * @Column(name="id", type="smallint", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=120, nullable=false)
     */
    private $name;

    /**
     * @var string $description
     *
     * @Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var smallint $order
     *
     * @Column(name="`order`", type="smallint", nullable=true)
     */
    private $order;

    /**
     * @var bigint $countThreads
     *
     * @Column(name="count_threads", type="bigint", nullable=false)
     */
    private $countThreads;

    /**
     * @var bigint $countPosts
     *
     * @Column(name="count_posts", type="bigint", nullable=false)
     */
    private $countPosts;

    /**
     * @var integer $lastPostDate
     *
     * @Column(name="last_post_date", type="integer", nullable=true)
     */
    private $lastPostDate;

    /**
     * @var User_Model_User
     *
     * @ManyToOne(targetEntity="User_Model_User")
     * @JoinColumn(name="last_poster_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $lastPosterId;

    /**
     * @var Forum_Model_Fthread
     *
     * @ManyToOne(targetEntity="Forum_Model_Fthread")
     * @JoinColumn(name="last_thread_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $lastThreadId;

    /**
     * @var string $lastThreadSubject
     *
     * @Column(name="last_thread_subject", type="string", length=120, nullable=true)
     */
    private $lastThreadSubject;

    /**
     * Get id
     *
     * @return smallint $id
     */
    public function getId()
    {
        return $this->id;
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
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set order
     *
     * @param smallint $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return smallint $order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set countThreads
     *
     * @param bigint $countThreads
     */
    public function setCountThreads($countThreads)
    {
        $this->countThreads = $countThreads;
    }

    /**
     * Get countThreads
     *
     * @return bigint $countThreads
     */
    public function getCountThreads()
    {
        return $this->countThreads;
    }

    /**
     * Set countPosts
     *
     * @param bigint $countPosts
     */
    public function setCountPosts($countPosts)
    {
        $this->countPosts = $countPosts;
    }

    /**
     * Get countPosts
     *
     * @return bigint $countPosts
     */
    public function getCountPosts()
    {
        return $this->countPosts;
    }

    /**
     * Set lastPostDate
     *
     * @param integer $lastPostDate
     */
    public function setLastPostDate($lastPostDate)
    {
        $this->lastPostDate = $lastPostDate;
    }

    /**
     * Get lastPostDate
     *
     * @return integer $lastPostDate
     */
    public function getLastPostDate()
    {
        return $this->lastPostDate;
    }

    /**
     * Set lastThreadSubject
     *
     * @param string $lastThreadSubject
     */
    public function setLastThreadSubject($lastThreadSubject)
    {
        $this->lastThreadSubject = $lastThreadSubject;
    }

    /**
     * Get lastThreadSubject
     *
     * @return string $lastThreadSubject
     */
    public function getLastThreadSubject()
    {
        return $this->lastThreadSubject;
    }

    /**
     * Set lastPosterId
     *
     * @param User_Model_User $lastPosterId
     */
    public function setLastPosterId(\User_Model_User $lastPosterId)
    {
        $this->lastPosterId = $lastPosterId;
    }

    /**
     * Get lastPosterId
     *
     * @return User_Model_User $lastPosterId
     */
    public function getLastPosterId()
    {
        return $this->lastPosterId;
    }

    /**
     * Set lastThreadId
     *
     * @param Forum_Model_Fthread $lastThreadId
     */
    public function setLastThreadId(\Forum_Model_Fthread $lastThreadId)
    {
        $this->lastThreadId = $lastThreadId;
    }

    /**
     * Get lastThreadId
     *
     * @return Forum_Model_Fthread $lastThreadId
     */
    public function getLastThreadId()
    {
        return $this->lastThreadId;
    }
}

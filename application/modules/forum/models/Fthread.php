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
 * @category    Models
 * @copyright   2011 by Laurent Declercq (nuxwin)
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @link        http://www.i-pms.net i-PMS Home Site
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * Forum_Model_Fthread
 *
 * @package     iPMS
 * @subpackage  Forum
 * @category    Models
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     0.0.1
 * @Table(name="fthreads")
 * @Entity
 */
class Forum_Model_Fthread
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
     * @var string $subject
     *
     * @Column(name="subject", type="string", length=130, precision=0, scale=0, nullable=false, unique=false)
     */
    private $subject;

    /**
     * @var datetime $createdOn
     *
     * @Column(name="created_on", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $createdOn;

    /**
     * @var datetime $lastPostDate
     *
     * @Column(name="last_post_date", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $lastPostDate;

    /**
     * @var bigint $countViews
     *
     * @Column(name="count_views", type="bigint", precision=0, scale=0, nullable=true, unique=false)
     */
    private $countViews;

    /**
     * @var bigint $countReplies
     *
     * @Column(name="count_replies", type="bigint", precision=0, scale=0, nullable=true, unique=false)
     */
    private $countReplies;

    /**
     * @var boolean $isClosed
     *
     * @Column(name="is_closed", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $isClosed;

    /**
     * @var boolean $isSticky
     *
     * @Column(name="is_sticky", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $isSticky;

    /**
     * @var Forum_Model_Fpost
     *
     * @ManyToOne(targetEntity="Forum_Model_Fpost")
     * @JoinColumns({
     *   @JoinColumn(name="first_post_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $firstPost;

    /**
     * @var User_Model_User
     *
     * @ManyToOne(targetEntity="User_Model_User")
     * @JoinColumns({
     *   @JoinColumn(name="last_poster_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $lastPoster;

    /**
     * @var Forum_Model_Forum
     *
     * @ManyToOne(targetEntity="Forum_Model_Forum")
     * @JoinColumns({
     *   @JoinColumn(name="forum_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $forum;

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
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
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
     * Set lastPostDate
     *
     * @param datetime $lastPostDate
     */
    public function setLastPostDate($lastPostDate)
    {
        $this->lastPostDate = $lastPostDate;
    }

    /**
     * Get lastPostDate
     *
     * @return datetime $lastPostDate
     */
    public function getLastPostDate()
    {
        return $this->lastPostDate;
    }

    /**
     * Set countViews
     *
     * @param bigint $countViews
     */
    public function setCountViews($countViews)
    {
        $this->countViews = $countViews;
    }

    /**
     * Get countViews
     *
     * @return bigint $countViews
     */
    public function getCountViews()
    {
        return $this->countViews;
    }

    /**
     * Set countReplies
     *
     * @param bigint $countReplies
     */
    public function setCountReplies($countReplies)
    {
        $this->countReplies = $countReplies;
    }

    /**
     * Get countReplies
     *
     * @return bigint $countReplies
     */
    public function getCountReplies()
    {
        return $this->countReplies;
    }

    /**
     * Set isClosed
     *
     * @param boolean $isClosed
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;
    }

    /**
     * Get isClosed
     *
     * @return boolean $isClosed
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * Set isSticky
     *
     * @param boolean $isSticky
     */
    public function setIsSticky($isSticky)
    {
        $this->isSticky = $isSticky;
    }

    /**
     * Get isSticky
     *
     * @return boolean $isSticky
     */
    public function getIsSticky()
    {
        return $this->isSticky;
    }

    /**
     * Set firstPost
     *
     * @param Forum_Model_Fpost $firstPost
     */
    public function setFirstPost(\Forum_Model_Fpost $firstPost)
    {
        $this->firstPost = $firstPost;
    }

    /**
     * Get firstPost
     *
     * @return Forum_Model_Fpost $firstPost
     */
    public function getFirstPost()
    {
        return $this->firstPost;
    }

    /**
     * Set lastPoster
     *
     * @param User_Model_User $lastPoster
     */
    public function setLastPoster(\User_Model_User $lastPoster)
    {
        $this->lastPoster = $lastPoster;
    }

    /**
     * Get lastPoster
     *
     * @return User_Model_User $lastPoster
     */
    public function getLastPoster()
    {
        return $this->lastPoster;
    }

    /**
     * Set forum
     *
     * @param Forum_Model_Forum $forum
     */
    public function setForum(\Forum_Model_Forum $forum)
    {
        $this->forum = $forum;
    }

    /**
     * Get forum
     *
     * @return Forum_Model_Forum $forum
     */
    public function getForum()
    {
        return $this->forum;
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

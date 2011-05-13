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
 * Forum_Model_Fpost
 *
 * @Table(name="fposts")
 * @Entity
 */
class Forum_Model_Fpost
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
     * @var integer $replyTo
     *
     * @Column(name="reply_to", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $replyTo;

    /**
     * @var string $subject
     *
     * @Column(name="subject", type="string", length=120, precision=0, scale=0, nullable=false, unique=false)
     */
    private $subject;

    /**
     * @var datetime $createdOn
     *
     * @Column(name="created_on", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $createdOn;

    /**
     * @var text $message
     *
     * @Column(name="message", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    private $message;

    /**
     * @var string $postHash
     *
     * @Column(name="post_hash", type="string", length=32, precision=0, scale=0, nullable=false, unique=false)
     */
    private $postHash;

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
     * @var Forum_Model_Forum
     *
     * @ManyToOne(targetEntity="Forum_Model_Forum")
     * @JoinColumns({
     *   @JoinColumn(name="forum_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $forum;

    /**
     * @var Forum_Model_Fthread
     *
     * @ManyToOne(targetEntity="Forum_Model_Fthread")
     * @JoinColumns({
     *   @JoinColumn(name="thread_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $thread;


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
     * Set replyTo
     *
     * @param integer $replyTo
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
    }

    /**
     * Get replyTo
     *
     * @return integer $replyTo
     */
    public function getReplyTo()
    {
        return $this->replyTo;
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
     * Set message
     *
     * @param text $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return text $message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set postHash
     *
     * @param string $postHash
     */
    public function setPostHash($postHash)
    {
        $this->postHash = $postHash;
    }

    /**
     * Get postHash
     *
     * @return string $postHash
     */
    public function getPostHash()
    {
        return $this->postHash;
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
     * Set thread
     *
     * @param Forum_Model_Fthread $thread
     */
    public function setThread(\Forum_Model_Fthread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Get thread
     *
     * @return Forum_Model_Fthread $thread
     */
    public function getThread()
    {
        return $this->thread;
    }
}

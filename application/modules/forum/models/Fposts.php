<?php



/**
 * Fposts
 *
 * @Table(name="fposts")
 * @Entity
 */
class Forum_Model_Fpost
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $replyTo
     *
     * @Column(name="reply_to", type="integer", nullable=false)
     */
    private $replyTo;

    /**
     * @var string $subject
     *
     * @Column(name="subject", type="string", length=120, nullable=false)
     */
    private $subject;

    /**
     * @var integer $createdOn
     *
     * @Column(name="created_on", type="integer", nullable=false)
     */
    private $createdOn;

    /**
     * @var text $message
     *
     * @Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var string $postHash
     *
     * @Column(name="post_hash", type="string", length=32, nullable=false)
     */
    private $postHash;

    /**
     * @var User_Model_User
     *
     * @ManyToOne(targetEntity="User_Model_User")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $user;

    /**
     * @var Forum_Model_Forum
     *
     * @ManyToOne(targetEntity="Forum_Model_Forum")
     * @JoinColumn(name="forum_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $forum;

    /**
     * @var Forum_Model_Fthread
     *
     * @ManyToOne(targetEntity="Forum_Model_Fthread")
     * @JoinColumn(name="thread_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $thread;
}

<?php



/**
 * Fthreads
 *
 * @Table(name="fthreads")
 * @Entity
 */
class Forum_Model_Fthread
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
     * @var string $subject
     *
     * @Column(name="subject", type="string", length=130, nullable=false)
     */
    private $subject;

    /**
     * @var integer $createdOn
     *
     * @Column(name="created_on", type="integer", nullable=false)
     */
    private $createdOn;

    /**
     * @var Forum_Model_Fpost
     *
     * @ManyToOne(targetEntity="Forum_Model_Fpost")
     * @JoinColumn(name="first_post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $firstPostId;

    /**
     * @var integer $lastPostDate
     *
     * @Column(name="last_post_date", type="integer", nullable=false)
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
     * @var bigint $countViews
     *
     * @Column(name="count_views", type="bigint", nullable=true)
     */
    private $countViews;

    /**
     * @var bigint $countReplies
     *
     * @Column(name="count_replies", type="bigint", nullable=true)
     */
    private $countReplies;

    /**
     * @var boolean $isClosed
     *
     * @Column(name="is_closed", type="boolean", nullable=true)
     */
    private $isClosed;

    /**
     * @var boolean $isSticky
     *
     * @Column(name="is_sticky", type="boolean", nullable=true)
     */
    private $isSticky;

    /**
     * @var Forum_Model_Forum
     *
     * @ManyToOne(targetEntity="Forum_Model_Forum")
     * @JoinColumn(name="forum_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $forum;

    /**
     * @var User_Model_User
     *
     * @ManyToOne(targetEntity="User_Model_User")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $user;
}

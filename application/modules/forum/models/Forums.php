<?php



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
}

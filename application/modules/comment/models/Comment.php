<?php



/**
 * Comments
 *
 * @Table(name="comments")
 * @Entity
 */
class Comment_Model_Comment
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
     * @var text $content
     *
     * @Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=120, nullable=false)
     */
    private $name;

    /**
     * @var string $email
     *
     * @Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string $website
     *
     * @Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var integer $createdOn
     *
     * @Column(name="created_on", type="integer", nullable=false)
     */
    private $createdOn;

    /**
     * @var Blog_Model_Post
     *
     * @ManyToOne(targetEntity="Blog_Model_Post")
     * @JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $post;

    /**
     * @var Users
     *
     * @ManyToOne(targetEntity="User_Model_User")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $user;
}

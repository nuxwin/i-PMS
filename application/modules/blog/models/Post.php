<?php



/**
 * Posts
 *
 * @Table(name="posts")
 * @Entity
 */
class Blog_Model_Post
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
     * @var string $title
     *
     * @Column(name="title", type="string", length=120, nullable=false)
     */
    private $title;

    /**
     * @var string $summary
     *
     * @Column(name="summary", type="string", length=255, nullable=false)
     */
    private $summary;

    /**
     * @var text $content
     *
     * @Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var integer $createdOn
     *
     * @Column(name="created_on", type="integer", nullable=false)
     */
    private $createdOn;

    /**
     * @var boolean $allowComments
     *
     * @Column(name="allow_comments", type="boolean", nullable=false)
     */
    private $allowComments;

    /**
     * @var User_Model_User
     *
     * @ManyToOne(targetEntity="User_Model_User")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $user;

	/**
	 * @var
	 * 
	 * @ManyToMany(targetEntity="Categories_Model_Category")
	 * @JoinTable(name="post_categories",
	 *  joinColumns={@JoinColumn(name="post_id", referencedColumnName="id")},
	 *  inverseJoinColumns={@JoinColumn(name="category_id", referencedColumnName="id")}
	 * )
	 */
    private $categories;
}

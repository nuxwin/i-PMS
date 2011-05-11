<?php



/**
 * Users
 *
 * @Table(name="users")
 * @Entity
 */
class User_Model_User
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
     * @var string $username
     *
     * @Column(name="username", type="string", length=32, nullable=false)
     */
    private $username;

    /**
     * @var string $password
     *
     * @Column(name="password", type="string", length=32, nullable=false)
     */
    private $password;

    /**
     * @var string $role
     *
     * @Column(name="role", type="string", length=15, nullable=false)
     */
    private $role;

    /**
     * @var boolean $isActive
     *
     * @Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var integer $lastLoginOn
     *
     * @Column(name="last_login_on", type="integer", nullable=true)
     */
    private $lastLoginOn;

    /**
     * @var string $firstname
     *
     * @Column(name="firstname", type="string", length=50, nullable=true)
     */
    private $firstname;

    /**
     * @var string $lastname
     *
     * @Column(name="lastname", type="string", length=50, nullable=true)
     */
    private $lastname;

    /**
     * @var string $email
     *
     * @Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string $avatar
     *
     * @Column(name="avatar", type="string", length=255, nullable=true)
     */
    private $avatar;
}

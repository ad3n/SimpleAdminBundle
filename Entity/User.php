<?php
namespace Ihsan\SimpleAdminBundle\Entity;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Ihsan\SimpleAdminBundle\Model\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ihsan_simple_admin_user")
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    protected $avatar;

    /**
     * @ORM\Column(name="full_name", type="string", length=77, nullable=true)
     */
    protected $fullName;

    public function __construct()
    {
        parent::__construct();
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getFullName()
    {
        return $this->fullName;
    }
}

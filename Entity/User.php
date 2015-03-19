<?php
namespace Ihsan\SimpleAdminBundle\Entity;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Ihsan\SimpleAdminBundle\Entity\EntityInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ihsan_simple_admin_user")
 */
class User extends BaseUser implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }
}

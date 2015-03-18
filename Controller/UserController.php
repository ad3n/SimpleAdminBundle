<?php
namespace Ihsan\SimpleAdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Ihsan\SimpleAdminBundle\Annotation\PageTitle;
use Ihsan\SimpleAdminBundle\Annotation\PageDescription;
use Ihsan\SimpleAdminBundle\Annotation\GridFields;
use Ihsan\SimpleAdminBundle\Annotation\ShowFields;

/**
 * @Route("/user")
 *
 * @PageTitle("user.page_title")
 * @PageDescription("user.page_description")
 * @GridFields({"username", "fullName", "email", "roles"})
 * @ShowFields({"username", "fullName", "email", "roles"})
 */
class UserController extends CrudController
{
}

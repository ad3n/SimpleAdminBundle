<?php
namespace Ihsan\SimpleAdminBundle\Security;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Ihsan\SimpleAdminBundle\Controller\CrudController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Ihsan\SimpleAdminBundle\Annotation\PageTitle;
use Ihsan\SimpleAdminBundle\Annotation\PageDescription;
use Ihsan\SimpleAdminBundle\Annotation\GridFields;
use Ihsan\SimpleAdminBundle\Annotation\ShowFields;

/**
 * @Route("/user")
 *
 * @PageTitle("page.user.title")
 * @PageDescription("page.user.description")
 */
class UserController extends CrudController
{
}

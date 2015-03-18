<?php
namespace Ihsan\SimpleAdminBundle\Menu;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Routing\Router;

class Builder extends ContainerAware
{
    /**
     * @var Symfony\Component\Routing\RouteCollection
     */
    protected $routeCollection;

    public function __construct(Router $router)
    {
        $this->routeCollection = $router->getRouteCollection();
    }

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'sidebar-menu'
            )
        ));

        $menu->addChild('Home', array(
            'route' => 'home',
            'label' => '<i class="fa fa-dashboard"></i> Dashboard</a>',
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        if ($this->routeCollection->get('ihsan_simpleadmin_security_user_new')) {
            $this->addUserMenu($menu);
        }

        return $menu;
    }

    protected function addUserMenu($menu)
    {
        $menu->addChild('User', array(
            'uri' => '#',
            'label' => '<i class="fa fa-user"></i> User Management<i class="fa fa-angle-double-left pull-right"></i></a>',
            'extras' => array('safe_label' => true),
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        $menu['User']->setChildrenAttribute('class', 'treeview-menu');

        $menu['User']->addChild('Add User', array(
            'route' => 'ihsan_simpleadmin_security_user_new',
            'attributes' => array(
                'class' => 'treeview'
            )
        ));

        $menu['User']->addChild('User List', array(
            'route' => 'ihsan_simpleadmin_security_user_list',
            'attributes' => array(
                'class' => 'treeview'
            )
        ));
    }
}

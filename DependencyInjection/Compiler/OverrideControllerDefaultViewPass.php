<?php
namespace Ihsan\SimpleAdminBundle\DependencyInjection\Compiler;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Ihsan\SimpleAdminBundle\Controller\OverridableTemplateInterface;

final class OverrideControllerDefaultViewPass
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (! is_array($controller)) {

            return;
        }

        $controller = $controller[0];

        if (! $controller instanceof OverridableTemplateInterface) {

            return;
        }

        if (! $controller->allowOverrideTemplate()) {
            return;
        }

        $controller->setNewActionTemplate($this->container->getParameter('ihsan.simple_admin.themes.new_view'));
        $controller->setEditActionTemplate($this->container->getParameter('ihsan.simple_admin.themes.edit_view'));
        $controller->setShowActioinTemplate($this->container->getParameter('ihsan.simple_admin.themes.show_view'));
        $controller->setListActionTemplate($this->container->getParameter('ihsan.simple_admin.themes.list_view'));
    }
}

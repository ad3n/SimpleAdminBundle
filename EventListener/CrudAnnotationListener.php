<?php
namespace Ihsan\SimpleCrudBundle\EventListener;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\Common\Annotations\Reader;

use Ihsan\SimpleCrudBundle\Controller\CrudController;
use Ihsan\SimpleCrudBundle\Annotation\Crud;
use Ihsan\SimpleCrudBundle\Annotation\FormClass;
use Ihsan\SimpleCrudBundle\Annotation\EntityClass;

final class CrudAnnotationListener
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (! is_array($controller)) {

            return;
        }

        $controller = $controller[0];

        if (! $controller instanceof CrudController) {

            return;
        }

        $object = new \ReflectionObject($controller);

        foreach ($this->reader->getClassAnnotations($object) as $annotation) {
            if ($annotation instanceof Crud) {
                $controller->setEntityClass($annotation->entityClass);
                $controller->setFormClass($annotation->formClass);
            }

            if ($annotation instanceof EntityClass) {
                $controller->setEntityClass($annotation->value);
            }

            if ($annotation instanceof FormClass) {
                $controller->setFormClass($annotation->value);
            }
        }
    }
}

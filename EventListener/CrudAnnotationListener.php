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

final class CrudAnnotationListener
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (! is_array($controller = $event->getController())) {

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
            }
        }
    }
}

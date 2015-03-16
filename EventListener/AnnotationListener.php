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
use Ihsan\SimpleCrudBundle\Annotation\HasEventListener;
use Ihsan\SimpleCrudBundle\Annotation\NormalizeFilter;
use Ihsan\SimpleCrudBundle\Annotation\PageDescription;
use Ihsan\SimpleCrudBundle\Annotation\PageTitle;
use Ihsan\SimpleCrudBundle\Annotation\GridFields;
use Ihsan\SimpleCrudBundle\Annotation\ShowFields;

final class AnnotationListener
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
                $controller->setPageTitle($annotation->pageTitle);
                $controller->setPageDescription($annotation->pageDescription);

                if ('true' === strtolower($annotation->hasEventListener)) {
                    $controller->hasEventListener();
                }

                if ('true' === strtolower($annotation->normalizeFilter)) {
                    $controller->normalizeFilter();
                }
            }

            if ($annotation instanceof EntityClass) {
                $controller->setEntityClass($annotation->value);
            }

            if ($annotation instanceof FormClass) {
                $controller->setFormClass($annotation->value);
            }

            if ($annotation instanceof PageTitle) {
                $controller->setPageTitle($annotation->value);
            }

            if ($annotation instanceof PageDescription) {
                $controller->setPageDescription($annotation->value);
            }

            if ($annotation instanceof ShowFields) {
                if ($annotation->isValid()) {//silent is gold
                    $controller->setShowFields($annotation->value);
                }
            }

            if ($annotation instanceof GridFields) {
                if ($annotation->isValid()) {//silent is gold
                    $controller->setGridFields($annotation->value);
                }
            }

            if ($annotation instanceof HasEventListener) {
                $controller->hasEventListener();
            }

            if ($annotation instanceof NormalizeFilter) {
                $controller->normalizeFilter();
            }
        }
    }
}

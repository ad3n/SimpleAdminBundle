<?php
namespace Ihsan\SimpleCrudBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\EventDispatcher\Event;
use Ihsan\SimpleCrudBundle\Controller\CrudController;
use Ihsan\SimpleCrudBundle\Entity\EntityInterface;

class PreShowRenderEvent extends Event
{
    protected $controller;

    protected $entity;

    public function setController(CrudController $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return CrudController
     */
    public function getController()
    {
        return $this->controller;
    }

    /***
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
}

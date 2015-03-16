<?php
namespace Ihsan\SimpleCrudBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\EventDispatcher\Event;
use Ihsan\SimpleCrudBundle\Controller\CrudController;

class PreListRenderEvent extends Event
{
    protected $controller;

    protected $data = array();

    public function setController(CrudController $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return CrudController
     */
    public function getController()
    {
        return $this->controller;
    }

    public function getData()
    {
        return $this->data;
    }
}

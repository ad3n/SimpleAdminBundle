<?php
namespace Ihsan\SimpleAdminBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\EventDispatcher\Event;

class BeforeShowEvent extends Event
{
    protected $viewData;

    public function setViewData(array $viewData)
    {
        $this->viewData = $viewData;

        return $this;
    }

    public function getViewData()
    {
        return $this->viewData;
    }
}

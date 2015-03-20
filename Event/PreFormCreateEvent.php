<?php
namespace Ihsan\SimpleAdminBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Ihsan\SimpleAdminBundle\Model\EntityInterface;
use Symfony\Component\EventDispatcher\Event;

class PreFormCreateEvent extends Event
{
    protected $data;

    public function setFormData(EntityInterface $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return EntityInterface
     */
    public function getFormData()
    {
        return $this->data;
    }
}

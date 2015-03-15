<?php
namespace Ihsan\SimpleCrudBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\EventDispatcher\Event;
use Doctrine\Common\Persistence\ObjectManager;
use Ihsan\SimpleCrudBundle\Entity\EntityInterface;

class PostFlushCrudEvent extends Event
{
    protected $entity;

    protected $entityManager;

    public function setEntityMeneger(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
}

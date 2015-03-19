<?php
namespace Ihsan\SimpleAdminBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\EventDispatcher\Event;
use Doctrine\Common\Persistence\ObjectManager;
use Ihsan\SimpleAdminBundle\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class PreDeleteEvent extends Event
{
    protected $entity;

    protected $entityManager;

    protected $response;

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

    public function setResponse(JsonResponse $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return JsonResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}

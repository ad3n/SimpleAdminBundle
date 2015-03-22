<?php
namespace Ihsan\SimpleAdminBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\HttpFoundation\Response;

class EntityResponseEvent extends EntityEvent
{
    protected $response;

    public function setResponse(Response $response)
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

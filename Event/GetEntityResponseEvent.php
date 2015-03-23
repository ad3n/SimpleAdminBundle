<?php
namespace Ihsan\SimpleAdminBundle\Event;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class GetEntityResponseEvent extends GetEntityEvent
{
    protected $response;

    protected $request;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

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

<?php
namespace Ihsan\SimpleAdminBundle\Controller;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 *
 */

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Ihsan\SimpleAdminBundle\Event\BeforeShowEvent;
use Ihsan\SimpleAdminBundle\IhsanSimpleAdminEvents as Event;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return $this->render($this->container->getParameter('ihsan.simple_admin.themes.dashboard'), array(
            'menu' => $this->container->getParameter('ihsan.simple_admin.menu'),
        ));
    }

    /**
     * @Route("/profile/")
     * @Method({"GET"})
     */
    public function profileAction(Request $request)
    {
        $entity = $this->getUser();

        $data = array();

        foreach ($this->showFields() as $key => $property) {
            $method = 'get'.ucfirst($property);

            if (method_exists($entity, $method)) {
                array_push($data, array(
                    'name' => $property,
                    'value' => call_user_func_array(array($entity, $method), array()),
                ));
            } else {
                $method = 'is'.ucfirst($property);

                if (method_exists($entity, $method)) {
                    array_push($data, array(
                        'name' => $property,
                        'value' => call_user_func_array(array($entity, $method), array()),
                    ));
                }
            }
        }

        $event = new BeforeShowEvent();
        $event->setViewData($data);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(Event::BEFORE_SHOW_EVENT, $event);

        $translator = $this->container->get('translator');
        $translationDomain = $this->container->getParameter('ihsan.simple_admin.translation_domain');

        return $this->render($this->container->getParameter('ihsan.simple_admin.themes.profile'), array(
            'data' => $data,
            'menu' => $this->container->getParameter('ihsan.simple_admin.menu'),
            'page_title' => $translator->trans('page.profile.title', array(), $translationDomain),
            'page_description' => $translator->trans('page.profile.description', array(), $translationDomain),
            'back' => $request->headers->get('referer'),
        ));
    }

    /**
     * @Route("/change_password/")
     * @Method({"GET", "POST"})
     */
    public function changePasswordAction(Request $request)
    {

    }
}
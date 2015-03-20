<?php
namespace Ihsan\SimpleAdminBundle\EventListener;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class DbUtilListener
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(KernelInterface $kernel, ContainerInterface $container, ObjectManager $objectManager)
    {
        $this->kernel = $kernel;
        $this->container = $container;
        $this->objectManager = $objectManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (! in_array($this->kernel->getEnvironment(), array('dev', 'test'))) {
            return ;
        }

        $request = $event->getRequest();
        if (! $request->query->has('ihsan_db_util')) {
            return ;
        }

        $criteria = $request->query->all();
        $entityAlias = $request->query->get('ihsan_db_util');
        $entities = $this->container->getParameter('ihsan.simple_admin.db_util.entities');
        unset($criteria['ihsan_db_util']);
        $translator = $this->container->get('translator');

        try {
            $repository = $this->objectManager->getRepository($entities[$entityAlias]);

            foreach ($criteria as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $entity = $repository->findBy(array($key => $v));
                        $this->objectManager->remove($entity);
                    }
                } else {
                    $entity = $repository->findBy($criteria);
                    $this->objectManager->remove($entity);
                }
            }

            $this->objectManager->flush();

            $response = new Response();
            $response->setContent($translator->trans('message.data_deleted', array('%data%' => json_encode($criteria))));

            $event->setResponse($response);
        } catch (Exception $ex) {
            return ;
        }
    }
}

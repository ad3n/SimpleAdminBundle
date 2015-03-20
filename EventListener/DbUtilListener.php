<?php
namespace Ihsan\SimpleAdminBundle\EventListener;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class DbUtilListener
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (! $request->query->has('ihsan_db_util')) {
            return ;
        }

        $query = $request->query->all();
        $table = $request->query->has('ihsan_db_util');
        unset($query['ihsan_db_util']);

        foreach ($query as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    //Build new criteria
                }
            } else {
                //Build new criteria
            }
        }
    }
}

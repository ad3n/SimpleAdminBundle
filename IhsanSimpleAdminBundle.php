<?php

namespace Ihsan\SimpleAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Ihsan\SimpleAdminBundle\DependencyInjection\Compiler\IsDependenciesPassed;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IhsanSimpleAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new IsDependenciesPassed());
    }
}

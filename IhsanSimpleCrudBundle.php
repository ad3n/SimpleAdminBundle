<?php

namespace Ihsan\SimpleCrudBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Ihsan\SimpleCrudBundle\DependencyInjection\Compiler\IsDependenciesPassed;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IhsanSimpleCrudBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new IsDependenciesPassed());
    }
}

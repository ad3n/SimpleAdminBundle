<?php
namespace Ihsan\SimpleCrudBundle\DependencyInjection\Compiler;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class IsDependenciesPassed implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
       if (! $container->hasParameter('knp_paginator.template.pagination')) {
           throw new InvalidDefinitionException('KnpPaginatorBundle is not loaded.');
       }

       if (! $container->hasParameter('fkr_css_url_rewrite.css_url_rewrite.rewrite_only_if_file_exists')) {
           throw new InvalidDefinitionException('FkrCssURLRewriteBundle is not loaded.');
       }

       if (! $container->hasParameter('fos_user.registration.confirmation.from_email')) {
           throw new InvalidDefinitionException('FOSUserBundle is not loaded.');
       }

       $container->setParameter('knp_paginator.template.pagination', $container->getParameter('ihsan.simple_crud.template.pagination'));
    }
}

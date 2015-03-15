<?php

namespace Ihsan\SimpleCrudBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class IhsanSimpleCrudExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ihsan.simple_crud.per_page', $config['per_page']);
        $container->setParameter('ihsan.simple_crud.menu', $config['menu']);
        $container->setParameter('ihsan.simple_crud.identifier', $config['identifier']);
        $container->setParameter('ihsan.simple_crud.filter', $config['filter']);
        $container->setParameter('ihsan.simple_crud.view.form', $config['view']['form']);
        $container->setParameter('ihsan.simple_crud.view.form_theme', $config['view']['form_theme']);
        $container->setParameter('ihsan.simple_crud.view.show', $config['view']['show']);
        $container->setParameter('ihsan.simple_crud.view.grid', $config['view']['grid']);
        $container->setParameter('ihsan.simple_crud.view.pagination', $config['view']['pagination']);

        $action = array();
        if ($config['per_page']['show']) {
            array_push($action, 'show');
        }
        if ($config['per_page']['edit']) {
            array_push($action, 'edit');
        }
        if ($config['per_page']['delete']) {
            array_push($action, 'delete');
        }

        $container->setParameter('ihsan.simple_crud.grid_action', $action);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}

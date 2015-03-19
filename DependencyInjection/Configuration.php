<?php

namespace Ihsan\SimpleAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ihsan_simple_admin');

        $rootNode
            ->children()
                ->scalarNode('app_title')
                    ->defaultValue('IhsanSimpleAdmin')
                ->end()
                ->integerNode('per_page')
                    ->defaultValue(10)
                ->end()
                ->scalarNode('identifier')
                    ->defaultValue('id')
                ->end()
                ->scalarNode('date_time_format')
                    ->defaultValue('d-m-Y')
                ->end()
                ->scalarNode('menu')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('profile_fields')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('filter')
                    ->defaultValue('name')
                ->end()
                ->scalarNode('translation_domain')
                    ->defaultValue('IhsanSimpleAdminBundle')
                ->end()
                ->arrayNode('security')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->arrayNode('user')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->children()
                                ->scalarNode('form_class')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('entity_class')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('show_fields')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('username', 'email', 'roles'))
                                ->end()
                                ->arrayNode('grid_fields')
                                    ->defaultValue(array('username', 'email', 'roles'))
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('change_password')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('form_class')
                                    ->defaultValue('ihsan.simple_admin.change_password_form')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('grid_action')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('show')->defaultTrue()->end()
                        ->booleanNode('edit')->defaultTrue()->end()
                        ->booleanNode('delete')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('themes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('dashboard')
                            ->defaultValue('IhsanSimpleAdminBundle:Index:index.html.twig')
                        ->end()
                        ->scalarNode('profile')
                            ->defaultValue('IhsanSimpleAdminBundle:Index:profile.html.twig')
                        ->end()
                        ->scalarNode('form_theme')
                            ->defaultValue('IhsanSimpleAdminBundle:Form:fields.html.twig')
                        ->end()
                        ->scalarNode('pagination')
                            ->defaultValue('IhsanSimpleAdminBundle:Layout:pagination.html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
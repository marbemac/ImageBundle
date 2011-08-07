<?php

namespace Marbemac\ImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('marbemac_image');

        $rootNode
            ->children()
                ->scalarNode('image_class')->defaultValue('Marbemac\ImageBundle\Document\Image')->cannotBeEmpty()->end()
                ->scalarNode('image_manager')->defaultValue('Marbemac\ImageBundle\Document\ImageManager')->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }

}
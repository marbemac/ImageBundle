<?php

namespace Marbemac\ImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;

class MarbemacImageExtension extends Extension
{
    protected $resources = array(
        'manager' => 'manager.xml'
    );

    public function load(array $configs, ContainerBuilder $container)
    {
        $definition = new Definition('Marbemac\ImageBundle\Extension\MarbemacImageTwigExtension');
        $definition->addTag('twig.extension');
        $container->setDefinition('marbemac_image_twig_extension', $definition);

        $this->loadDefaults($container);
        
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $variables = array(
                        'image_class',
                        'image_manager'
                    );

        foreach ($variables as $attribute) {
            $container->setParameter('marbemac_image.options.'.$attribute, $config[$attribute]);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/marbemac_image';
    }

    /**
     * @codeCoverageIgnore
     */
    protected function loadDefaults($container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        foreach ($this->resources as $resource) {
            $loader->load($resource);
        }
    }
}
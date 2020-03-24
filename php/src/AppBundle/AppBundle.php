<?php

namespace AppBundle;

use Bazinga\Bundle\HateoasBundle\DependencyInjection\Configuration;
use Metadata\Driver\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
//    /**
//     * @param ContainerBuilder $container
//     */
//    public function build(ContainerBuilder $container)
//    {
//        $loader = new YamlFileLoader($container, new \Symfony\Component\Config\FileLocator(__DIR__.'/Resources/config'));
//        $loader->load('routing.yml');
//    }
}

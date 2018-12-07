<?php

namespace PiedWeb\ReservationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class PiedWebReservationExtension extends Extension //implements PrependExtensionInterface
{
    public function getAlias()
    {
        return 'piedweb_reservation';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Better idea to get config everywhere ?
        foreach ($config as $key => $value) {
            if ('payment_method' == $key) {
                $value = implode('|', $value);
            }
            $container->setParameter('app.'.$key, $value);
        }

        // todo: A tester,
        // Si il est preprend par l'implémentation par défault à la première utilisation,
        // doctrine.yaml créé une erreur
        //var_dump( $container->getParameter('app.entity_order')); die();
        $this->prepend($container);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container)
    {
        //var_dump(1); die();
        // Load configuration for other package
        $parser = new Parser();
        $finder = Finder::create()->files()->name('*.yaml')->in(__DIR__.'/../Resources/config/packages/');
        foreach ($finder as $file) {
            $configs = $parser->parse(file_get_contents($file->getRealPath()));
            if (null !== $configs) {
                foreach ($configs as $name => $config) {
                    $container->prependExtensionConfig($name, $config);
                }
            }
        }
    }
}

<?php

namespace PiedWeb\ReservationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * php bin/console config:dump-reference PiedWebCMSBundle.
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder
            ->root('piedweb_reservation')
                ->children()
                    ->scalarNode('entity_product')->defaultValue('PiedWeb\ReservationBundle\Entity\Product')->cannotBeEmpty()->end()
                    ->scalarNode('entity_order')->defaultValue('PiedWeb\ReservationBundle\Entity\Order')->cannotBeEmpty()->end()
                    ->scalarNode('entity_order_item')->defaultValue('PiedWeb\ReservationBundle\Entity\OrderItem')->cannotBeEmpty()->end()
                    ->scalarNode('entity_basket')->defaultValue('PiedWeb\ReservationBundle\Entity\Basket')->cannotBeEmpty()->end()
                    ->scalarNode('entity_basket_item')->defaultValue('PiedWeb\ReservationBundle\Entity\BasketItem')->cannotBeEmpty()->end()
                ->end()
        ;

        return $treeBuilder;
    }
}

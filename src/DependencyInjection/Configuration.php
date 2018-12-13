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
                    ->arrayNode('payment_method')
                        ->prototype('scalar')->end()
                        ->defaultValue([
                            'PiedWeb\ReservationBundle\PaymentMethod\PaypalCheckoutExpress',
                            'PiedWeb\ReservationBundle\PaymentMethod\Cash',
                        ])
                        //->end()
                    ->end()
                    ->scalarNode('entity_product')->defaultValue('App\Entity\Product')->cannotBeEmpty()->end()
                    ->scalarNode('entity_order')->defaultValue('App\Entity\Order')->cannotBeEmpty()->end()
                    ->scalarNode('entity_order_item')->defaultValue('App\Entity\OrderItem')->cannotBeEmpty()->end()
                    ->scalarNode('entity_basket')->defaultValue('App\Entity\Basket')->cannotBeEmpty()->end()
                    ->scalarNode('entity_basket_item')->defaultValue('App\Entity\BasketItem')->cannotBeEmpty()->end()
                ->end()
        ;

        return $treeBuilder;
    }
}

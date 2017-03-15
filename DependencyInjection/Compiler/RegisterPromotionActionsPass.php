<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class RegisterPromotionActionsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sylius.registry_promotion_action')) {
            return;
        }

        $promotionActionRegistry = $container->getDefinition('sylius.registry_promotion_action');

        $promotionActionTypeToLabelMap = [];
        foreach ($container->findTaggedServiceIds('sylius.promotion_action') as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged promotion action needs to have `type` and `label` attributes.');
            }

            $promotionActionTypeToLabelMap[$attributes[0]['type']] = $attributes[0]['label'];
            $promotionActionRegistry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }

        $container->setParameter('sylius.promotion_actions', $promotionActionTypeToLabelMap);
    }
}

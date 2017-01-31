<?php

/*
 * This file is part of the EmharMandrillApp bundle.
 *
 * (c) Emmanuel Harleaux
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Emhar\MandrillAppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('emhar_mandrill_app');

		$rootNode
            ->children()
                ->scalarNode('api_key')->isRequired()->end()
                ->scalarNode('test_email')->defaultFalse()->end()
            ->end()
		;

		return $treeBuilder;
	}

}

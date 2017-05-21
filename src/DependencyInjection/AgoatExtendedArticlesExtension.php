<?php

/*
 * This file is part of the Extended Articles Extension.
 *
 * Copyright (c) 2017 Arne Stappen (alias aGoat)
 *
 * @license LGPL-3.0+
 */

namespace Agoat\ExtendedArticlesBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds the bundle services to the container.
 *
 * @author Arne Stappen <https://github.com/agoat>
 */
class AgoatExtendedArticlesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
		
		// Set valid extensions paramater if not set anyway
		if (!$container->hasParameter('contao.article.formats'))
		{
			$container->setParameter('contao.article.formats', ['standard', 'aside', 'link', 'quote', 'status', 'image', 'gallery', 'video', 'chat']);
		}		
    }
}

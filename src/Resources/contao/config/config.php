<?php

/*
 * Contao Extended Articles Extension
 *
 * Copyright (c) 2017 Arne Stappen
 *
 * @license LGPL-3.0+
 */
 
 
/**
 * Register back end module (additional javascript)
 */
$GLOBALS['BE_MOD']['content']['article']['javascript'][] = 'bundles/agoatextendedarticles/core.js';
$GLOBALS['BE_MOD']['content']['article']['javascript'][] = 'bundles/agoatextendedarticles/chosenAddOption.js';


/**
 * Register front end modules
 */
$arrModules['article']['articles'] = 'ModuleArticles';
$arrModules['article']['teasers'] = 'ModuleTeasers';
$arrModules['article']['articlereader'] = 'ModuleArticleReader';

array_insert($GLOBALS['FE_MOD'], 1, $arrModules);


/**
 * Register HOOK
 */
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('tl_article_extended', 'toggleFeaturedArticle'); 

$bundles = \System::getContainer()->getParameter('kernel.bundles');
if (isset($bundles['ContaoCommentsBundle']))
{
	$GLOBALS['TL_HOOKS']['listComments'][] = array('tl_comments_extendedarticle', 'listPatternComments'); 
}


/**
 * Back end form fields (widgets)
 */
$GLOBALS['BE_FFL']['inputselect'] = 'InputSelect';

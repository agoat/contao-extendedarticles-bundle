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


/**
 * Module
 */
$arrModules['article']['articlecontent'] = 'ModuleArticleContent';
$arrModules['article']['articleteaser'] = 'ModuleArticleTeaser';
$arrModules['article']['articlereader'] = 'ModuleArticleReader';

array_insert($GLOBALS['FE_MOD'], 1, $arrModules);


/**
 * HOOK
 */
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('tl_article_extended', 'toggleFeaturedArticle'); 

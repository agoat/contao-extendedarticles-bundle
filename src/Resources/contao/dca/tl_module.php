<?php

/*
 * Contao Extended Articles Extension
 *
 * Copyright (c) 2017 Arne Stappen
 *
 * @license LGPL-3.0+
 */
 

/**
 * Palettes
 */
//$GLOBALS['TL_DCA']['tl_module']['palettes']['articles']    = '{title_legend},name,headline,type;{config_legend},news_archives,numberOfItems,news_featured,perPage,skipFirst;{template_legend:hide},news_metaFields,news_template,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['articleteaser']  = '{title_legend},name,headline,type;{config_legend},numberOfItems,featured,perPage,skipFirst,otherColumn;{reference_legend:hide},defineRoot;{sort_legend:hide},sortByDate;{template_legend:hide},metaFields,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'sortByDate';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['sortByDate'] = 'sortOrder';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['sortByDate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['sortByDate'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['sortOrder'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['sortOrder'],
	'default'                 => 'descending',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('ascending', 'descending'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(16) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['featured'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['featured'],
	'default'                 => 'all',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('all', 'featured', 'unfeatured'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(16) NOT NULL default ''"
);
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

// Change article teaser settings
$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = str_replace(
	'{teaser_legend:hide},teaserCssID,showTeaser,teaser;', 
	'{teaser_legend},date,time,subTitle,teaser,singleSRC,alt,caption;', 
	$GLOBALS['TL_DCA']['tl_article']['palettes']['default']
);
$GLOBALS['TL_DCA']['tl_article']['palettes']['__selector__'][] = 'addImage';
$GLOBALS['TL_DCA']['tl_article']['subpalettes']['addImage'][] = 'singleSRC,alt,caption';



// Fields
$GLOBALS['TL_DCA']['tl_article']['fields']['date'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['date'],
	'default'                 => time(),
	'exclude'                 => true,
	'filter'                  => true,
	'sorting'                 => true,
	'flag'                    => 8,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'date', 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['time'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['time'],
	'default'                 => time(),
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'time', 'doNotCopy'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['subTitle'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news']['subTitle'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['addImage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news']['addImage'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['singleSRC'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news']['singleSRC'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['alt'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news']['alt'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['caption'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news']['caption'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);





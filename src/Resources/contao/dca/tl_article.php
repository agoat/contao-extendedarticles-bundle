<?php

/*
 * Contao Extended Articles Extension
 *
 * Copyright (c) 2017 Arne Stappen
 *
 * @license LGPL-3.0+
 */
 
 /**
 * Load tl_content language file
 */
System::loadLanguageFile('tl_content');


/**
 * Palettes
 */

// Change article teaser settings
$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = str_replace(
	'{teaser_legend:hide},teaserCssID,showTeaser,teaser;', 
	'{date_legend},date,time;{location_legend},location,latlong;{teaser_legend},subTitle,teaser;{image_legend},addImage;', 
	$GLOBALS['TL_DCA']['tl_article']['palettes']['default']
);
$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = str_replace(
	'{expert_legend:hide},guests,cssID', 
	'{expert_legend:hide},noComments,featured,cssID,guests', 
	$GLOBALS['TL_DCA']['tl_article']['palettes']['default']
);
$GLOBALS['TL_DCA']['tl_article']['palettes']['__selector__'][] = 'addImage';
$GLOBALS['TL_DCA']['tl_article']['subpalettes']['addImage'] = 'singleSRC,alt,caption';


// Layout corrections
$GLOBALS['TL_DCA']['tl_article']['fields']['guests']['eval']['tl_class'] = 'w50 m12';


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

$GLOBALS['TL_DCA']['tl_article']['fields']['location'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['location'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['latlong'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['latlong'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'digit', 'multiple'=>true, 'size'=>2, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['subTitle'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['subTitle'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'long'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['addImage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['addImage'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['singleSRC'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('filesOnly'=>true, 'extensions'=>Config::get('validImageTypes'), 'fieldType'=>'radio', 'mandatory'=>true),
	'save_callback' => array
	(
//		array('tl_news', 'storeFileMetaInformation')
	),
	'sql'                     => "binary(16) NULL"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['alt'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['alt'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['caption'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['caption'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'allowHtml'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['noComments'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['noComments'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 clr'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['featured'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['featured'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "char(1) NOT NULL default ''"
);






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
$GLOBALS['TL_DCA']['tl_module']['palettes']['articles']  = '{title_legend},name,headline,type;{config_legend},numberOfItems,fromColumn,perPage,skipFirst,featured,showTeaser;{reference_legend:hide},defineRoot;{sort_legend:hide},sortByDate;{template_legend:hide},articleTpl,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['teasers']  = '{title_legend},name,headline,type;{config_legend},numberOfItems,fromColumn,perPage,skipFirst,featured,readerModule;{reference_legend:hide},defineRoot;{sort_legend:hide},sortByDate;{template_legend:hide},teaserTpl,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$bundles = \System::getContainer()->getParameter('kernel.bundles');

if (isset($bundles['ContaoCommentsBundle']))
{
	$GLOBALS['TL_DCA']['tl_module']['palettes']['articlereader']  = '{title_legend},name,headline,type;{config_legend},showTeaser;{template_legend:hide},articleTpl,customTpl;{image_legend:hide},imgSize;{comment_legend},allowComments;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
}
else
{
	$GLOBALS['TL_DCA']['tl_module']['palettes']['articlereader']  = '{title_legend},name,headline,type;{config_legend},showTeaser;{template_legend:hide},articleTpl,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
}

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'] = array_merge
(
	$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'],
	array('sortByDate','allowComments')
);
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['sortByDate'] = 'sortOrder';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['allowComments'] = 'com_order,perPage,com_moderate,com_bbcode,com_protected,com_requireLogin,com_disableCaptcha,com_template,notifyAdmin';





/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['readerModule'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['readerModule'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_extendedarticle', 'getReaderModules'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('mandatory'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50 wizard'),
	'wizard' => array
	(
		array('tl_module_extendedarticle', 'editModule')
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['featured'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['featured'],
	'default'                 => 'all',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('all_articles', 'featured_articles', 'unfeatured_articles'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fromColumn'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fromColumn'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module', 'getLayoutSections'),
	'reference'               => &$GLOBALS['TL_LANG']['COLS'],
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['showTeaser'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showTeaser'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['articleTpl'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['articleTpl'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_extendedarticle', 'getArticleTemplates'),
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['teaserTpl'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['teaserTpl'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_extendedarticle', 'getTeaserTemplates'),
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);
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
$GLOBALS['TL_DCA']['tl_module']['fields']['allowComments'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['allowComments'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['notifyAdmin'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['notifyAdmin'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true,'tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Arne Stappen <https://github.com/agoat>
 */
class tl_module_extendedarticle extends Backend
{

	/**
	 * Return all article templates as array
	 *
	 * @param DataContainer $dc
	 *
	 * @return array
	 */
	public function getArticleTemplates()
	{
		return $this->getTemplateGroup('article_');
	}
	
	
	/**
	 * Return all article teaser templates as array
	 *
	 * @param DataContainer $dc
	 *
	 * @return array
	 */
	public function getTeaserTemplates()
	{
		return $this->getTemplateGroup('teaser_');
	}	
	
	/**
	 * Get all news reader modules and return them as array
	 *
	 * @return array
	 */
	public function getReaderModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='articlereader' ORDER BY t.name, m.name");
		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}
		return $arrModules;
	}

	/**
	 * Return the edit article alias wizard
	 *
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function editModule(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . \StringUtil::specialchars($GLOBALS['TL_LANG']['tl_module']['edit_module']) . '" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . \StringUtil::specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['tl_module']['edit_module'])) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_module']['edit_module']) . '</a>';
	}

}

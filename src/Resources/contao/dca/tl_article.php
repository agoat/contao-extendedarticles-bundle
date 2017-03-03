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


$GLOBALS['TL_DCA']['tl_article']['list']['sorting']['panelLayout'] = 'filter;filter;sort,search';
//$GLOBALS['TL_DCA']['tl_article']['list']['sorting']['mode'] = 2;
// Maybe set the mode to sorting by date with custom filter


/**
 * Callbacks
 */
$GLOBALS['TL_DCA']['tl_article']['config']['onsubmit_callback'][] = array('tl_article_extended', 'adjustTime');


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
$GLOBALS['TL_DCA']['tl_article']['list']['label']['fields'] = array('title', 'inColumn', 'date');
$GLOBALS['TL_DCA']['tl_article']['list']['label']['format'] = '%s <span style="color:#999;padding-left:3px">[%s/%s]</span>';


array_insert($GLOBALS['TL_DCA']['tl_article']['list']['operations'], 6, array
(
	'feature' => array
	(
		'label'               => &$GLOBALS['TL_LANG']['tl_article']['feature'],
		'icon'                => 'featured.svg',
		'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleFeaturedArticle(this,%s)"',
		'button_callback'     => array('tl_article_extended', 'iconFeatured')		
	)
));


$GLOBALS['TL_DCA']['tl_article']['fields']['guests']['eval']['tl_class'] = 'w50 m12';


// Field corrections
$GLOBALS['TL_DCA']['tl_article']['fields']['inColumn']['options_callback'] = array('tl_article_extended', 'getActiveLayoutSections');


// Fields
$GLOBALS['TL_DCA']['tl_article']['fields']['date'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['date'],
	'default'                 => time(),
	'exclude'                 => true,
	'filter'                  => 2,
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
	'filter'                  => 2,
	'sorting'                 => true,
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
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['caption'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['caption'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'allowHtml'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['noComments'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['noComments'],
	'exclude'                 => true,
	'filter'                  => 2,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 clr'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_article']['fields']['featured'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_article']['featured'],
	'exclude'                 => true,
	'filter'                  => 2,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "char(1) NOT NULL default ''"
);



/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Arne Stappen <https://github.com/agoat>
 */
class tl_article_extended extends tl_article
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Adjust start end end time of the event based on date, span, startTime and endTime
	 *
	 * @param DataContainer $dc
	 */
	public function adjustTime(DataContainer $dc)
	{
		// Return if there is no active record (override all)
		if (!$dc->activeRecord)
		{
			return;
		}
		$arrSet['date'] = strtotime(date('Y-m-d', $dc->activeRecord->date) . ' ' . date('H:i:s', $dc->activeRecord->time));
		$arrSet['time'] = $arrSet['date'];
		$this->Database->prepare("UPDATE tl_article %s WHERE id=?")->set($arrSet)->execute($dc->id);
	}


	/**
	 * Return all active layout sections as array
	 *
	 * @param DataContainer $dc
	 *
	 * @return array
	 */
	public function getActiveLayoutSections(DataContainer $dc)
	{
		// Show only active sections
		if ($dc->activeRecord->pid)
		{
			$arrSections = array();
			$objPage = PageModel::findWithDetails($dc->activeRecord->pid);

			// Get the layout sections
			foreach (array('layout', 'mobileLayout') as $key)
			{
				if (!$objPage->$key)
				{
					continue;
				}

				$objLayout = LayoutModel::findByPk($objPage->$key);

				if ($objLayout === null)
				{
					continue;
				}

				$arrModules = \StringUtil::deserialize($objLayout->modules);

				if (empty($arrModules) || !is_array($arrModules))
				{
					continue;
				}

				$articleModules = array('0');
				
				if (($objArticleModules = ModuleModel::findBy(array("tl_module.type IN('articles','teasers','articlereader')"), null)) !== null)
				{
					$articleModules = array_merge($articleModules, $objArticleModules->fetchEach('id'));
				}	
	
				// Find all sections with an article module (see #6094)
				foreach ($arrModules as $arrModule)
				{
					if (in_array($arrModule['mod'], $articleModules) && $arrModule['enable'])
					{
						$arrSections[] = $arrModule['col'];
					}
				}
			}
		}

		// Show all sections (e.g. "override all" mode)
		else
		{
			$arrSections = array('header', 'left', 'right', 'main', 'footer');
			$objLayout = $this->Database->query("SELECT sections FROM tl_layout WHERE sections!=''");

			while ($objLayout->next())
			{
				$arrCustom = \StringUtil::deserialize($objLayout->sections);

				// Add the custom layout sections
				if (!empty($arrCustom) && is_array($arrCustom))
				{
					foreach ($arrCustom as $v)
					{
						if (!empty($v['id']))
						{
							$arrSections[] = $v['id'];
						}
					}
				}
			}
		}

		return Backend::convertLayoutSectionIdsToAssociativeArray($arrSections);
	}

	/**
	 * Return the "feature/unfeature element" button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function iconFeatured($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(Input::get('fid')))
		{
			$this->toggleFeatured(Input::get('fid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}
		
		// Check permissions AFTER checking the fid, so hacking attempts are logged
		if (!$this->User->hasAccess('tl_article::featured', 'alexf'))
		{
			return '';
		}
		
		$href .= '&amp;fid='.$row['id'].'&amp;state='.($row['featured'] ? '' : 1);
		
		if (!$row['featured'])
		{
			$icon = 'featured_.svg';
		}
		
		return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['featured'] ? 1 : 0) . '"').'</a> ';
	}

	/**
	 * Feature/unfeature an article
	 *
	 * @param integer       $intId
	 * @param boolean       $blnVisible
	 * @param DataContainer $dc
	 *
	 * @return string
	 *
	 * @throws Contao\CoreBundle\Exception\AccessDeniedException
	 */
	public function toggleFeatured($intId, $blnVisible, DataContainer $dc=null)
	{
		// Check permissions to edit
		Input::setGet('id', $intId);
		Input::setGet('act', 'feature');

		$this->checkPermission();
		
		// Check permissions to feature
		if (!$this->User->hasAccess('tl_article::featured', 'alexf'))
		{
			throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to feature/unfeature article ID ' . $intId . '.');
		}
		
		$objVersions = new Versions('tl_article', $intId);
		$objVersions->initialize();
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_article']['fields']['featured']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_article']['fields']['featured']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, ($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, $this);
				}
			}
		}
		
		// Update the database
		$this->Database->prepare("UPDATE tl_article SET tstamp=". time() .", featured='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);
					   
		$objVersions->create();
	}

	/**
	 * HOOK executePostActions
	 *
	 * @param string        $strAction
	 * @param DataContainer $dc
	 */
	public function toggleFeaturedArticle($strAction, DataContainer $dc)
	{
		if ($strAction == 'toggleFeaturedArticle')
		{
			
			$this->toggleFeatured(\Input::post('id'), ((\Input::post('state') == 1) ? true : false));
		}
		
	}

}

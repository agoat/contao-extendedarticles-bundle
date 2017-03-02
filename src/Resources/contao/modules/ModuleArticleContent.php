<?php

/*
 * Contao Extended Articles Extension
 *
 * Copyright (c) 2017 Arne Stappen
 *
 * @license LGPL-3.0+
 */
 
namespace Contao;



/**
 * Provides methodes to handle article rendering
 *
 * @property array  $news_archives
 * @property string $news_jumpToCurrent
 * @property string $news_format
 * @property int    $news_readerModule
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleArticleContent extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_articlecontent';


	/**
	 * Do not display the module if there are no articles
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['articleteaser'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$strBuffer = parent::generate();

		return !empty($this->Template->articles) ? $strBuffer : '';
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		/** @var PageModel $objPage */
		global $objPage;

		if (!strlen($this->fromColumn))
		{
			$this->fromColumn = $this->strColumn;
		}
		
		$pageId = $objPage->id;
		$pageObj = $objPage;

		// Show the articles of a different page
		if ($this->defineRoot && $this->rootPage > 0)
		{
			if (($objTarget = $this->objModel->getRelated('rootPage')) instanceof PageModel)
			{
				$pageId = $objTarget->id;
				$pageObj = $this->objModel->getRelated('rootPage');

				/** @var PageModel $objTarget */
				$this->Template->request = $objTarget->getFrontendUrl();
			}
		}


		$limit = null;
		$offset = intval($this->skipFirst);
		
		// Maximum number of items
		if ($this->numberOfItems > 0)
		{
			$limit = $this->numberOfItems;
		}
		
		// Handle featured articles
		if ($this->featured == 'featured')
		{
			$blnFeatured = true;
		}
		elseif ($this->featured == 'unfeatured')
		{
			$blnFeatured = false;
		}
		else
		{
			$blnFeatured = null;
		}

		// Handle extra sorting
		if ($this->sortByDate)
		{
			$arrOptions = array('order' => 'date ' . (($this->sortOrder == 'descending') ? 'DESC' : 'ASC'));
		}
		else
		{
			$arrOptions = array();
		}
		
		
		// Get published articles
		$objArticles = \ExtendedArticleModel::findPublishedByPidAndColumnAndFeatured($pageId, $this->fromColumn, $blnFeatured, $limit, $offset, $arrOptions);

		$arrArticles = array();
		
		if ($objArticles !== null)
		{
			while ($objArticles->next())
			{
				list($strId, $strClass) = \StringUtil::deserialize($objArticles->cssID, true);
				$latlong = \StringUtil::deserialize($objArticles->latlong);
				
				if ($objArticles->cssClass != '')
				{
					$strClass = ' ' . $objArticles->cssClass;
				}
				if ($objArticles->featured)
				{
					$strClass = ' featured' . $strClass;
				}
				
				$article = $objArticles->alias ?: $objArticles->id;
				$href = '/articles/' . (($objArticles->inColumn != 'main') ? $objArticles->inColumn . ':' : '') . $article;
			
				$objPartial = new \FrontendTemplate($this->teaserTpl);

				// Add meta data
				$objPartial->id = ($strId) ?: 'teaser-' . $objArticles->id;
				$objPartial->class = $strClass;
				$objPartial->title = \StringUtil::specialchars($objArticles->title);
				$objPartial->subtitle = $objArticles->subTitle;
				$objPartial->teaser = \StringUtil::toHtml5($objArticles->teaser);
				$objPartial->date = \Date::parse($pageObj->datimFormat, $objArticles->date);
				$objPartial->timestamp = $objArticles->date;
				$objPartial->datetime = date('Y-m-d\TH:i:sP', $objArticles->date);
				$objPartial->location = $objArticles->location;
				$objPartial->latlong = ($latlong[0] !='' && $latlong[1] != '') ? implode(',', $latlong) : false;
				$objPartial->href = $pageObj->getFrontendUrl($href);
				$objPartial->readMore = \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $this->headline), true);
				$objPartial->more =  $GLOBALS['TL_LANG']['MSC']['more'];

				if (($objAuthor = $objArticles->getRelated('author')) instanceof UserModel)
				{
					$objPartial->author = $objAuthor->name;
				}
				
				$objPartial->addImage = false;
				
				if ($objArticles->addImage && $objArticles->singleSRC != '')
				{
					$objModel = \FilesModel::findByUuid($objArticles->singleSRC);
									
					if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path))
					{
						$this->addImageToTemplate($objPartial, array(
							'singleSRC' => $objModel->path,
							'size' => $this->imgSize,
							'alt' => $objArticles->alt,
							'title' => $objArticles->title,
							'caption' => $objArticles->caption
						));
					}
				}
			
				$arrArticles[] = $objPartial->parse();

			}
		}

		$this->Template->articles = $arrArticles;
	}
}
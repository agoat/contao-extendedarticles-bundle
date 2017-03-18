<?php

/*
 * Contao Extended Articles Extension
 *
 * Copyright (c) 2017 Arne Stappen
 *
 * @license LGPL-3.0+
 */
 
namespace Contao;

use Patchwork\Utf8;


/**
 * Provides methodes to handle article teaser rendering
 *
 * @property array  $news_archives
 * @property string $news_jumpToCurrent
 * @property string $news_format
 * @property int    $news_readerModule
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleTeasers extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_teasers';


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

		// Show the article reader if an article is directly called
		if ($this->readerModule > 0 && (isset($_GET['articles']) || (\Config::get('useAutoItem') && isset($_GET['auto_item']))))
		{
			return $this->getFrontendModule($this->readerModule, $this->strColumn);
		}
		
		return parent::generate();
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
		if ($this->featured == 'featured_articles')
		{
			$blnFeatured = true;
		}
		elseif ($this->featured == 'unfeatured_articles')
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
		
		// Handle category filter
		if ($this->filterByCategory)
		{
			$strCategory = $this->category;
		}
		
		// Get published articles
		$objArticles = \ExtendedArticleModel::findPublishedByPidAndColumnAndFeaturedAndCategory($pageId, $this->fromColumn, $blnFeatured, $strCategory, $limit, $offset, $arrOptions);

		$arrArticles = array();
		
		if ($objArticles !== null)
		{
			while ($objArticles->next())
			{
				list($strId, $strClass) = \StringUtil::deserialize($objArticles->cssID, true);
				$latlong = \StringUtil::deserialize($objArticles->latlong);
	
				if ($strClass != '')
				{
					$strClass = ' ' . $strClass;
				}
				if ($objArticles->featured)
				{
					$strClass .= ' featured';
				}
				if ($objArticles->format != 'standard')
				{
					$strClass .= ' ' . $objArticles->format;
				}

				$article = $objArticles->alias ?: $objArticles->id;
	
				switch ($objArticles->readmore)
				{
					case 'page':
						if (($objTarget = $objArticles->getRelated('jumpTo')) instanceof PageModel)
						{
							$href = ampersand($objTarget->getFrontendUrl());
						}
						$readMore = \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $this->headline), true);
						break;
					case 'external':
						$href = ampersand($objArticles->url);
						$readMore = \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['open'], $objArticles->url));
						break;						
					default:
						$href = $pageObj->getFrontendUrl('/articles/' . (($objArticles->inColumn != 'main') ? $objArticles->inColumn . ':' : '') . $article);
						$readMore = \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $this->headline), true);
				}
				
				$objTeaserTemplate = new \FrontendTemplate($this->teaserTpl);
				$objTeaserTemplate->setData($objArticles->row());

				// Add meta data
				$objTeaserTemplate->cssId = ($strId) ?: 'teaser-' . $objArticles->id;
				$objTeaserTemplate->cssClass = $strClass;

				// Add teaser
				$objTeaserTemplate->title = \StringUtil::specialchars($objArticles->title);
				$objTeaserTemplate->subtitle = $objArticles->subTitle;
				$objTeaserTemplate->teaser = \StringUtil::toHtml5($objArticles->teaser);
				$objTeaserTemplate->date = \Date::parse($pageObj->datimFormat, $objArticles->date);
				$objTeaserTemplate->timestamp = $objArticles->date;
				$objTeaserTemplate->datetime = date('Y-m-d\TH:i:sP', $objArticles->date);
				$objTeaserTemplate->location = $objArticles->location;
				$objTeaserTemplate->latlong = ($latlong[0] !='' && $latlong[1] != '') ? implode(',', $latlong) : false;
				$objTeaserTemplate->href = $href;
				$objTeaserTemplate->attributes = ($objArticles->target) ? ' target="_blank"' : '';
				$objTeaserTemplate->readMore = $readMore;
				$objTeaserTemplate->more = $GLOBALS['TL_LANG']['MSC']['more'];

				if (($objAuthor = $objArticles->getRelated('author')) instanceof UserModel)
				{
					$objTeaserTemplate->author = $objAuthor->name;
				}
				
				$objTeaserTemplate->addImage = false;
				
				if ($objArticles->addImage && $objArticles->singleSRC != '')
				{
					$objModel = \FilesModel::findByUuid($objArticles->singleSRC);
									
					if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path))
					{
						$this->addImageToTemplate($objTeaserTemplate, array(
							'singleSRC' => $objModel->path,
							'size' => $this->imgSize,
							'alt' => $objArticles->alt,
							'title' => $objArticles->title,
							'caption' => $objArticles->caption
						));
					}
				}
			
				$arrArticles[] = $objTeaserTemplate->parse();

			}
		}

		$this->Template->articles = $arrArticles;
	}
}
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
class ModuleArticles extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_articles';


	/**
	 * Do not render the module if an article is called directly
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

		// Don't render articles if an article is called directly
		if (isset($_GET['articles']) || (\Config::get('useAutoItem') && isset($_GET['auto_item'])))
		{
			return;
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

				$objArticleTemplate = new \FrontendTemplate($this->articleTpl);
				$objArticleTemplate->setData($objArticles->row());

				// Add html data
				$objArticleTemplate->cssId = ($strId) ?: 'teaser-' . $objArticles->id;
				$objArticleTemplate->cssClass = $strClass;
				$objArticleTemplate->href = $href;
				$objArticleTemplate->attributes = ($objArticles->target && $objArticles->readmore !== 'default') ? ' target="_blank"' : '';
				$objArticleTemplate->readMore = $readMore;
				$objArticleTemplate->more = $GLOBALS['TL_LANG']['MSC']['more'];

				// Add content elements
				$arrElements = array();
				$objCte = \ContentModel::findPublishedByPidAndTable($objArticles->id, 'tl_article');

				if ($objCte !== null)
				{
					$intCount = 0;
					$intLast = $objCte->count() - 1;

					while ($objCte->next())
					{
						$arrCss = array();

						/** @var ContentModel $objRow */
						$objRow = $objCte->current();

						// Add the "first" and "last" classes (see #2583)
						if ($intCount == 0 || $intCount == $intLast)
						{
							if ($intCount == 0)
							{
								$arrCss[] = 'first';
							}

							if ($intCount == $intLast)
							{
								$arrCss[] = 'last';
							}
						}

						$objRow->classes = $arrCss;
						$arrElements[] = $this->getContentElement($objRow, $this->strColumn);
						++$intCount;
					}
				}

				$objArticleTemplate->elements = $arrElements;

				// Add teaser
				if ($objArticleTemplate->showTeaser = $this->showTeaser)
				{
					// Add meta information
					$objArticleTemplate->date = \Date::parse($objPage->datimFormat, $objArticles->date);
					$objArticleTemplate->timestamp = $objArticles->date;
					$objArticleTemplate->datetime = date('Y-m-d\TH:i:sP', $objArticles->date);
					$objArticleTemplate->location = $objArticles->location;
					$objArticleTemplate->latlong = ($latlong[0] !='' && $latlong[1] != '') ? implode(',', $latlong) : false;

					// Add teaser data
					$objArticleTemplate->title = \StringUtil::specialchars($objArticles->title);
					$objArticleTemplate->subtitle = $objArticles->subTitle;
					$objArticleTemplate->teaser = \StringUtil::toHtml5($objArticles->teaser);

					// Add author
					if (($objAuthor = $objArticles->getRelated('author')) instanceof UserModel)
					{
						$objArticleTemplate->author = $objAuthor->name;
					}
					
					// Add image
					$objArticleTemplate->addImage = false;
					
					if ($objArticles->addImage && $objArticles->singleSRC != '')
					{
						$objModel = \FilesModel::findByUuid($objArticles->singleSRC);
										
						if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path))
						{
							$this->addImageToTemplate($objArticleTemplate, array(
								'singleSRC' => $objModel->path,
								'size' => $this->imgSize,
								'alt' => $objArticles->alt,
								'title' => $objArticles->title,
								'caption' => $objArticles->caption
							));
						}
					}
				}
		
				$arrArticles[] = $objArticleTemplate->parse();

			}
		}

		$this->Template->articles = $arrArticles;
	}
}
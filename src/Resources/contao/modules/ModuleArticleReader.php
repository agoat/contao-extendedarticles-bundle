<?php

/*
 * Contao Extended Articles Extension
 *
 * Copyright (c) 2017 Arne Stappen
 *
 * @license LGPL-3.0+
 */
 
namespace Contao;

use Contao\CoreBundle\Exception\PageNotFoundException;
use Patchwork\Utf8;


/**
 * Provides methodes to handle direct article rendering
 *
 * @property array  $news_archives
 * @property string $news_jumpToCurrent
 * @property string $news_format
 * @property int    $news_readerModule
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleArticleReader extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_articlereader';


	/**
	 * Do not display the module if there are no articles
	 *
	 * @return string
	 */
	public function generate()
	{
		global $objPage;
		
		// Don't try to render an article from direct call for a 404 error page
		if ($objPage->type == 'error_404')
		{
			return;
		}
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

		return parent::generate();
	}

	
	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;

		if (!strlen($this->fromColumn))
		{
			$this->fromColumn = $this->strColumn;
		}


		// Get section and article alias
		list($strSection, $strArticle) = explode(':', \Input::get('articles'));

		if ($strArticle === null)
		{
			$strArticle = $strSection;
		}
		
		// Get published article
		$objArticle = \ArticleModel::findPublishedByIdOrAliasAndPid($strArticle, false);

		if (null === $objArticle)
		{
			throw new PageNotFoundException('Page not found: ' . \Environment::get('uri'));
		}
		
		// Overwrite the page title (see #2853 and #4955)
		if ($strArticle != '' && ($strArticle == $objArticle->id || $strArticle == $objArticle->alias) && $objArticle->title != '')
		{
			$objPage->pageTitle = strip_tags(\StringUtil::stripInsertTags($objArticle->title));
			
			if ($objArticle->teaser != '')
			{
				$objPage->description = $this->prepareMetaDescription($objArticle->teaser);
			}
		}		

		list($strId, $strClass) = \StringUtil::deserialize($objArticles->cssID, true);

		if ($objArticles->cssClass != '')
		{
			$strClass = ' ' . $objArticles->cssClass;
		}
		if ($objArticles->featured)
		{
			$strClass = ' featured' . $strClass;
		}



		$objArticleTemplate = new \FrontendTemplate($this->articleTpl);
		
		$objArticleTemplate->id = ($strId) ?: 'article-' . $objArticle->id;
		$objArticleTemplate->class = $strClass;
		$objArticleTemplate->column = $this->inColumn;
		
		$arrElements = array();
		$objCte = \ContentModel::findPublishedByPidAndTable($objArticle->id, 'tl_article');

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
		

		// Add article teaser
		if ($objArticleTemplate->addTeaser = $this->teaser)
		{
			list($strId, $strClass) = \StringUtil::deserialize($objArticle->cssID, true);
			$latlong = \StringUtil::deserialize($objArticle->latlong);
			
			if ($objArticle->cssClass != '')
			{
				$strClass = ' ' . $objArticle->cssClass;
			}
			if ($objArticle->featured)
			{
				$strClass = ' featured' . $strClass;
			}
			
			// Add meta data
			$objArticleTemplate->title = \StringUtil::specialchars($objArticle->title);
			$objArticleTemplate->subtitle = $objArticle->subTitle;
			$objArticleTemplate->teaser = \StringUtil::toHtml5($objArticle->teaser);
			$objArticleTemplate->date = \Date::parse($objPage->datimFormat, $objArticle->date);
			$objArticleTemplate->timestamp = $objArticle->date;
			$objArticleTemplate->datetime = date('Y-m-d\TH:i:sP', $objArticle->date);
			$objArticleTemplate->location = $objArticle->location;
			$objArticleTemplate->latlong = ($latlong[0] !='' && $latlong[1] != '') ? implode(',', $latlong) : false;

			if (($objAuthor = $objArticle->getRelated('author')) instanceof UserModel)
			{
				$objArticleTemplate->author = $objAuthor->name;
			}
			
			$objArticleTemplate->addImage = false;
			
			if ($objArticle->addImage && $objArticle->singleSRC != '')
			{
				$objModel = \FilesModel::findByUuid($objArticle->singleSRC);
								
				if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path))
				{
					$this->addImageToTemplate($objArticleTemplate, array(
						'singleSRC' => $objModel->path,
						'size' => $this->imgSize,
						'alt' => $objArticle->alt,
						'title' => $objArticle->title,
						'caption' => $objArticle->caption
					));
				}
			}
		}

		// Back link
		$objArticleTemplate->backlink = 'javascript:history.go(-1)'; // see #6955
		$objArticleTemplate->back = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['goBack']);

		$this->Template->article = $objArticleTemplate->parse();
	}
}
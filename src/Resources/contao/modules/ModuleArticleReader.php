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
		
		// Don't try to render an direct called article for a 404 error page
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
			$strSection = 'main';
		}

		if ($strSection != $this->strColumn)
		{
			return;
		}
		
		if (!strlen($strArticle))
		{
			return;
		}
		
		// Get published article
		$objArticle = \ArticleModel::findPublishedByIdOrAliasAndPid($strArticle, false);

		if (null === $objArticle)
		{
			throw new PageNotFoundException('Page not found: ' . \Environment::get('uri'));
		}
		
		// Overwrite the page title (see @contao/core #2853 and #4955)
		if ($strArticle != '' && ($strArticle == $objArticle->id || $strArticle == $objArticle->alias) && $objArticle->title != '')
		{
			$objPage->pageTitle = strip_tags(\StringUtil::stripInsertTags($objArticle->title));
			
			if ($objArticle->teaser != '')
			{
				$objPage->description = $this->prepareMetaDescription($objArticle->teaser);
			}
		}		

		list($strId, $strClass) = \StringUtil::deserialize($objArticle->cssID, true);

		if ($strClass != '')
		{
			$strClass = ' ' . $objArticle->cssClass;
		}
		if ($objArticle->featured)
		{
			$strClass .= ' featured';
		}
		if ($objArticle->format != 'standard')
		{
			$strClass .= ' ' . $objArticle->format;
		}

		$objArticleTemplate = new \FrontendTemplate($this->articleTpl);

		$objArticleTemplate->id = $objArticle->id;
		$objArticleTemplate->inColumn = $objArticle->inColumn;
		$objArticleTemplate->cssId = ($strId) ?: 'article-' . $objArticle->id;
		$objArticleTemplate->cssClass = $strClass;
		
		// Add content elements
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
		if ($objArticleTemplate->showTeaser = $this->showTeaser)
		{
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

		$this->Template->article = $objArticleTemplate->parse();

		// Back link
		$this->Template->backlink = 'javascript:history.go(-1)'; // see #6955
		$this->Template->back = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['goBack']);

		// Add comments (if comments bundle installed)
		$bundles = \System::getContainer()->getParameter('kernel.bundles');

		if ($this->allowComments && isset($bundles['ContaoCommentsBundle']))
		{
			$this->Template->allowComments = true;
			$this->Template->noComments = ($objArticle->noComments) ? true : false;
			
			// Adjust the comments headline level
			$intHl = min(intval(str_replace('h', '', $this->hl)), 5);
			$this->Template->hlc = 'h' . ($intHl + 1);

			$arrNotifies = array();

			// Notify the author
			if (($objAuthor = $objArticle->getRelated('author')) instanceof UserModel && $objAuthor->email != '')
			{
				$arrNotifies[] = $objAuthor->email;
			}

			// Notify the system administrator
			if ($this->notifyAdmin)
			{
				$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
			}


			$this->import('Comments');
			$objConfig = new \stdClass();

			$objConfig->perPage = $this->perPage;
			$objConfig->order = $this->com_order;
			$objConfig->template = $this->com_template;
			$objConfig->requireLogin = $this->com_requireLogin;
			$objConfig->disableCaptcha = $this->com_disableCaptcha;
			$objConfig->bbcode = $this->com_bbcode;
			$objConfig->moderate = $this->com_moderate;

			$this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_article', $objArticle->id, $arrNotifies);
		}
	}
}

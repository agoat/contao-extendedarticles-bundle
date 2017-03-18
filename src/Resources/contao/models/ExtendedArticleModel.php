<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;


class ExtendedArticleModel extends \ArticleModel
{

	/**
	 * Find all published articles by their parent ID, column and featured status
	 *
	 * @param integer $intPid     The page ID
	 * @param string  $strColumn  The column name
	 * @param array   $arrOptions An optional options array
	 *
	 * @return Model\Collection|ArticleModel[]|ArticleModel|null A collection of models or null if there are no articles in the given column
	 */
	public static function findPublishedByPidAndColumnAndFeatured($intPid, $strColumn, $blnFeatured, $intLimit=0, $intOffset=0, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.pid=? AND $t.inColumn=?");
		$arrValues = array($intPid, $strColumn);
		
		if ($blnFeatured === true)
		{
			$arrColumns[] = "$t.featured='1'";
		}
		elseif ($blnFeatured === false)
		{
			$arrColumns[] = "$t.featured=''";
		}

		if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
		{
			$time = \Date::floorToMinute();
			$arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
		}

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}
		
		$arrOptions['limit']  = $intLimit;
		$arrOptions['offset'] = $intOffset;
		
		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}

	/**
	 * Find all published articles by their parent ID and column
	 *
	 * @param integer $intPid     The page ID
	 * @param string  $strColumn  The column name
	 * @param array   $arrOptions An optional options array
	 *
	 * @return Model\Collection|ArticleModel[]|ArticleModel|null A collection of models or null if there are no articles in the given column
	 */
	public static function findPublishedByPidAndColumnAndFeaturedAndCategory($intPid, $strColumn, $blnFeatured, $strCategory, $intLimit=0, $intOffset=0, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.pid=? AND $t.inColumn=?");
		$arrValues = array($intPid, $strColumn);
		
		if ($blnFeatured === true)
		{
			$arrColumns[] = "$t.featured='1'";
		}
		elseif ($blnFeatured === false)
		{
			$arrColumns[] = "$t.featured=''";
		}

		if ($strCategory != '')
		{
			$arrColumns[] = "$t.category LIKE ?";
			$arrValues[] = '%' . $strCategory . '%';
		}

			
		if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
		{
			$time = \Date::floorToMinute();
			$arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
		}

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}
		
		$arrOptions['limit']  = $intLimit;
		$arrOptions['offset'] = $intOffset;
		
		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}


}
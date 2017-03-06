<?php

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Arne Stappen <https://github.com/agoat>
 */
class tl_comments_extendedarticle extends Backend
{

	// listComments Hook
	public function listPatternComments($arrRow) 
	{
		if ($arrRow['source'] == 'tl_article')
		{
			$db = Database::getInstance();
			
			$objParent = $db->prepare("SELECT id, title FROM tl_article WHERE id=?")
						    ->execute($arrRow['parent']);
			
			if ($objParent->numRows)
			{
				return ' (<a href="contao/main.php?do=article&amp;table=tl_content&amp;id=' . $objParent->id . '&amp;rt=' . REQUEST_TOKEN . '">' . $objParent->title . '</a>)';
			}
		}
	}
}

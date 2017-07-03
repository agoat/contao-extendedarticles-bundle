<?php

/*
 * Contao Extended Articles Extension
 *
 * Copyright (c) 2017 Arne Stappen
 *
 * @license LGPL-3.0+
 */


// Replace the generateArticle callback
$GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'][] = array('tl_page_extendedarticles', 'adjustTime');


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Arne Stappen (alias aGOAT) <https://github.com/agoat>
 */
class tl_page_extendedarticles extends Backend
{


	/**
	 * Adjust the date and time for the automatically generated article
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

		// Not a regular page
		if (!in_array($dc->activeRecord->type, array('regular', 'error_403', 'error_404')))
		{
			return;
		}

		/** @var Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface $objSessionBag */
		$objSessionBag = System::getContainer()->get('session')->getBag('contao_backend');

		$new_records = $objSessionBag->get('new_records');

		// Not a new page
		if (!$new_records || !is_array($new_records[$dc->table]) || !in_array($dc->id, $new_records[$dc->table]))
		{
			return;
		}

		// Check whether there are articles (e.g. on copied pages)
		$objTotal = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_article WHERE pid=?")
								   ->execute($dc->id);

	   if ($objTotal->count != 1)
		{
			return;
		}

		// Set current time
		$arrSet['date'] = $arrSet['time'] = time();
	
		$this->Database->prepare("UPDATE tl_article %s WHERE pid=?")->set($arrSet)->execute($dc->id);
	}
}

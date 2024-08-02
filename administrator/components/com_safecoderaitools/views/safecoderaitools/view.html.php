<?php

/**
 * @package     SafeCoder AI Tools
 * @subpackage  com_safecoderaitools
 * 
 * @version     1.0.0
 * 
 * @author      Miron Savan <hello@safecoder.com>
 * @link        https://www.safecoder.com/aitools
 * @copyright   Copyright (C) 2012 SafeCoder Software SRL (RO30786660)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SafeCoderAIToolsViewSafeCoderAITools extends HtmlView
{

	function display($tpl = null)
	{

		/** @var \Joomla\CMS\Application\CMSApplication $app */
		$app = Factory::getApplication();

		$this->IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
		if ($this->IsLightbox == 1) {
			$this->IsLightboxParam = '&IsLightbox=1';
		}
		else {
			$this->IsLightboxParam = '';
		}

		// Display the view
		parent::display($tpl);
	}
}
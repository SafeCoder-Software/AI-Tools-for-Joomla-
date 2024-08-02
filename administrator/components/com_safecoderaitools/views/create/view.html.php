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
use SafeCoderSoftwareAITools\Libraries\Config;
use SafeCoderSoftwareAITools\Libraries\ContextDetails;
use SafeCoderSoftwareAITools\Libraries\PromptData;
use SafeCoderSoftwareAITools\Libraries\Utility;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SafeCoderAIToolsViewCreate extends HtmlView
{

	/**
	 * Display view
	 *
	 * @param [type] $tpl = template
	 * @return void
	 */
	function display($tpl = null)
	{

		/** @var \Joomla\CMS\Application\CMSApplication $app */
		$app = Factory::getApplication();

		$this->IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
		if ($this->IsLightbox != 1) {
			$this->IsLightbox = 0;
		}

		$this->PredefinedPrompt = $app->input->get('PredefinedPrompt', 0, 'INT');
		if (Utility::isValidPositiveNumber($this->PredefinedPrompt) == true) {

			$this->PredefinedPrompt = $this->PredefinedPrompt - 1000;
			if ($this->PredefinedPrompt < 0) {
				$this->PredefinedPrompt = -1;
			}
		} else {
			$this->PredefinedPrompt = -1;
		}

		$this->AIToolsPromptID = $app->input->get('PromptID', 0, 'INT');
		if (Utility::isValidPositiveNumber($this->AIToolsPromptID) != true) {
			$this->AIToolsPromptID = 0;
		}

		$this->AIToolsPromptObj = new PromptData($this->AIToolsPromptID);
		if ($this->AIToolsPromptObj->IsProcessedValue == true && $this->AIToolsPromptObj->IsOKValue) {
			$this->AIToolsPromptObj->LoadChoices();
		}

		$this->AIToolsConfig = new Config();

		$this->ArticleID = $app->input->get('ArticleID', 0, 'INT');
		if (Utility::isValidPositiveNumber($this->ArticleID) != true) {
			$this->ArticleID = 0;
		}

		$this->CategoryID = $app->input->get('CategoryID', 0, 'INT');
		if (Utility::isValidPositiveNumber($this->CategoryID) != true) {
			$this->CategoryID = 0;
		}

		$this->AIToolsContext = new ContextDetails();

		if (Utility::isValidPositiveNumber($this->ArticleID) == true) {
			$this->AIToolsContext->ArticleID = $this->ArticleID;
		} else if (Utility::isValidPositiveNumber($this->CategoryID) == true) {
			$this->AIToolsContext->CategoryID = $this->CategoryID;
		}

		$this->AIToolsContext->Load();

		// Display the view
		parent::display($this->SetTemplate());
	}

	/**
	 * Set View Template
	 *
	 * @return string|null
	 */
	private function SetTemplate()
	{

		try {

			if (Utility::isValidPositiveNumber($this->AIToolsPromptID) != true) {
				return 'new';
			} else {
				if ($this->AIToolsPromptObj->IsProcessedValue == true) {
					return null;
				} else {
					return 'result';
				}
			}
		} catch (\Throwable $th) {
			return 'new';
		}
	}
}

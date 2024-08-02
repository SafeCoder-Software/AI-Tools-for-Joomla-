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
use Joomla\CMS\MVC\Controller\BaseController;
use SafeCoderSoftwareAITools\Libraries\General;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

define('SCS_COMPONENT_ROOT_URL', 'index.php?option=com_safecoderaitools');

/** @var \Joomla\CMS\Application\CMSApplication $app */
$app = Factory::getApplication();

JLoader::registerNamespace('SafeCoderSoftwareAITools\Libraries', __DIR__ . DIRECTORY_SEPARATOR . 'libraries');
JLoader::registerNamespace('League\HTMLToMarkdown', __DIR__ . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'html-to-markdown' . DIRECTORY_SEPARATOR . 'src');

General::CheckSettings();
General::SetPageTitle();
General::SetPageButtons();
General::LoadPageResources();
if($app->input->get('IsLightbox', 0, 'INT') == 1) {
    General::RemovePageDecorations();
}

$controller = BaseController::getInstance('SafeCoderAITools');

$controller->execute($app->input->getCmd('task'));

$controller->redirect();
<?php

/**
 * @package     SafeCoder AI Tools
 * @subpackage  System.SafeCoderAITools
 * 
 * @version     1.0.0
 * 
 * @author      Miron Savan <hello@safecoder.com>
 * @link        https://www.safecoder.com/aitools
 * @copyright   Copyright (C) 2012 SafeCoder Software SRL (RO30786660)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use SafeCoderSoftwareAITools\Helper\PlgHelper;

// No direct access to this file
defined('_JEXEC') or die;

class PlgSystemSafeCoderAITools extends CMSPlugin
{

    // autoload language 3.x+
    protected $autoloadLanguage = true;

    // helper file present
    private $PlgHelperExists = false;

    /**
     * Constructor - load component libraries, language, check helper file
     *
     * @param [type] $subject
     * @param array $config
     */
    public function __construct(&$subject, $config = [])
    {

        try {
            $componentLibrariesPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_safecoderaitools' . DIRECTORY_SEPARATOR . 'libraries';
            if (is_dir($componentLibrariesPath)) {
                JLoader::registerNamespace('SafeCoderSoftwareAITools\Libraries', $componentLibrariesPath);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }


        $this->loadLanguage();
        $this->PlgHelperExists = $this->CheckHelperDIR();

        parent::__construct($subject, $config);
    }


    /**
     * Check if component exists and try to add toolbar button
     *
     * @return void
     */
    public function onAfterDispatch()
    {

        try {

            /** @var \Joomla\CMS\Application\CMSApplication $app */
            $app = Factory::getApplication();

            if (!$app->isClient('administrator')) {
                return;
            }

            if ($this->PlgHelperExists != true) {
                $app->enqueueMessage(Text::_('PLG_SYSTEM_SAFECODERAITOOLS_INCOMPLETE_PLUGIN_INSTALL'), 'error');
                return;
            }

            $CheckComponent = PlgHelper::CheckComponentInstall();
            if ($CheckComponent != true) {
                $app->enqueueMessage(Text::_('PLG_SYSTEM_SAFECODERAITOOLS_COMPONENT_MISSING_404'), 'warning');
                return;
            }

            $this->DisplayDecorationsComContent();

            return;
        } catch (\Throwable $th) {
            return;
        }
    }

    /**
     * Display toolbar button
     *
     * @return boolean
     */
    private function DisplayDecorationsComContent()
    {

        try {

            /** @var \Joomla\CMS\Application\CMSApplication $app */
            $app = Factory::getApplication();

            $option = (string) $app->input->get('option', '', 'CMD');
            $view = (string) $app->input->get('view', '', 'CMD');
            $layout = (string) $app->input->get('layout', '', 'CMD');

            if (!in_array($option, array('com_content', 'com_categories'))) {
                return;
            }

            if (!in_array($view, array('category', 'article'))) {
                return;
            }

            if ($layout != 'edit') {
                return;
            }

            PlgHelper::LoadPageResources();

            // toolbar button
            PlgHelper::LoadToolbarDropdownToolbar();

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Check if helper file exists
     *
     * @return boolean
     */
    private function CheckHelperDIR()
    {

        try {

            $HelperDIR = JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'safecoderaitools' . DIRECTORY_SEPARATOR . 'Helper';
            if (!is_dir($HelperDIR)) {
                return false;
            }

            $HelperDIR .= DIRECTORY_SEPARATOR . 'PlgHelper.php';
            if (!file_exists($HelperDIR)) {
                return false;
            }

            require_once $HelperDIR;

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

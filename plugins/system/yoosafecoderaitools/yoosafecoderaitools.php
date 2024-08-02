<?php

/**
 * @package     SafeCoder AI Tools
 * @subpackage  System.YOOSafeCoderAITools
 * 
 * @version     1.0.0
 * 
 * @author      Miron Savan <hello@safecoder.com>
 * @link        https://www.safecoder.com/aitools
 * @copyright   Copyright (C) 2012 SafeCoder Software SRL (RO30786660)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use SafeCoderSoftwareAITools\Helper\PlgYooHelper;
use YOOtheme\Application;

// No direct access to this file
defined('_JEXEC') or die;

class plgSystemYOOSafeCoderAiTools extends CMSPlugin
{

    // autoload plugin language
    protected $autoloadLanguage = true;

    // helper file present
    private $PlgYooHelperExists = false;

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
        $this->PlgYooHelperExists = $this->CheckHelperDIR();

        parent::__construct($subject, $config);
    }

    /**
     * Load YOOTheme Classes + page resources
     *
     * @return void
     */
    public function onAfterInitialise()
    {

        try {
            if ($this->PlgYooHelperExists != true) {
                return;
            }

            if (PlgYooHelper::CheckComponentInstall() != true) {
                return;
            }

            if (!class_exists(Application::class, false)) {
                return;
            }

            /** @var \Joomla\CMS\Application\CMSApplication $app */
            $japp = Factory::getApplication();

            $p = $japp->input->get('p', '', 'string');
            if ($p != 'customizer') {
                return;
            }

            PlgYooHelper::LoadPageResources();

            $app = Application::getInstance();
            $app->load(__DIR__ . '/bootstrap.php');

            return;
        } catch (\Throwable $th) {
            return;
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
            $HelperDIR = JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'yoosafecoderaitools' . DIRECTORY_SEPARATOR . 'YOOHelper';
            if (!is_dir($HelperDIR)) {
                return false;
            }

            $HelperDIR .= DIRECTORY_SEPARATOR . 'PlgYooHelper.php';
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

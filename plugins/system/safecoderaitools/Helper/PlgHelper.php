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

namespace SafeCoderSoftwareAITools\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;
use SafeCoderSoftwareAITools\Libraries\Config;
use SafeCoderSoftwareAITools\Libraries\Utility;

class PlgHelper
{

    /**
     * Check if com_safecoderaitools is installed
     *
     * @return boolean
     */
    public static function CheckComponentInstall()
    {

        $libraryPath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_safecoderaitools';
        if (!is_dir($libraryPath)) {
            return false;
        }

        $libraryPath .= DIRECTORY_SEPARATOR . 'libraries';
        if (!is_dir($libraryPath)) {
            return false;
        }

        $UtilityPath = $libraryPath . DIRECTORY_SEPARATOR . 'Utility.php';
        if (!file_exists($UtilityPath)) {
            return false;
        }

        if (!\class_exists('\SafeCoderSoftwareAITools\Libraries\Utility')) {
            return false;
        }

        return true;
    }

    /**
     * Load custom css for J! backend
     *
     * @return boolean
     */
    public static function LoadPageResources()
    {
        try {

            $doc = Factory::getDocument();
            $wa = $doc->getWebAssetManager();
            $wa->addInlineStyle('

                .icon-safecoder-square {
                    text-align:center;
                    background-image: url(\'' . Uri::root() . 'media/com_safecoderaitools/plugin/logo-button.png\');
                    height: 1.25em;
                    width: 1.25em;
                    background-position: center center;
                    background-repeat: no-repeat;
                    background-size: 1rem;
                }

                .button-scs-ai-dropdown-toolbar.show .icon-safecoder-square, .button-scs-ai-dropdown-toolbar:hover .icon-safecoder-square {
                    background-image: url(\'' . Uri::root() . 'media/com_safecoderaitools/plugin/logo-button-inverted.png\');
                }

                #toolbar-scs-ai-dropdown-toolbar .dropdown-menu.show {
                    max-width:300px !important;
                }

                #toolbar-scs-ai-dropdown-toolbar .dropdown-menu > div.btn-group {
                    display: none !important;
                }

                #toolbar-scs-ai-dropdown-toolbar .dropdown-menu joomla-toolbar-button {
                    display: inline-block !important;
                    height: 40px !important;
                    width: 100% !important;
                    margin: 0px !important;
                    padding: 0px !important;
                }

                #toolbar-scs-ai-dropdown-toolbar .dropdown-menu joomla-toolbar-button > a::before {
                    content: "" !important;
                }

            ');

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Load Article Edit + Category Edit dropdown menu
     *
     * @return boolean
     */
    public static function LoadToolbarDropdownToolbar()
    {

        try {

            $config = new Config();

            /** @var \Joomla\CMS\Application\CMSApplication $app */
            $app = Factory::getApplication();

            $id = (int) $app->input->get('id', 0, 'INT');
            if (Utility::isValidPositiveNumber($id) != true) {
                $id = 0;
            }

            $toolbar = Toolbar::getInstance('toolbar');

            $IsLightBox = '&IsLightbox=1';
            if ($config->getPluginOpenInModal() != 1) {
                $IsLightBox = '';
            }

            $TypePreface = '';
            $viewName = $app->input->get('view', '', 'CMD');
            if ($viewName == 'article') {
                $TypePreface = '&ArticleID=';
            } else if ($viewName == 'category') {
                $TypePreface = '&CategoryID=';
            }

            $AIDropdownToolbar = new DropdownButton('scs-ai-dropdown-toolbar');
            $AIDropdownToolbar->setParent($toolbar);
            $AIDropdownToolbar->text('PLG_SYSTEM_SAFECODERAITOOLS_DROPDOWN_LABEL');
            $AIDropdownToolbar->toggleSplit(false);
            $AIDropdownToolbar->icon('icon-safecoder-square');
            $AIDropdownToolbar->buttonClass('btn btn-action');
            $AIDropdownToolbar->listCheck(false);

            $childBar = $AIDropdownToolbar->getChildToolbar();

            if (Utility::isValidPositiveNumber($id) == true) {
                if ($config->getPluginOpenInModal() == 1) {
                    $PromptWithContext = $childBar->popupButton('scs-ai-dropdown-prompt-with-context');
                } else {
                    $PromptWithContext = $childBar->linkButton('scs-ai-dropdown-prompt-with-context');
                    $PromptWithContext->target('_blank');
                }


                if (!empty($TypePreface)) {
                    $TypePreface = $TypePreface . $id;
                }
                $PromptWithContext->url('index.php?option=com_safecoderaitools&view=create' . $TypePreface . $IsLightBox);
                $PromptWithContext->text('PLG_SYSTEM_SAFECODERAITOOLS_DROPDOWN_NEW_PROMPT_WITH_CONTEXT_LABEL');
                $PromptWithContext->icon('fa fa-plus');
                $PromptWithContext->buttonClass('btn btn-primary');
                if ($config->getPluginOpenInModal() == 1) {
                    $PromptWithContext->title(Text::_('PLG_SYSTEM_SAFECODERAITOOLS_DROPDOWN_NEW_PROMPT_WITH_CONTEXT_TITLE'));
                    $PromptWithContext->modalWidth('80vw');
                    $PromptWithContext->bodyHeight('80vh');
                }
                $PromptWithContext->listCheck(false);
            }

            if ($config->getPluginShowCleanPrompt() == 1 || Utility::isValidPositiveNumber($id) != true) {

                if ($config->getPluginOpenInModal() == 1) {
                    $PromptClean = $childBar->popupButton('scs-ai-dropdown-prompt-clean');
                } else {
                    $PromptClean = $childBar->linkButton('scs-ai-dropdown-prompt-clean');
                    $PromptClean->target('_blank');
                }
                $PromptClean->url('index.php?option=com_safecoderaitools&view=create' . $IsLightBox);
                $PromptClean->text('PLG_SYSTEM_SAFECODERAITOOLS_DROPDOWN_NEW_PROMPT_WITHOUT_CONTEXT_LABEL');
                $PromptClean->icon('fa fa-plus');
                $PromptClean->buttonClass('btn btn-primary');
                if ($config->getPluginOpenInModal() == 1) {
                    $PromptClean->title(Text::_('PLG_SYSTEM_SAFECODERAITOOLS_DROPDOWN_NEW_PROMPT_WITHOUT_CONTEXT_TITLE'));
                    $PromptClean->modalWidth('80vw');
                    $PromptClean->bodyHeight('80vh');
                }
                $PromptClean->listCheck(false);
            }

            if ($config->getPluginShowHistoryButton() == 1) {

                if ($config->getPluginOpenInModal() == 1) {
                    $PromptHistory = $childBar->popupButton('scs-ai-dropdown-prompt-history');
                } else {
                    $PromptHistory = $childBar->linkButton('scs-ai-dropdown-prompt-history');
                    $PromptHistory->target('_blank');
                }
                $PromptHistory->url('index.php?option=com_safecoderaitools&view=history' . $IsLightBox);
                $PromptHistory->text('PLG_SYSTEM_SAFECODERAITOOLS_DROPDOWN_HISTORY');
                $PromptHistory->icon('fa fa-history');
                $PromptHistory->buttonClass('btn btn-primary');

                if ($config->getPluginOpenInModal() == 1) {
                    $PromptHistory->title(Text::_('PLG_SYSTEM_SAFECODERAITOOLS_DROPDOWN_HISTORY_TITLE'));
                    $PromptHistory->modalWidth('80vw');
                    $PromptHistory->bodyHeight('80vh');
                }
                
                $PromptHistory->listCheck(false);
            }

            if ($config->getPluginShowPredefinedPrompt() == 1 && Utility::IsValidNonEmptyArray($config->getPredefinedPromptsList()) == true && Utility::isValidPositiveNumber($id) == true) {

                $max = count($config->getPredefinedPromptsList());
                if (Utility::isValidPositiveNumber($config->getPluginPredefinedPromptMax()) == true) {
                    $max = $config->getPluginPredefinedPromptMax();
                }

                $list = $config->getPredefinedPromptsList();

                if ($config->getPluginPredefinedPromptListReversed() == 1) {
                    $list = \array_reverse($list);
                }

                $x = 0;
                foreach ($list as $key => $predefinedPrompt) {

                    if ($x >= $max) {
                        continue;
                    }

                    if (Utility::IsValidNonEmptyArray($predefinedPrompt) != true) {
                        continue;
                    }

                    if (!\array_key_exists('promptTitle', $predefinedPrompt) || !\array_key_exists('promptContent', $predefinedPrompt)) {
                        continue;
                    }

                    if (empty($predefinedPrompt['promptTitle']) || empty($predefinedPrompt['promptContent'])) {
                        continue;
                    }

                    if ($config->getPluginOpenInModal() == 1) {
                        $PredefinedPrompt = $childBar->popupButton('scs-ai-dropdown-prompt-predefined-clean' .  $key);
                    } else {
                        $PredefinedPrompt = $childBar->linkButton('scs-ai-dropdown-prompt-predefined-clean' .  $key);
                        $PredefinedPrompt->target('_blank');
                    }

                    $PromptKey = $key + 1000;

                    $PredefinedPrompt->url('index.php?option=com_safecoderaitools&view=create' . $TypePreface . '&PredefinedPrompt=' . $PromptKey . $IsLightBox);

                    if (\strlen($predefinedPrompt['promptTitle']) > 28) {
                        $PredefinedPrompt->text(\substr($predefinedPrompt['promptTitle'], 0, 28) . '...');
                    } else {
                        $PredefinedPrompt->text($predefinedPrompt['promptTitle']);
                    }

                    $PredefinedPrompt->icon('fa fa-plane-departure text-success');
                    $PredefinedPrompt->buttonClass('btn btn-default');
                    if ($config->getPluginOpenInModal() == 1) {
                        $PredefinedPrompt->title($predefinedPrompt['promptTitle']);
                        $PredefinedPrompt->modalWidth('80vw');
                        $PredefinedPrompt->bodyHeight('80vh');
                    }
                    $PredefinedPrompt->listCheck(false);

                    $x++;
                }
            }

            if ($config->getPluginShowHelpButton() == 1) {
                if ($config->getPluginOpenInModal() == 1) {
                    $PromptWithContext = $childBar->popupButton('scs-ai-dropdown-show-help-button');
                } else {
                    $PromptWithContext = $childBar->linkButton('scs-ai-dropdown-show-help-button');
                    $PromptWithContext->target('_blank');
                }

                $PromptWithContext->url('https://support.safecoder.com/aitools');
                $PromptWithContext->text('PLG_SYSTEM_SAFECODERAITOOLS_GET_HELP');
                $PromptWithContext->icon('icon-help text-danger');
                $PromptWithContext->buttonClass('btn btn-primary');
                if ($config->getPluginOpenInModal() == 1) {
                    $PromptWithContext->title(Text::_('PLG_SYSTEM_SAFECODERAITOOLS_GET_HELP_TITLE'));
                    $PromptWithContext->modalWidth('80vw');
                    $PromptWithContext->bodyHeight('80vh');
                }
                $PromptWithContext->listCheck(false);
            }


            $buttons = $toolbar->getItems();

            $newButtons = array();

            foreach ($buttons as $button) {

                try {

                    if ($button->getName() == 'cancel') {
                        $newButtons[] = $AIDropdownToolbar;
                    }

                    $newButtons[] = $button;
                } catch (\Throwable $th) {
                    $newButtons[] = $button;
                }
            }

            $toolbar->setItems($newButtons);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

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

namespace SafeCoderSoftwareAITools\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use SafeCoderSoftwareAITools\Libraries\Config;
use SafeCoderSoftwareAITools\Libraries\Utility;

class PlgYooHelper
{

    /**
     * Check if com_safecoderaitools is installed
     *
     * @return boolean
     */
    public static function CheckComponentInstall()
    {

        try {
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
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Load customizer JS
     *
     * @return boolean
     */
    public static function LoadPageResources()
    {

        try {
            $scriptPath = \JPATH_PLUGINS . \DIRECTORY_SEPARATOR . 'system' . \DIRECTORY_SEPARATOR . 'yoosafecoderaitools' . \DIRECTORY_SEPARATOR . 'assets' . \DIRECTORY_SEPARATOR . 'js' . \DIRECTORY_SEPARATOR . 'scripts.js';

            if (!\file_exists($scriptPath)) {
                return false;
            }

            $content = \file_get_contents($scriptPath);
            if (empty($content)) {
                return false;
            }

            $content = \str_replace('{{SCSROOTURL}}', Uri::root(), $content);
            $content = \str_replace('{{PLG_SYSTEM_YOOSAFECODERAITOOLS_ERROR_WHILE_MAKING_LINK}}', Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_ERROR_WHILE_MAKING_LINK'), $content);

            $doc = Factory::getDocument();
            $wa = $doc->getWebAssetManager();
            $wa->addInlineScript($content);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Build panel details / button list
     *
     * @return array
     */
    public static function BuildPanelFields()
    {

        $panel = array();

        try {

            $baseLink = Uri::root();

            $config = new Config();

            // with context
            $panel['title'] = Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_TITLE');
            $panel['fields'] = array();

            $panel['fields']['scai_prompt_with_context'] = array();
            $panel['fields']['scai_prompt_with_context']['name'] = 'button-prompt-with-context';
            $panel['fields']['scai_prompt_with_context']['type'] = 'button-panel';
            $panel['fields']['scai_prompt_with_context']['text'] = Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_NEW_PROMPT_WITH_CONTEXT');
            $panel['fields']['scai_prompt_with_context']['panel'] = '';
            if (!empty($config->getOpenAIAPIKey())) {
                $panel['fields']['scai_prompt_with_context']['enable'] = 'true';
            } else {
                $panel['fields']['scai_prompt_with_context']['enable'] = 'false';
            }
            $panel['fields']['scai_prompt_with_context']['show'] = '$customizer.SafeCoderAiToolsItemID > 0';
            $panel['fields']['scai_prompt_with_context']['attrs'] = array();
            $panel['fields']['scai_prompt_with_context']['attrs']['onClick'] = 'OpenPrompt("' . \base64_encode($baseLink) . '", "' . Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_NEW_PROMPT_WITH_CONTEXT') . '", -1, 1)';

            // without context
            $panel['fields']['scai_prompt_clean'] = array();
            $panel['fields']['scai_prompt_clean']['name'] = 'button-prompt-clean';
            $panel['fields']['scai_prompt_clean']['type'] = 'button-panel';
            $panel['fields']['scai_prompt_clean']['text'] = Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_NEW_PROMPT_CLEAN');
            $panel['fields']['scai_prompt_clean']['panel'] = '';
            if (!empty($config->getOpenAIAPIKey())) {
                $panel['fields']['scai_prompt_clean']['enable'] = 'true';
            } else {
                $panel['fields']['scai_prompt_clean']['enable'] = 'false';
            }
            $panel['fields']['scai_prompt_clean']['show'] = '$customizer.config.scai_show_clean_prompt == 1';
            $panel['fields']['scai_prompt_clean']['attrs'] = array();
            $panel['fields']['scai_prompt_clean']['attrs']['onClick'] = 'OpenPrompt("' . \base64_encode($baseLink) . '", "' . Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_NEW_PROMPT_WITH_CONTEXT') . '", -1, 0)';

            // history button
            $panel['fields']['scai_prompt_history'] = array();
            $panel['fields']['scai_prompt_history']['name'] = 'button-prompt-history';
            $panel['fields']['scai_prompt_history']['type'] = 'button-panel';
            $panel['fields']['scai_prompt_history']['text'] = Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PROMPT_HISTORY');
            $panel['fields']['scai_prompt_history']['panel'] = '';
            if (!empty($config->getOpenAIAPIKey())) {
                $panel['fields']['scai_prompt_history']['enable'] = 'true';
            } else {
                $panel['fields']['scai_prompt_history']['enable'] = 'false';
            }
            $panel['fields']['scai_prompt_history']['show'] = '$customizer.config.scai_show_prompt_history == 1';
            $panel['fields']['scai_prompt_history']['attrs'] = array();
            $panel['fields']['scai_prompt_history']['attrs']['onClick'] = 'OpenPrompt("' . \base64_encode($baseLink) . '", "' . Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_NEW_PROMPT_WITH_CONTEXT') . '", -1, -3)';

            // help button
            $panel['fields']['scai_button_help'] = array();
            $panel['fields']['scai_button_help']['name'] = 'button-scs-help';
            $panel['fields']['scai_button_help']['type'] = 'button-panel';
            $panel['fields']['scai_button_help']['text'] = Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_GET_HELP');
            $panel['fields']['scai_button_help']['panel'] = '';
            if (!empty($config->getOpenAIAPIKey())) {
                $panel['fields']['scai_button_help']['enable'] = 'true';
            } else {
                $panel['fields']['scai_button_help']['enable'] = 'false';
            }
            $panel['fields']['scai_button_help']['show'] = '$customizer.config.scai_show_help_button == 1';
            $panel['fields']['scai_button_help']['attrs'] = array();
            $panel['fields']['scai_button_help']['attrs']['onClick'] = 'OpenPrompt("' . \base64_encode($baseLink) . '", "' . Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_NEW_PROMPT_WITH_CONTEXT') . '", -1, 2)';

            $panel['fieldset'] = array();
            $panel['fieldset']['default'] = array();
            $panel['fieldset']['default']['fields'] = array();
            $panel['fieldset']['default']['fields'][] = 'scai_prompt_with_context';
            $panel['fieldset']['default']['fields'][] = 'scai_prompt_clean';
            $panel['fieldset']['default']['fields'][] = 'scai_prompt_history';

            $list = $config->getPredefinedPromptsList();

            if (Utility::IsValidNonEmptyArray($list) != true) {
                return $panel;
            }

            $groupField = array();
            $groupField['label'] = Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_GROUP_TITLE');
            $groupField['type'] = 'group';
            $groupField['show'] = '$customizer.config.scai_show_predefined_prompts == 1';
            $groupField['divider'] = true;
            $groupField['fields'] = array();

            foreach ($list as $key => $item) {

                if (Utility::IsValidNonEmptyArray($item) != true) {
                    continue;
                }

                if (!\array_key_exists('promptTitle', $item) || !isset($item['promptTitle']) || empty(trim($item['promptTitle']))) {
                    continue;
                }

                $panel['fields']['scai_prompt_predefined_' . $key] = array();
                $panel['fields']['scai_prompt_predefined_' . $key]['name'] = 'button-predefined-prompt-item' . $key;
                $panel['fields']['scai_prompt_predefined_' . $key]['type'] = 'button-panel';
                $panel['fields']['scai_prompt_predefined_' . $key]['label'] = $item['promptTitle'];
                $panel['fields']['scai_prompt_predefined_' . $key]['text'] = Text::_('JOPEN');
                $panel['fields']['scai_prompt_predefined_' . $key]['panel'] = '';
                if (!empty($config->getOpenAIAPIKey())) {
                    $panel['fields']['scai_prompt_predefined_' . $key]['enable'] = 'true';
                } else {
                    $panel['fields']['scai_prompt_predefined_' . $key]['enable'] = 'false';
                }
                $panel['fields']['scai_prompt_predefined_' . $key]['attrs'] = array();
                $panel['fields']['scai_prompt_predefined_' . $key]['attrs']['onClick'] = 'OpenPrompt("' . \base64_encode($baseLink) . '", "' . $item['promptTitle'] . '", ' . (int) $key . ', 1)';
                $groupField['fields'][] = 'scai_prompt_predefined_' . $key;
            }

            $panel['fieldset']['default']['fields'][] = $groupField;
            $panel['fieldset']['default']['fields'][] = 'scai_button_help';

            return $panel;
        } catch (\Throwable $th) {
            return array();
        }
    }
}

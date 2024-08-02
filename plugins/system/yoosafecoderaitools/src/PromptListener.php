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

use Joomla\CMS\Language\Text;
use SafeCoderSoftwareAITools\Helper\PlgYooHelper;
use YOOtheme\Config;
use function YOOtheme\app;
use YOOtheme\Arr;

class PromptListener
{
    /**
     * Add prompt panel
     *
     * @param Config $config
     * @return boolean
     */
    public static function initCustomizer(Config $config)
    {

        try {
            $config->set('customizer.panels.safecoderaitools-settings', [
                'title'  => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_NAME'),
                'fields' => [
                    'scai_open_in_window' => [
                        'label' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_NEW_PROMPT_IN_BUTTON'),
                        'type' => 'select',
                        'name' => 'scai_open_in',
                        'default' => 1,
                        'description' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_NEW_PROMPT_IN_BUTTON_DESC'),
                        'options' => [
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_NEW_PROMPT_IN_LIGHTBOX') => 1,
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_NEW_PROMPT_IN_NEW_TAB') => 0
                        ]
                    ],
                    'scai_show_clean_prompt_window' => [
                        'label' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_CLEAN_PROMPT_BUTTON'),
                        'type' => 'select',
                        'name' => 'scai_show_clean_prompt',
                        'default' => 1,
                        'description' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_CLEAN_PROMPT_BUTTON_DESC'),
                        'options' => [
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_SHOW') => 1,
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_HIDE') => 0
                        ]
                    ],
                    'scai_show_prompt_history_window' => [
                        'label' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_HISTORY_PROMPT_BUTTON'),
                        'type' => 'select',
                        'name' => 'scai_show_prompt_history',
                        'default' => 1,
                        'description' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_HISTORY_PROMPT_BUTTON_DESC'),
                        'options' => [
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_SHOW') => 1,
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_HIDE') => 0
                        ]
                    ],
                    'scai_show_predefined_prompts_window' => [
                        'label' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_PREDEFINED_LIST_BUTTON'),
                        'type' => 'select',
                        'name' => 'scai_show_predefined_prompts',
                        'default' => 1,
                        'description' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_PREDEFINED_LIST_BUTTON'),
                        'options' => [
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_SHOW') => 1,
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_HIDE') => 0
                        ]
                    ],
                    'scai_show_help_button_window' => [
                        'label' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_HELP_BUTTON'),
                        'type' => 'select',
                        'name' => 'scai_show_help_button',
                        'default' => 1,
                        'description' => Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_HELP_BUTTON_DESC'),
                        'options' => [
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_SHOW') => 1,
                            Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_HIDE') => 0
                        ]
                    ],
                ],
            ]);

            $config->set('customizer.sections.settings.fields.settings.items.safecoderaitools-settings', Text::_('PLG_SYSTEM_YOOSAFECODERAITOOLS_PANEL_NAME'));

            $config->set('customizer.panels.safecoderaitools-prompt', PlgYooHelper::BuildPanelFields());

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Add Prompt Button for Panel to every element
     *
     * @param [type] $type
     * @return array
     */
    public static function PromptButton($type)
    {

        try {
            if (!app(Config::class)->get('app.isCustomizer')) {
                return $type;
            }
    
            if (!Arr::has($type, 'fieldset.default')) {
                return $type;
            }
    
            $tabs = array_reduce($type['fieldset']['default']['fields'], function ($carry, $v) {
                return array_merge($carry, [$v['title'] ?? null]);
            }, []);
    
            if (($index = array_search('Advanced', $tabs)) === false) {
                return $type;
            }
    
            $accessField = [
                'name' => '_safecoderaitools_prompt',
                'label' => Text::_('PLG_SYSTEM_SYOOSAFECODERAITOOLS_NEW_PROMPT_TOOL_LABEL'),
                'text' => Text::_('PLG_SYSTEM_SYOOSAFECODERAITOOLS_NEW_PROMPT_TOOL_TEXT'),
                'type' => 'button-panel',
                'panel' => 'safecoderaitools-prompt',
                'description' => Text::_('PLG_SYSTEM_SYOOSAFECODERAITOOLS_NEW_PROMPT_TOOL_LABEL_DESC'),
            ];
    
            $fields = $type['fieldset']['default']['fields'][$index]['fields'] ?? null;
    
            if (is_null($fields) || !is_array($fields)) {
                return $type;
            }
    
            Arr::splice($fields, ($fields[1] ?? '') === 'status' ? 2 : 1, 0, [$accessField]);
            $type['fieldset']['default']['fields'][$index]['fields'] = $fields;
    
            return $type;
        } catch (\Throwable $th) {
            return $type;
        }
        
    }
}

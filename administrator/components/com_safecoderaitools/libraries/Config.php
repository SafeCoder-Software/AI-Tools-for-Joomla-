<?php

/**
 * 
 * @package     SafeCoder AI Tools
 * @subpackage  com_safecoderaitools
 * 
 * @version     1.0.0
 * 
 * @author      Miron Savan <hello@safecoder.com>
 * @link        https://www.safecoder.com/aitools
 * @copyright   Copyright (C) 2012 SafeCoder Software SRL (RO30786660)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later; see LICENSE.txt
 * 
 */

namespace SafeCoderSoftwareAITools\Libraries;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class Config
{

    private $OpenAIAPIKey = '';
    private $OpenAIModel = '';
    private $OpenAIMaxTokens = 0;
    private $OpenAITemperature = 0;
    private $OpenAITopP = 0;
    private $OpenAIIterations = 0;
    private $OpenAIFrequencyPenalty = 0;
    private $OpenAIPresencePenalty = 0;

    private $PromptToolAdvancedOptions = 0;

    private $predefinedPromptsList = array();

    private $ContextArticleContentType = 0;
    private $ContextIncludeTitle = 0;
    private $ContextIncludeFieldContent = 0;
    private $ContextIncludeFieldBeforeContent = 0;
    private $ContextCharacterLimit = 0;
    private $ContextWordLimit = 0;
    private $ContextSentenceLimit = 0;

    private $ContextPreText = '';
    private $ContextPostText = '';
    private $UserInputPreText = '';
    private $UserInputPostText = '';

    private $GeneralTypeAnimationEnabled = 0;
    private $GeneralShowSelectedContext = 0;
    private $GeneralShowRequestResponse = 0;
    private $GeneralShowRequestDetails = 0;

    private $PluginShowCleanPrompt = 0;
    private $PluginShowPredefinedPrompt = 0;
    private $PluginPredefinedPromptMax = 0;
    private $PluginOpenInModal = 0;
    private $PluginShowHelpButton = 0;
    private $PluginPredefinedPromptListReversed = 0;
    private $PluginShowHistoryButton = 0;

    /**
     * 
     * Load component and content plugin config options
     * 
     * @return boolean
     * 
     */
    function __construct()
    {

        try {

            $params = ComponentHelper::getParams('com_safecoderaitools');

            $this->OpenAIAPIKey = \trim($params->get('OpenAIAPIKey', ''));
            $this->OpenAIModel = OpenAI::ProcessModelValue(\trim($params->get('OpenAIModel', 'text-davinci-003')));
            $this->OpenAIMaxTokens = OpenAI::ProcessMaxTokens($params->get('OpenAIMaxTokens', 2049));
            $this->OpenAITemperature = OpenAI::ProcessTemperatureValue($params->get('OpenAITemperature', 1));
            $this->OpenAITopP = OpenAI::ProcessTop_PValue($params->get('OpenAITopP', 1));
            $this->OpenAIIterations = OpenAI::ProcessNValue($params->get('OpenAIIterations', 1));
            $this->OpenAIFrequencyPenalty = OpenAI::ProcessFrequencyPenaltyValue($params->get('OpenAIFrequencyPenalty', 0));
            $this->OpenAIPresencePenalty = OpenAI::ProcessPresencePenaltyValue($params->get('OpenAIPresencePenalty', 0));

            $this->PromptToolAdvancedOptions = General::ProcessBooleanValue($params->get('PromptToolAdvancedOptions', 0));

            $promptsList = (array) $params->get('predefinedPromptsList');

            if (Utility::IsValidNonEmptyArray($promptsList) == true) {

                foreach ($promptsList as $promptItem) {

                    if (!\is_object($promptItem) || !\property_exists($promptItem, 'promptTitle') || !\property_exists($promptItem, 'promptContent')) {
                        continue;
                    }

                    $this->predefinedPromptsList[] = (array) $promptItem;
                }
            }

            $this->ContextArticleContentType = General::ProcessContextArticleContentType($params->get('ContextArticleContentType', 0));
            $this->ContextIncludeTitle = General::ProcessBooleanValue($params->get('ContextIncludeTitle', 1));
            $this->ContextIncludeFieldContent = General::ProcessBooleanValue($params->get('ContextIncludeFieldContent', 1));
            $this->ContextIncludeFieldBeforeContent = General::ProcessBooleanValue($params->get('ContextIncludeFieldBeforeContent', 0));
            $this->ContextCharacterLimit = General::ProcessPositiveNumberValue($params->get('ContextCharacterLimit', 0));
            $this->ContextWordLimit = General::ProcessPositiveNumberValue($params->get('ContextWordLimit', 0));
            $this->ContextSentenceLimit = General::ProcessBooleanValue($params->get('ContextSentenceLimit', 0));

            $this->ContextPreText = Utility::cleanString($params->get('ContextPreText', ''));
            $this->ContextPostText = Utility::cleanString($params->get('ContextPostText', ''));
            $this->UserInputPreText = Utility::cleanString($params->get('UserInputPreText', ''));
            $this->UserInputPostText = Utility::cleanString($params->get('UserInputPostText', ''));

            $this->GeneralTypeAnimationEnabled = General::ProcessBooleanValue($params->get('GeneralTypeAnimationEnabled', 1));
            $this->GeneralShowSelectedContext = General::ProcessBooleanValue($params->get('GeneralShowSelectedContext', 1));
            $this->GeneralShowRequestResponse = General::ProcessBooleanValue($params->get('GeneralShowRequestResponse', 1));
            $this->GeneralShowRequestDetails = General::ProcessBooleanValue($params->get('GeneralShowRequestDetails', 1));
        } catch (\Throwable $th) {
            //throw $th;
        }

        try {

            $plugin = PluginHelper::getPlugin('system', 'safecoderaitools');

            $pluginParams = null;
            if (\is_object($plugin)) {
                $pluginParams = new Registry($plugin->params);
            }

            if (!\is_object($pluginParams)) {
                $this->PluginShowCleanPrompt = 1;
                $this->PluginShowPredefinedPrompt = 1;
                $this->PluginPredefinedPromptMax = 3;
                $this->PluginOpenInModal = 1;
                $this->PluginShowHelpButton = 1;
                $this->PluginPredefinedPromptListReversed = 0;
            } else {
                $this->PluginShowCleanPrompt = General::ProcessBooleanValue($pluginParams->get('PluginShowCleanPrompt', 1));
                $this->PluginShowPredefinedPrompt = General::ProcessBooleanValue($pluginParams->get('PluginShowPredefinedPrompt', 1));
                $this->PluginPredefinedPromptMax = General::ProcessPositiveNumberValue($pluginParams->get('PluginPredefinedPromptMax', 3));
                $this->PluginOpenInModal = General::ProcessBooleanValue($pluginParams->get('PluginOpenInModal', 1));
                $this->PluginShowHelpButton = General::ProcessBooleanValue($pluginParams->get('PluginShowHelpButton', 1));
                $this->PluginPredefinedPromptListReversed = General::ProcessBooleanValue($pluginParams->get('PluginPredefinedPromptListReversed', 0));
                $this->PluginShowHistoryButton = General::ProcessBooleanValue($pluginParams->get('PluginShowHistoryButton', 1));
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return true;
    }

    /**
     * 
     * Return Open AI API Key
     *
     * @return string
     */
    public function getOpenAIAPIKey()
    {
        return (string) $this->OpenAIAPIKey;
    }

    /**
     * 
     * Return Open AI Temperature
     *
     * @return float
     */
    public function getOpenAITemperature()
    {
        return (float) $this->OpenAITemperature;
    }

    /**
     * 
     * Return Open AI Model
     *
     * @return string
     */
    public function getOpenAIModel()
    {
        return (string) $this->OpenAIModel;
    }

    /**
     * 
     * Return Open AI Max Tokens
     *
     * @return integer
     */
    public function getOpenAIMaxTokens()
    {
        return (int) $this->OpenAIMaxTokens;
    }

    /**
     * 
     * Return Open AI Top_P
     *
     * @return float
     */
    public function getOpenAITop_P()
    {
        return (float) $this->OpenAITopP;
    }

    /**
     * 
     * Return Open AI Iterations (N value)
     *
     * @return integer
     */
    public function getOpenAIIterations()
    {
        return (int) $this->OpenAIIterations;
    }

    /**
     * 
     * Return Open AI Frequency Penalty
     *
     * @return float
     */
    public function getOpenAIFrequencyPenalty()
    {
        return (float) $this->OpenAIFrequencyPenalty;
    }

    /**
     * 
     * Return Open AI Presence Penalty
     *
     * @return integer
     */
    public function getOpenAIPresencePenalty()
    {
        return (float) $this->OpenAIPresencePenalty;
    }

    /**
     * 
     * Return Prompt Tool Show/Hide Advanced Options
     *
     * @return integer
     */
    public function getPromptToolAdvancedOptions()
    {
        return (int) $this->PromptToolAdvancedOptions;
    }

    /**
     * 
     * Return Predefined Prompt List
     *
     * @return array
     */
    public function getPredefinedPromptsList()
    {
        return $this->predefinedPromptsList;
    }

    /**
     * 
     * Return content merge type
     *
     * @return integer
     */
    public function getContextArticleContentType()
    {
        return (int) $this->ContextArticleContentType;
    }

    /**
     * 
     * Return include title
     *
     * @return integer
     */
    public function getContextIncludeTitle()
    {
        return (int) $this->ContextIncludeTitle;
    }

    /**
     * 
     * Return include field content
     *
     * @return integer
     */
    public function getContextIncludeFieldContent()
    {
        return (int) $this->ContextIncludeFieldContent;
    }

    /**
     * 
     * Return include field content before main content
     *
     * @return integer
     */
    public function getContextIncludeFieldBeforeContent()
    {
        return (int) $this->ContextIncludeFieldBeforeContent;
    }

    /**
     * 
     * Return Context Characters Limit
     *
     * @return integer
     */
    public function getContextCharacterLimit()
    {
        return (int) $this->ContextCharacterLimit;
    }

    /**
     * 
     * Return Context Word Limit
     *
     * @return integer
     */
    public function getContextWordLimit()
    {
        return (int) $this->ContextWordLimit;
    }

    /**
     * 
     * Return Context Sentence Limit
     *
     * @return integer
     */
    public function getContextSentenceLimit()
    {
        return (int) $this->ContextSentenceLimit;
    }

    /**
     * 
     * Return Context PreText
     *
     * @return string
     */
    public function getContextPreText()
    {
        return (string) $this->ContextPreText;
    }

    /**
     * 
     * Return Context PostText
     *
     * @return string
     */
    public function getContextPostText()
    {
        return (string) $this->ContextPostText;
    }

    /**
     * 
     * Return User Input PreText
     *
     * @return string
     */
    public function getUserInputPreText()
    {
        return (string) $this->UserInputPreText;
    }

    /**
     * 
     * Return User Input PostText
     *
     * @return string
     */
    public function getUserInputPostText()
    {
        return (string) $this->UserInputPostText;
    }

    /**
     * 
     * Return Type Animation Setting
     *
     * @return integer
     */
    public function getGeneralTypeAnimationEnabled()
    {
        return (int) $this->GeneralTypeAnimationEnabled;
    }

    /**
     * 
     * Return Selected Context Setting
     *
     * @return integer
     */
    public function getGeneralShowSelectedContext()
    {
        return (int) $this->GeneralShowSelectedContext;
    }

    /**
     * 
     * Return Request Response Setting
     *
     * @return integer
     */
    public function getGeneralShowRequestResponse()
    {
        return (int) $this->GeneralShowRequestResponse;
    }

    /**
     * 
     * Return Request Details Setting
     *
     * @return integer
     */
    public function getGeneralShowRequestDetails()
    {
        return (int) $this->GeneralShowRequestDetails;
    }

    /**
     * 
     * System Plugin - Show Clean Prompt (0/1)
     *
     * @return integer
     */
    public function getPluginShowCleanPrompt()
    {
        return (int) $this->PluginShowCleanPrompt;
    }

    /**
     * 
     * System Plugin - Show Predefined Prompt List (0/1)
     *
     * @return integer
     */
    public function getPluginShowPredefinedPrompt()
    {
        return (int) $this->PluginShowPredefinedPrompt;
    }

    /**
     * 
     * System Plugin - Predefined number of prompts displayed
     *
     * @return integer
     */
    public function getPluginPredefinedPromptMax()
    {
        return (int) $this->PluginPredefinedPromptMax;
    }

    /**
     * 
     * System Plugin - Show Open Prompt In Modal (0/1)
     *
     * @return integer
     */
    public function getPluginOpenInModal()
    {
        return (int) $this->PluginOpenInModal;
    }

    /**
     * 
     * System Plugin - Show Help Button (0/1)
     *
     * @return integer
     */
    public function getPluginShowHelpButton()
    {
        return (int) $this->PluginShowHelpButton;
    }

    /**
     * 
     * System Plugin - Prompt List reversed (0/1)
     *
     * @return integer
     */
    public function getPluginPredefinedPromptListReversed()
    {
        return (int) $this->PluginPredefinedPromptListReversed;
    }

    /**
     * 
     * System Plugin - Show History Button (0/1)
     *
     * @return integer
     */
    public function getPluginShowHistoryButton() {
        return (int) $this->PluginShowHistoryButton;
    }
}

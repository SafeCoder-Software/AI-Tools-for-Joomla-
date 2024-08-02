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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use SafeCoderSoftwareAITools\Libraries\Config;
use SafeCoderSoftwareAITools\Libraries\ContextDetails;
use SafeCoderSoftwareAITools\Libraries\General;
use SafeCoderSoftwareAITools\Libraries\OpenAI;
use SafeCoderSoftwareAITools\Libraries\PromptData;
use SafeCoderSoftwareAITools\Libraries\Utility;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SafeCoderAIToolsControllerCreate extends BaseController
{

    /**
     * Create new promt
     *
     * @return void
     */
    public function New()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        try {

            General::SetHeader();

            $CategoryProp = '';
            $ArticleProp = '';

            $question = (string) $app->input->get('contentValue', '', 'STRING');

            $ArticleID = (int) $app->input->get('ArticleID', 0, 'INT');
            if (Utility::isValidPositiveNumber($ArticleID) != true) {
                $ArticleID = 0;
            } else {
                $ArticleProp = '&ArticleID=' . $ArticleID;
            }


            $CategoryID = (int) $app->input->get('CategoryID', 0, 'INT');
            if (Utility::isValidPositiveNumber($CategoryID) != true) {
                $CategoryID = 0;
            } else {
                $CategoryProp = '&CategoryID=' . $CategoryID;
            }

            $IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
            if (Utility::isValidPositiveNumber($IsLightbox) == 1) {
                $IsLightbox = '&IsLightbox=1';
            } else {
                $IsLightbox = '';
            }

            $config = new Config();

            if (empty($config->getOpenAIAPIKey())) {
                $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_OPEN_AI_API_KEY_EMPTY'), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create' . $ArticleProp . $CategoryProp . $IsLightbox);
                return;
            }

            $user = $app->getIdentity();

            if (!is_object($user) || $user->guest == 1 || Utility::isValidPositiveNumber($user->id) != true) {
                $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_AUTH'), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create' . $ArticleProp . $CategoryProp . $IsLightbox);
                return;
            }

            $OpenAIModel = $config->getOpenAIModel();
            $OpenAIMaxTokens = $config->getOpenAIMaxTokens();
            $OpenAITemperature = $config->getOpenAITemperature();
            $OpenAITop_P = $config->getOpenAITop_P();
            $OpenAIIterations = $config->getOpenAIIterations();
            $OpenAIPresencePenalty = $config->getOpenAIPresencePenalty();
            $OpenAIFrequencyPenalty = $config->getOpenAIFrequencyPenalty();

            if ($config->getPromptToolAdvancedOptions() == 1) {

                $OpenAIModel = (string) trim($app->input->get('OpenAIModel', 'gpt-3.5-turbo-16k', 'STRING'));
                $OpenAIModel = OpenAI::ProcessModelValue($OpenAIModel);

                $OpenAIMaxTokens = (int) trim($app->input->get('OpenAIMaxTokens', 2049, 'INT'));
                if (Utility::isValidPositiveNumber($OpenAIMaxTokens) != true) {
                    $OpenAIMaxTokens = $config->getOpenAIMaxTokens();
                } else {
                    $OpenAIMaxTokens = OpenAI::ProcessMaxTokens($OpenAIMaxTokens, $OpenAIModel);
                }

                $OpenAITemperature = (float) trim($app->input->get('OpenAITemperature', 1, 'FLOAT'));
                $OpenAITemperature = OpenAI::ProcessTemperatureValue($OpenAITemperature);

                $OpenAITop_P = (float) trim($app->input->get('OpenAITop_P', 1, 'FLOAT'));
                $OpenAITop_P = OpenAI::ProcessTop_PValue($OpenAITop_P);

                $OpenAIIterations = (int) trim($app->input->get('OpenAIIterations', 1, 'INT'));
                if (Utility::isValidPositiveNumber($OpenAIIterations) != true) {
                    $OpenAIIterations = $config->getOpenAIIterations();
                } else {
                    $OpenAIIterations = OpenAI::ProcessNValue($OpenAIIterations);
                }

                $OpenAIPresencePenalty = (float) trim($app->input->get('OpenAIPresencePenalty', 0, 'FLOAT'));
                $OpenAIPresencePenalty = OpenAI::ProcessPresencePenaltyValue($OpenAIPresencePenalty);

                $OpenAIFrequencyPenalty = (float) trim($app->input->get('OpenAIFrequencyPenalty', 0, 'FLOAT'));
                $OpenAIFrequencyPenalty = OpenAI::ProcessFrequencyPenaltyValue($OpenAIFrequencyPenalty);
            }

            if (empty($question)) {
                $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_QUESTION_EMPTY'), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create' . $ArticleProp . $CategoryProp . $IsLightbox);
                return;
            }

            $context = new ContextDetails();
            $context->ArticleID = $ArticleID;
            $context->CategoryID = $CategoryID;
            $context->Load();

            $ContextString = General::PrepareContextString($context, $config);

            $PromptDataObj = new PromptData();
            $PromptDataObj->ArticleID = $ArticleID;
            $PromptDataObj->CategoryID = $CategoryID;
            $PromptDataObj->UserID = $user->id;
            $PromptDataObj->FullName = $user->name;
            $PromptDataObj->UserInput = $question;
            $PromptDataObj->OpenAIModel = $OpenAIModel;
            $PromptDataObj->OpenAIMaxTokens = $OpenAIMaxTokens;
            $PromptDataObj->OpenAITemperature = $OpenAITemperature;
            $PromptDataObj->OpenAITop_P = $OpenAITop_P;
            $PromptDataObj->OpenAIIterations = $OpenAIIterations;
            $PromptDataObj->OpenAIPresencePenalty = $OpenAIPresencePenalty;
            $PromptDataObj->OpenAIFrequencyPenalty = $OpenAIFrequencyPenalty;
            $PromptDataObj->PromptContext = $ContextString;
            $PromptDataObj->CreateFullPrompt($config);

            $PromptDataObj->Save();

            if (Utility::isValidPositiveNumber($PromptDataObj->id) != true) {
                $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_SAVE_PROMPT'), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create' . $ArticleProp . $CategoryProp . $IsLightbox);
                return;
            }


            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create&PromptID=' . $PromptDataObj->id . $IsLightbox);
            return;
        } catch (\Throwable $th) {
            $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_INTERNAL_SERVER_ERROR'), 'error');
            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create' . $ArticleProp . $CategoryProp . $IsLightbox);
            return;
        }
    }

    /**
     * Process/Complete Ai Request
     *
     * @return void
     */
    public function CompleteRequest()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        try {

            General::SetHeader();

            $user = $app->getIdentity();

            if (!is_object($user) || $user->guest == 1 || Utility::isValidPositiveNumber($user->id) != true) {
                echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_AUTH'), true, true);
                $app->close();
            }

            $PromptID = (int) $app->input->get('PromptID', 0, 'INT');
            if (Utility::isValidPositiveNumber($PromptID) != true) {
                echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_PROMPT_ID_MISSING'), true, true);
                $app->close();
            }

            $PromptDataObj = new PromptData($PromptID);

            if ($PromptDataObj->id != $PromptID || Utility::isValidPositiveNumber($PromptDataObj->id) != true) {
                echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_PROMPT_NOT_FOUND'), true, true);
                $app->close();
            }

            if ($PromptDataObj->IsProcessedValue == true) {
                echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_PROMPT_ALREADY_PROCESSED'), true, true);
                $app->close();
            }

            if (empty($PromptDataObj->FullPrompt)) {
                $PromptDataObj->IsProcessed = 1;
                $PromptDataObj->Save();
                echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_PROMPT_EMPTY'), true, true);
                $app->close();
            }

            $config = new Config();

            if (empty($config->getOpenAIAPIKey())) {
                $PromptDataObj->IsProcessed = 1;
                $PromptDataObj->Save();
                echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_OPEN_AI_API_KEY_EMPTY'), true, true);
                $app->close();
            }

            $PromptResponse = OpenAI::processRequest($PromptDataObj, $config);
            if (!is_bool($PromptResponse)) {
                $PromptDataObj->IsProcessed = 1;
                $PromptDataObj->Save();
                $PromptResponse = false;
            }

            if ($PromptResponse != true) {
                $PromptDataObj->IsProcessed = 1;
                $PromptDataObj->Save();
                echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_OPEN_AI_RESPONSE_NOK'), true, true);
                $app->close();
            }

            $PromptDataObj->Load($PromptDataObj->id);
            $PromptDataObj->UpdateValuesFromResponse();

            echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_OPEN_AI_RESPONSE_OK'), false, true);
            $app->close();
        } catch (\Throwable $th) {
            echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_INTERNAL_SERVER_ERROR'), true, true);
            $app->close();
        }
    }

    /**
     * Cancel request on some error
     *
     * @return void
     */
    public function CancelRequest()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        try {

            General::SetHeader();

            $user = $app->getIdentity();

            if (!is_object($user) || $user->guest == 1 || Utility::isValidPositiveNumber($user->id) != true) {
                throw new Exception(Text::_('COM_SAFECODERAITOOLS_ERROR_AUTH'), 500);
            }

            $PromptID = (int) $app->input->get('PromptID', 0, 'INT');
            if (Utility::isValidPositiveNumber($PromptID) != true) {
                throw new Exception(Text::_('COM_SAFECODERAITOOLS_ERROR_PROMPT_ID_MISSING'), 500);
            }

            $PromptDataObj = new PromptData($PromptID);

            if ($PromptDataObj->id != $PromptID || Utility::isValidPositiveNumber($PromptDataObj->id) != true) {
                throw new Exception(Text::_('COM_SAFECODERAITOOLS_ERROR_PROMPT_NOT_FOUND'), 500);
            }

            $ErrorMsg = $app->input->get('ErrorMsg', '', 'STRING');
            if (Utility::isJson($ErrorMsg)) {
                $ErrorMsg = json_decode($ErrorMsg);
            }

            if (Utility::IsValidNonEmptyArray($ErrorMsg)) {

                $code = 0;
                if (array_key_exists(0, $ErrorMsg)) {
                    $code = $ErrorMsg[0];
                }

                $msg = '';
                if (array_key_exists(1, $ErrorMsg)) {
                    $msg = $ErrorMsg[1];
                }

                $type = "server_error";

                $PromptDataObj->RawResponse = General::BuildErrorArrayString($msg, $type, $code);
            }

            $PromptDataObj->IsProcessed = 1;
            $PromptDataObj->Save();

            echo new JsonResponse(null, Text::_('COM_SAFECODERAITOOLS_ERROR_PROMPT_CANCELLED'), true, true);
            $app->close();
        } catch (\Throwable $th) {
            throw new Exception(Text::_('COM_SAFECODERAITOOLS_ERROR_INTERNAL_SERVER_ERROR'), 500);
        }
    }

    /**
     * Clear Prompt and start over with context
     *
     * @return void
     */
    public function StartOver()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        General::SetHeader();

        $ArticleProp = '';
        $CategoryProp = '';
        $IsLightbox = '';

        $ArticleID = (int) $app->input->get('ArticleID', 0, 'INT');
        if (Utility::isValidPositiveNumber($ArticleID) != true) {
            $ArticleID = 0;
        } else {
            $ArticleProp = '&ArticleID=' . $ArticleID;
        }

        $CategoryID = (int) $app->input->get('CategoryID', 0, 'INT');
        if (Utility::isValidPositiveNumber($CategoryID) != true) {
            $CategoryID = 0;
        } else {
            $CategoryProp = '&CategoryID=' . $CategoryID;
        }

        $IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
        if (Utility::isValidPositiveNumber($IsLightbox) == 1) {
            $IsLightbox = '&IsLightbox=1';
        } else {
            $IsLightbox = '';
        }

        $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_SUCCESS_PROMPT_CLEAR'), 'success');
        $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create' . $ArticleProp . $CategoryProp . $IsLightbox);
        return;
    }

    /**
     * Clear prompt / Clear context
     *
     * @return void
     */
    public function ClearSelection()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        General::SetHeader();

        $IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
        if (Utility::isValidPositiveNumber($IsLightbox) == 1) {
            $IsLightbox = '&IsLightbox=1';
        } else {
            $IsLightbox = '';
        }

        $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_SUCCESS_PROMPT_CLEAR'), 'success');
        $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create' . $IsLightbox);
        return;
    }

    /**
     * Go to history page [to be imporved in the future]
     *
     * @return void
     */
    public function viewHistory()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        General::SetHeader();

        $IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
        if ($IsLightbox == 1) {
            $IsLightbox = '&IsLightbox=1';
        } else {
            $IsLightbox = '';
        }

        $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=history' . $IsLightbox);
        return;
    }
}

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
use SafeCoderSoftwareAITools\Libraries\Config;
use SafeCoderSoftwareAITools\Libraries\General;
use SafeCoderSoftwareAITools\Libraries\OpenAI;
use SafeCoderSoftwareAITools\Libraries\PromptData;
use SafeCoderSoftwareAITools\Libraries\Utility;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SafeCoderAIToolsControllerDisplay extends BaseController
{

    /**
     * Remove item from the database
     *
     * @return void
     */
    public function Remove()
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

        $PromptID = $app->input->get('PromptID', 0, 'INT');
        if (Utility::isValidPositiveNumber($PromptID) != true) {
            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=history' . $IsLightbox);
            return;
        }

        try {

            $promptObj = new PromptData($PromptID);
            $promptObj->Delete();

            $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_REMOVE_SINGLE_OK'), 'success');
            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=history' . $IsLightbox);
            return;
        } catch (\Throwable $th) {
            $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_REMOVE_SINGLE_ERROR'), 'error');
            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=history' . $IsLightbox);
            return;
        }
    }

    /**
     * Duplicate prompt and run it again
     *
     * @return void
     */
    public function RunAgain()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        try {

            General::SetHeader();

            $PromptProp = '';

            $IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
            if (Utility::isValidPositiveNumber($IsLightbox) == 1) {
                $IsLightbox = '&IsLightbox=1';
            } else {
                $IsLightbox = '';
            }

            $PromptID = $app->input->get('PromptID', 0, 'INT');
            if (Utility::isValidPositiveNumber($PromptID) != true) {
                $app->enqueueMessage(Text::_(''), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=history' . $IsLightbox);
                return;
            } else {
                $PromptProp = '&PromptID=' . $PromptID;
            }

            $config = new Config();

            if (empty($config->getOpenAIAPIKey())) {
                $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_OPEN_AI_API_KEY_EMPTY'), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=display' . $PromptProp . $IsLightbox);
                return;
            }

            $user = $app->getIdentity();

            if (!is_object($user) || $user->guest == 1 || Utility::isValidPositiveNumber($user->id) != true) {
                $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_AUTH'), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=display' . $PromptProp . $IsLightbox);
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

            $PromptDataObj = new PromptData($PromptID);
            if (Utility::isValidPositiveNumber($PromptDataObj->id) != true || $PromptDataObj->id != $PromptID) {
                $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_PROMPT_404_MSG'), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=diplay' . $PromptProp . $IsLightbox);
                return;
            }

            $PromptDataObj->id = 0;
            $PromptDataObj->UserID = $user->id;
            $PromptDataObj->FullName = $user->name;
            $PromptDataObj->OpenAIModel = $OpenAIModel;
            $PromptDataObj->OpenAIMaxTokens = $OpenAIMaxTokens;
            $PromptDataObj->OpenAITemperature = $OpenAITemperature;
            $PromptDataObj->OpenAITop_P = $OpenAITop_P;
            $PromptDataObj->OpenAIIterations = $OpenAIIterations;
            $PromptDataObj->OpenAIPresencePenalty = $OpenAIPresencePenalty;
            $PromptDataObj->OpenAIFrequencyPenalty = $OpenAIFrequencyPenalty;
            $PromptDataObj->IsProcessed = 0;
            $PromptDataObj->IsProcessedValue = false;
            $PromptDataObj->IsOK = 0;
            $PromptDataObj->IsOKValue = false;
            $PromptDataObj->Save();

            if (Utility::isValidPositiveNumber($PromptDataObj->id) != true) {
                $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_SAVE_PROMPT'), 'error');
                $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=display' . $PromptProp . $IsLightbox);
                return;
            }


            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create&PromptID=' . $PromptDataObj->id . $IsLightbox);
            return;
        } catch (\Throwable $th) {
            $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_INTERNAL_SERVER_ERROR'), 'error');
            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=display' . $PromptProp . $IsLightbox);
            return;
        }
    }
}

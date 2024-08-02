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

namespace SafeCoderSoftwareAITools\Libraries;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

class PromptData
{

    public $id = 0;

    public $ArticleID = 0;
    public $CategoryID = 0;

    public $Type = 0;
    public $TypeName = '';

    public $UserID = 0;
    public $FullName = '';

    public $UserInput = '';

    public $RawResponse = '';
    public $RawResponseArr = null;

    public $IsProcessed = 0;
    public $IsProcessedValue = false;

    public $IsOK = 0;
    public $IsOKValue = false;

    public $CompletionID = '';
    public $CompletionModel = '';
    public $CompletionPromptTokens = 0;
    public $CompletionTokens = 0;
    public $CompletionTotalTokens = 0;

    public $OpenAIModel = '';
    public $OpenAIMaxTokens = 0;
    public $OpenAITemperature = 0;
    public $OpenAITop_P = 0;
    public $OpenAIIterations = 0;
    public $OpenAIPresencePenalty = 0;
    public $OpenAIFrequencyPenalty = 0;

    public $PromptContext = '';
    public $FullPrompt = '';

    public $Date = '';
    public $DateUpdated = '';

    public $TimeDifference = '0.00';

    public $ChoiceList = array();

    public $ErrorMessage = '';

    public $ErrorType = '';
    public $ErrorTypeValue = '';

    public $ErrorParam = '';
    public $ErrorCode = '';

    /**
     * 
     * Load prompt
     *
     * @param integer $id
     * 
     * @return boolean
     */
    function __construct($id = 0)
    {

        try {

            if (Utility::isValidPositiveNumber($id) == true) {
                $this->Load($id);
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Process current values
     *
     * @return boolean
     */
    public function ProcessValues()
    {
        $this->FormatValues();

        return true;
    }

    /**
     * Load Prompt Values / Pupulate
     *
     * @param integer $id
     * @return boolean
     */
    public function Load($id = 0)
    {

        try {

            if (Utility::isValidPositiveNumber($id) != true) {
                return false;
            }

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $columns = array();
            $columns[] = 'id';
            $columns[] = 'ArticleID';
            $columns[] = 'CategoryID';
            $columns[] = 'UserID';
            $columns[] = 'FullName';
            $columns[] = 'UserInput';
            $columns[] = 'RawResponse';
            $columns[] = 'IsProcessed';
            $columns[] = 'IsOK';
            $columns[] = 'CompletionID';
            $columns[] = 'CompletionModel';
            $columns[] = 'CompletionPromptTokens';
            $columns[] = 'CompletionTokens';
            $columns[] = 'CompletionTotalTokens';
            $columns[] = 'OpenAIModel';
            $columns[] = 'OpenAIMaxTokens';
            $columns[] = 'OpenAITemperature';
            $columns[] = 'OpenAITop_P';
            $columns[] = 'OpenAIIterations';
            $columns[] = 'OpenAIPresencePenalty';
            $columns[] = 'OpenAIFrequencyPenalty';
            $columns[] = 'PromptContext';
            $columns[] = 'FullPrompt';
            $columns[] = 'Date';
            $columns[] = 'DateUpdated';

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select($db->quoteName($columns));
            $query->from($db->quoteName(General::$PromptTable));
            $query->where($db->quoteName('id') . ' = ' . $db->quote($id));
            $db->setQuery($query);
            $result = $db->loadAssoc();

            if (Utility::IsValidNonEmptyArray($result, 'id') != true) {
                return false;
            }

            foreach ($result as $resultKey => $resultValue) {

                if (\property_exists($this, $resultKey)) {
                    $this->$resultKey = $resultValue;
                }
            }

            $this->FormatValues();

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Load prompt response choices
     *
     * @return boolean
     */
    public function LoadChoices()
    {

        try {

            if (Utility::isValidPositiveNumber($this->id) != true) {
                return false;
            }

            $columns = array();
            $columns[] = 'PromptID';
            $columns[] = 'Text';
            $columns[] = 'Index';
            $columns[] = 'FinishReason';

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select($db->quoteName($columns));
            $query->from($db->quoteName(General::$PromptChoicesTable));
            $query->where($db->quoteName('PromptID') . ' = ' . $db->quote($this->id));
            $query->order($db->quoteName('Index') . ' ASC');
            $db->setQuery($query);
            $list = $db->loadAssocList();

            if (Utility::IsValidNonEmptyArray($list) != true) {
                return false;
            }

            foreach ($list as $lItem) {

                if (Utility::IsValidNonEmptyArray($lItem, 'PromptID') != true) {
                    continue;
                }

                $lItemObj = Utility::ArrayValuesToObject($lItem, new PromptChoice());

                if ($lItemObj->PromptID != $this->id) {
                    continue;
                }

                if (empty($lItemObj->Text)) {
                    continue;
                }

                $this->ChoiceList[] = $lItemObj;
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Insert / Update values
     *
     * @return boolean
     */
    public function Save()
    {

        try {

            $this->FormatValues();

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            if (Utility::isValidPositiveNumber($this->id) != true) {

                $columns = array();
                $columns[] = 'ArticleID';
                $columns[] = 'CategoryID';
                $columns[] = 'UserID';
                $columns[] = 'FullName';
                $columns[] = 'UserInput';
                $columns[] = 'RawResponse';
                $columns[] = 'IsProcessed';
                $columns[] = 'IsOK';
                $columns[] = 'CompletionID';
                $columns[] = 'CompletionModel';
                $columns[] = 'CompletionPromptTokens';
                $columns[] = 'CompletionTokens';
                $columns[] = 'CompletionTotalTokens';
                $columns[] = 'OpenAIModel';
                $columns[] = 'OpenAIMaxTokens';
                $columns[] = 'OpenAITemperature';
                $columns[] = 'OpenAITop_P';
                $columns[] = 'OpenAIIterations';
                $columns[] = 'OpenAIPresencePenalty';
                $columns[] = 'OpenAIFrequencyPenalty';
                $columns[] = 'PromptContext';
                $columns[] = 'FullPrompt';

                $values = array();
                $values[] = $db->quote($this->ArticleID);
                $values[] = $db->quote($this->CategoryID);
                $values[] = $db->quote($this->UserID);
                $values[] = $db->quote($this->FullName);
                $values[] = $db->quote($this->UserInput);
                $values[] = $db->quote($this->RawResponse);
                $values[] = $db->quote($this->IsProcessed);
                $values[] = $db->quote($this->IsOK);
                $values[] = $db->quote($this->CompletionID);
                $values[] = $db->quote($this->CompletionModel);
                $values[] = $db->quote($this->CompletionPromptTokens);
                $values[] = $db->quote($this->CompletionTokens);
                $values[] = $db->quote($this->CompletionTotalTokens);
                $values[] = $db->quote($this->OpenAIModel);
                $values[] = $db->quote($this->OpenAIMaxTokens);
                $values[] = $db->quote($this->OpenAITemperature);
                $values[] = $db->quote($this->OpenAITop_P);
                $values[] = $db->quote($this->OpenAIIterations);
                $values[] = $db->quote($this->OpenAIPresencePenalty);
                $values[] = $db->quote($this->OpenAIFrequencyPenalty);
                $values[] = $db->quote($this->PromptContext);
                $values[] = $db->quote($this->FullPrompt);

                /** @var \Joomla\Database\Query\MysqliQuery $query */
                $query = $db->getQuery(true);
                $query->insert($db->quoteName(General::$PromptTable));
                $query->columns($db->quoteName($columns));
                $query->values(\implode(', ', $values));
                $db->setQuery($query);
                $db->execute();

                $ins = $db->insertid();
                if (Utility::isValidPositiveNumber($ins) != true) {
                    return false;
                } else {
                    $this->id = $ins;
                    return true;
                }
            } else {

                /** @var \Joomla\Database\Query\MysqliQuery $query */
                $query = $db->getQuery(true);
                $query->update($db->quoteName(General::$PromptTable));

                $query->set($db->quoteName('ArticleID') . ' = ' . $db->quote($this->ArticleID));
                $query->set($db->quoteName('CategoryID') . ' = ' . $db->quote($this->CategoryID));
                $query->set($db->quoteName('UserID') . ' = ' . $db->quote($this->UserID));
                $query->set($db->quoteName('FullName') . ' = ' . $db->quote($this->FullName));
                $query->set($db->quoteName('UserInput') . ' = ' . $db->quote($this->UserInput));
                $query->set($db->quoteName('RawResponse') . ' = ' . $db->quote($this->RawResponse));
                $query->set($db->quoteName('IsProcessed') . ' = ' . $db->quote($this->IsProcessed));
                $query->set($db->quoteName('IsOK') . ' = ' . $db->quote($this->IsOK));
                $query->set($db->quoteName('CompletionID') . ' = ' . $db->quote($this->CompletionID));
                $query->set($db->quoteName('CompletionModel') . ' = ' . $db->quote($this->CompletionModel));
                $query->set($db->quoteName('CompletionPromptTokens') . ' = ' . $db->quote($this->CompletionPromptTokens));
                $query->set($db->quoteName('CompletionTokens') . ' = ' . $db->quote($this->CompletionTokens));
                $query->set($db->quoteName('CompletionTotalTokens') . ' = ' . $db->quote($this->CompletionTotalTokens));
                $query->set($db->quoteName('OpenAIModel') . ' = ' . $db->quote($this->OpenAIModel));
                $query->set($db->quoteName('OpenAIMaxTokens') . ' = ' . $db->quote($this->OpenAIMaxTokens));
                $query->set($db->quoteName('OpenAITemperature') . ' = ' . $db->quote($this->OpenAITemperature));
                $query->set($db->quoteName('OpenAITop_P') . ' = ' . $db->quote($this->OpenAITop_P));
                $query->set($db->quoteName('OpenAIIterations') . ' = ' . $db->quote($this->OpenAIIterations));
                $query->set($db->quoteName('OpenAIPresencePenalty') . ' = ' . $db->quote($this->OpenAIPresencePenalty));
                $query->set($db->quoteName('OpenAIFrequencyPenalty') . ' = ' . $db->quote($this->OpenAIFrequencyPenalty));
                $query->set($db->quoteName('PromptContext') . ' = ' . $db->quote($this->PromptContext));
                $query->set($db->quoteName('FullPrompt') . ' = ' . $db->quote($this->FullPrompt));

                $query->where($db->quoteName('id') . ' = ' . $db->quote($this->id));

                $db->setQuery($query);
                $db->execute();

                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Remove prompt from database
     *
     * @return boolean
     */
    public function Delete()
    {

        try {

            if (Utility::isValidPositiveNumber($this->id) != true) {
                return false;
            }

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->delete($db->quoteName(General::$PromptTable));
            $query->where($db->quoteName('id') . ' = ' . $db->quote($this->id));
            $db->setQuery($query);
            $db->execute();

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->delete($db->quoteName(General::$PromptChoicesTable));
            $query->where($db->quoteName('PromptID') . ' = ' . $db->quote($this->id));
            $db->setQuery($query);
            $db->execute();

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Update completion + choices
     *
     * @return boolean
     */
    public function UpdateValuesFromResponse()
    {

        try {

            if (Utility::isValidPositiveNumber($this->id) != true) {
                return false;
            }

            if (Utility::IsValidNonEmptyArray($this->RawResponseArr, 'id') != true) {
                return false;
            }

            $this->CompletionID = $this->RawResponseArr['id'];

            if (Utility::IsValidNonEmptyArray($this->RawResponseArr, 'model')) {
                $this->CompletionModel = $this->RawResponseArr['model'];
            }

            if (Utility::IsValidNonEmptyArray($this->RawResponseArr, 'usage')) {

                if (Utility::IsValidNonEmptyArray($this->RawResponseArr['usage'], 'prompt_tokens')) {
                    $this->CompletionPromptTokens = $this->RawResponseArr['usage']['prompt_tokens'];
                }

                if (Utility::IsValidNonEmptyArray($this->RawResponseArr['usage'], 'completion_tokens')) {
                    $this->CompletionTokens = $this->RawResponseArr['usage']['completion_tokens'];
                }

                if (Utility::IsValidNonEmptyArray($this->RawResponseArr['usage'], 'total_tokens')) {
                    $this->CompletionTotalTokens = $this->RawResponseArr['usage']['total_tokens'];
                }
            }

            $this->Save();

            if (Utility::IsValidNonEmptyArray($this->RawResponseArr, 'choices')) {

                $textList = $this->RawResponseArr['choices'];

                if (Utility::IsValidNonEmptyArray($textList)) {

                    foreach ($textList as $textItem) {

                        $choiceObj = new PromptChoice();
                        $choiceObj->PromptID = $this->id;

                        if (Utility::IsValidNonEmptyArray($textItem, 'text')) {
                            $choiceObj->Text = $textItem['text'];
                        }
  
                        if (Utility::IsValidNonEmptyArray($textItem, 'index')) {
                            $choiceObj->Index = $textItem['index'];
                        }
       
                        if (Utility::IsValidNonEmptyArray($textItem, 'finish_reason')) {
                            $choiceObj->FinishReason = $textItem['finish_reason'];
                        }
                        
                        $choiceObj->Insert();
                    }
                }
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Build Full Prompt Text
     *
     * @param [Config] $config
     * @return boolean
     */
    public function CreateFullPrompt($config = null)
    {

        try {

            if (!$config instanceof Config) {
                return false;
            }

            $fullPromptArr = array();

            if (!empty($this->PromptContext)) {
                $fullPromptArr[] = $this->PromptContext;
            }

            if (!empty($this->UserInput)) {

                $UserInputString = $this->UserInput;
                if (!empty($config->getUserInputPreText())) {
                    $UserInputString = $config->getUserInputPreText() . "\n" . $UserInputString;
                }

                if (!empty($config->getUserInputPostText())) {
                    $UserInputString = $UserInputString . "\n" . $config->getUserInputPostText();
                }

                if (!empty($UserInputString)) {
                    $fullPromptArr[] = $UserInputString;
                }
            }

            if (Utility::IsValidNonEmptyArray($fullPromptArr) != true) {
                return false;
            }

            $fullPromptString = \implode("\n", $fullPromptArr);
            if (empty($fullPromptString)) {
                return '';
            }

            $this->FullPrompt = $fullPromptString;

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Format prompt values
     *
     * @return boolean
     */
    private function FormatValues()
    {

        try {

            $this->id = (int) \trim(\strip_tags($this->id));
            if (Utility::isValidPositiveNumber($this->id) != true) {
                $this->id = 0;
            }

            $this->ArticleID = (int) \trim(\strip_tags($this->ArticleID));
            if (Utility::isValidPositiveNumber($this->ArticleID) != true) {
                $this->ArticleID = 0;
            }

            $this->CategoryID = (int) \trim(\strip_tags($this->CategoryID));
            if (Utility::isValidPositiveNumber($this->CategoryID) != true) {
                $this->CategoryID = 0;
            }

            if (Utility::isValidPositiveNumber($this->ArticleID) == true) {
                $this->Type = 1;
                $this->TypeName = Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_ARTICLE');
            } else if (Utility::isValidPositiveNumber($this->CategoryID) == true) {
                $this->Type = 2;
                $this->TypeName = Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_CATEGORY');
            } else {
                $this->Type = 0;
                $this->TypeName = Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_NONE');
            }

            $this->UserID = (int) \trim(\strip_tags($this->UserID));
            if (Utility::isValidPositiveNumber($this->UserID) != true) {
                $this->UserID = 0;
            } else {
                if (empty($this->FullName)) {
                    $this->FullName = Utility::getNameFromUserID($this->UserID);
                }
            }

            $this->UserInput = (string) \trim(\strip_tags($this->UserInput));

            $this->RawResponse = (string) \trim($this->RawResponse);
            if (Utility::isJson($this->RawResponse)) {
                $this->RawResponseArr = \json_decode($this->RawResponse, true);
            } else {
                $this->RawResponseArr = null;
            }

            $this->ErrorMessage = Text::_('COM_SAFECODERAITOOLS_API_ERROR_UNKNOWN');
            $this->ErrorType = Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE');
            $this->ErrorTypeValue = Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE');

            if (Utility::IsValidNonEmptyArray($this->RawResponseArr, 'error') == true) {

                $errorDetails = $this->RawResponseArr['error'];

                if (Utility::IsValidNonEmptyArray($errorDetails, 'message') == true) {
                    $this->ErrorMessage = $errorDetails['message'];
                }

                if (Utility::IsValidNonEmptyArray($errorDetails, 'type') == true) {
                    $this->ErrorType = $errorDetails['type'];
                }

                if ($this->ErrorType != Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE')) {
                    $this->ErrorTypeValue = \ucwords(\str_replace('_', ' ', $this->ErrorType));
                }

                if (Utility::IsValidNonEmptyArray($errorDetails, 'code') == true) {
                    $this->ErrorCode = $errorDetails['code'];
                }

                if (empty($this->ErrorMessage) && Utility::isValidPositiveNumber($this->ErrorCode) == true) {
                    $this->ErrorMessage = Utility::ReturnResponseMessageBasedOnCode($this->ErrorCode);
                }
            }

            if (Utility::isValidPositiveNumber($this->IsProcessed) == true) {
                $this->IsProcessed = 1;
                $this->IsProcessedValue = true;
            } else {
                $this->IsProcessed = 0;
                $this->IsProcessedValue = false;
            }

            if (Utility::isValidPositiveNumber($this->IsOK) == true) {
                $this->IsOK = 1;
                $this->IsOKValue = true;
            } else {
                $this->IsOK = 0;
                $this->IsOKValue = false;
            }

            $this->CompletionID = (string) \trim(\strip_tags($this->CompletionID));
            $this->CompletionModel = (string) \trim(\strip_tags($this->CompletionModel));

            $this->CompletionPromptTokens = (int) \trim(\strip_tags($this->CompletionPromptTokens));
            if (Utility::isValidPositiveNumber($this->CompletionPromptTokens) != true) {
                $this->CompletionPromptTokens = 0;
            }

            $this->CompletionTokens = (int) \trim(\strip_tags($this->CompletionTokens));
            if (Utility::isValidPositiveNumber($this->CompletionTokens) != true) {
                $this->CompletionTokens = 0;
            }

            $this->CompletionTotalTokens = (int) \trim(\strip_tags($this->CompletionTotalTokens));
            if (Utility::isValidPositiveNumber($this->CompletionTotalTokens) != true) {
                $this->CompletionTotalTokens = 0;
            }

            $this->OpenAIModel = OpenAI::ProcessModelValue($this->OpenAIModel);
            $this->OpenAIMaxTokens = OpenAI::ProcessMaxTokens($this->OpenAIMaxTokens);
            $this->OpenAITemperature = OpenAI::ProcessTemperatureValue($this->OpenAITemperature);
            $this->OpenAITop_P = OpenAI::ProcessTop_PValue($this->OpenAITop_P);
            $this->OpenAIIterations = OpenAI::ProcessNValue($this->OpenAIIterations);
            $this->OpenAIPresencePenalty = OpenAI::ProcessPresencePenaltyValue($this->OpenAIPresencePenalty);
            $this->OpenAIFrequencyPenalty = OpenAI::ProcessFrequencyPenaltyValue($this->OpenAIFrequencyPenalty);

            $this->PromptContext = \trim($this->PromptContext);
            $this->FullPrompt = \trim($this->FullPrompt);

            if(Utility::IsValidMySQLDate($this->Date) != true) {
                $this->Date = null;
            }

            if(Utility::IsValidMySQLDate($this->DateUpdated) != true) {
                $this->DateUpdated = null;
            }

            $this->TimeDifference = Utility::getTimeDifference($this->DateUpdated, $this->Date);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

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

use DateInterval;
use DateTime;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

class Charts
{

    /**
     * Get an array with dates for the last 15 days including today
     *
     * @return array
     */
    public static function GetLastFifteenDays()
    {
        try {

            $dates = array();
            $date = new DateTime();

            $dates[] = $date->format('Y-m-d');

            for ($i = 0; $i < 14; $i++) {
                $date->sub(new DateInterval('P1D'));
                $dates[] = $date->format('Y-m-d');
            }

            $dates = \array_reverse($dates);

            return $dates;
        } catch (\Throwable $th) {
            return array();
        }
    }

    /**
     * Get JS array with past 15 days
     *
     * @return void
     */
    public static function GetJSArrayFromLastFifteenDays()
    {
        try {
            $dates = self::GetLastFifteenDays();
            return (string) json_encode($dates);
        } catch (Exception $e) {
            return '[]';
        }
    }

    /**
     * Get JS array with values from last 15 days
     *
     * @return string
     */
    public static function GetJSArrayFromTotalTokenValues()
    {

        try {

            $dates = self::GetLastFifteenDays();
            if (Utility::IsValidNonEmptyArray($dates) != true) {
                return '[]';
            }

            $results = array();
            foreach ($dates as $date) {
                $results[] = self::LoadTokensDayValue($date, 'CompletionTotalTokens');
            }

            if (Utility::IsValidNonEmptyArray($results) != true) {
                return '[]';
            }

            return (string) json_encode($results);
        } catch (Exception $e) {
            return '[]';
        }
    }

    /**
     * Load total tokens spent by day
     *
     * @param [type] $date - day
     * @param [type] $column - type of token
     * @return integer
     */
    public static function LoadTokensDayValue($date, $column)
    {

        try {

            $allowed = array('CompletionPromptTokens', 'CompletionTokens', 'CompletionTotalTokens');

            if (!\in_array($column, $allowed)) {
                return 0;
            }

            if (Utility::IsValidMySQLDate($date, 'Y-m-d') != true) {
                return 0;
            }

            $start = $date . ' 00:00:00.000000';
            $end = $date . ' 23:59:59.000000';

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select('SUM(' . $db->quoteName($column) . ')');
            $query->from($db->quoteName(General::$PromptTable));
            $query->where($db->quoteName('Date') . ' >= ' . $db->quote($start));
            $query->where($db->quoteName('Date') . ' <= ' . $db->quote($end));
            $db->setQuery($query);
            $tokens = $db->loadResult();

            if (Utility::isValidPositiveNumber($tokens) != true) {
                return 0;
            }

            return (int) $tokens;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * Load Token count - lifetime
     *
     * @param [type] $column
     * @return integer
     */
    public static function LoadTotalTokenCount($column)
    {

        try {

            $allowed = array('CompletionPromptTokens', 'CompletionTokens', 'CompletionTotalTokens');

            if (!\in_array($column, $allowed)) {
                return 0;
            }

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select('SUM(' . $db->quoteName($column) . ')');
            $query->from($db->quoteName(General::$PromptTable));
            $db->setQuery($query);
            $tokens = $db->loadResult();

            if (Utility::isValidPositiveNumber($tokens) != true) {
                return 0;
            }

            return (int) $tokens;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * Get prompt tokens - percent
     *
     * @return string
     */
    public static function GetTokensByPercent()
    {

        try {

            $promptTokens = self::LoadTotalTokenCount('CompletionPromptTokens');
            $completionTokens = self::LoadTotalTokenCount('CompletionTokens');
            $TotalTokens = self::LoadTotalTokenCount('CompletionTotalTokens');

            if ($TotalTokens == 0 || $promptTokens == $completionTokens) {
                return (string) json_encode([50.00, 50.00]);
            }

            $promptTokensPercent = round(($promptTokens / $TotalTokens) * 100, 2);
            $completionTokensPercent = round(($completionTokens / $TotalTokens) * 100, 2);

            // Always ensure two decimal places for JavaScript
            $promptTokensPercent = number_format($promptTokensPercent, 2, '.', '');
            $completionTokensPercent = number_format($completionTokensPercent, 2, '.', '');

            return (string) json_encode([$promptTokensPercent, $completionTokensPercent]);
        } catch (\Throwable $th) {
            return (string) json_encode([50.00, 50.00]);
        }
    }

    /**
     * Get prompt tokens - all / lifetime
     *
     * @return string
     */
    public static function GetTokenValues()
    {

        try {

            $promptTokens = self::LoadTotalTokenCount('CompletionPromptTokens');
            $completionTokens = self::LoadTotalTokenCount('CompletionTokens');
            $TotalTokens = self::LoadTotalTokenCount('CompletionTotalTokens');

            return (string) json_encode([$TotalTokens, $promptTokens, $completionTokens]);
        } catch (\Throwable $th) {
            return (string) json_encode([0, 0, 0]);
        }
    }

    /**
     * Load Model count - lifetime
     *
     * @param [type] $column
     * @return integer
     */
    public static function LoadModelUsageCount($model)
    {

        try {

            $allowed = array('text-davinci-003', 'text-davinci-002', 'text-curie-001', 'text-babbage-001', 'text-ada-001');

            if (!\in_array($model, $allowed)) {
                return 0;
            }

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select('COUNT(' . $db->quoteName('id') . ')');
            $query->from($db->quoteName(General::$PromptTable));
            $query->where($db->quoteName('CompletionModel') . ' = ' . $db->quote($model));
            $query->where($db->quoteName('isOK') . ' = ' . $db->quote('1'));
            $db->setQuery($query);
            $tokens = $db->loadResult();

            if (Utility::isValidPositiveNumber($tokens) != true) {
                return 0;
            }

            return (int) $tokens;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * Get all model names as js label
     *
     * @return string
     */
    public static function GetJSArrayOfModelNames()
    {
        return (string) \json_encode(array('text-davinci-003', 'text-davinci-002', 'text-curie-001', 'text-babbage-001', 'text-ada-001'));
    }

    /**
     * Get mode count values
     *
     * @return string
     */
    public static function GetJSArrayFromModelCounts()
    {

        try {
            $text_davinci_003 = self::LoadModelUsageCount('text-davinci-003');
            $text_davinci_002 = self::LoadModelUsageCount('text-davinci-002');
            $text_curie_001 = self::LoadModelUsageCount('text-curie-001');
            $text_babbage_001 = self::LoadModelUsageCount('text-babbage-001');
            $text_ada_001 = self::LoadModelUsageCount('text-ada-001');

            return (string) \json_encode(array($text_davinci_003, $text_davinci_002, $text_curie_001, $text_babbage_001, $text_ada_001));
        } catch (\Throwable $th) {
            return (string) \json_encode(array(0, 0, 0, 0, 0));
        }
    }

    /**
     * Get processed value for averages
     *
     * @param string $column
     * @return string
     */
    public static function LoadAverageValue($column = '')
    {

        try {

            if (empty($column)) {
                return 'N/A';
            }

            $allowed = array('OpenAIMaxTokens', 'OpenAITemperature', 'OpenAITop_P', 'OpenAIIterations', 'OpenAIPresencePenalty', 'OpenAIFrequencyPenalty');
            if (!\in_array($column, $allowed)) {
                return 'N/A';
            }

            $value = self::LoadAverageValueByColumn($column);
            $value = self::FormatNumberValueForAvg($value);
            
            return $value;
        } catch (\Throwable $th) {
            return 'N/A';
        }
    }

    /**
     * Prepare value for chart display
     *
     * @param string $value
     * @return string
     */
    public static function FormatNumberValueForAvg($value = '') {

        $valueExpoded = \explode('.', $value);
        if(Utility::IsValidNonEmptyArray($valueExpoded) && \count($valueExpoded) == 2) {
            if($valueExpoded[0] == 0) {
                $value = '<span class="scs-decimals">' . \implode('</span>.', $valueExpoded);
            }
            else {
                $value = \implode('.<span class="scs-decimals">', $valueExpoded) . '</span>';
            }
            
        }
        
        return (string) $value;

    }

    /**
     * Load average value based on column name
     *
     * @param string $column
     * @return string
     */
    public static function LoadAverageValueByColumn($column = '')
    {

        try {

            if (empty($column)) {
                return 'N/A';
            }

            $allowed = array('OpenAIMaxTokens', 'OpenAITemperature', 'OpenAITop_P', 'OpenAIIterations', 'OpenAIPresencePenalty', 'OpenAIFrequencyPenalty');
            if (!\in_array($column, $allowed)) {
                return 'N/A';
            }

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select("ROUND(AVG(" . $db->quoteName($column) . "), 2) AS " . $db->quoteName('Average'));
            $query->from($db->quoteName(General::$PromptTable));
            $db->setQuery($query);
            $resultValue = $db->loadResult();

            if (!\is_numeric($resultValue)) {
                return '0.00';
            }

            return (string) number_format($resultValue, 2, '.', '');;

        } catch (\Throwable $th) {
            return 'N/A';
        }
    }

    /**
     * Load last 5 created prompts
     *
     * @return array
     */
    public static function LoadLastFivePrompts() {

        try {
            
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
            $query->order($db->quoteName('id') . ' DESC');
            $db->setQuery($query, 0, 5);
            $results = $db->loadAssocList();

            if(Utility::IsValidNonEmptyArray($results) != true) {
                return array();
            }

            $items = array();
            foreach ($results as $result) {
                
                if(Utility::IsValidNonEmptyArray($result, 'id') != true) {
                    continue;
                }

                $resultObj = Utility::ArrayValuesToObject($result, new PromptData());

                if(Utility::isValidPositiveNumber($resultObj->id) != true || $resultObj->id != $result['id']) {
                    continue;
                }

                $items[] = $resultObj;

            }

            return (array) $items;

        } catch (\Throwable $th) {
            return array();
        }

    }

    /**
     * Get headings for Lifetime Usage Chart
     *
     * @return string
     */
    public static function GetLifetimeUsageHeadings() {
        return (string) \json_encode(array(Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CHART_TOTAL_TOKENS'), Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CHART_PROMPT_TOKENS'), Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CHART_COMPLETION_TOKENS')));
    }


    /**
     * Get headings for Tokens Ratio Chart
     *
     * @return string
     */
    public static function GetTokenRatioHeadings() {
        return (string) \json_encode(array(Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CHART_PROMPT_TOKENS'), Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CHART_COMPLETION_TOKENS')));
    }

}

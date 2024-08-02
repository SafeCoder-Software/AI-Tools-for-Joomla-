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

use DateTime;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use League\HTMLToMarkdown\HtmlConverter;

class General
{

    public static $PromptTable = '#__safecoder_ai_tools_prompt';
    public static $PromptChoicesTable = '#__safecoder_ai_tools_prompt_choices';

    /**
     * 
     * Set Joomla Backend page title
     *
     * @return boolean
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function SetPageTitle()
    {

        try {

            /** @var \Joomla\CMS\Application\CMSApplication $app */
            $app = Factory::getApplication();

            $view = (string) \trim(\strip_tags($app->input->get('view', '', 'CMD')));
            if (empty($view)) {
                $view = 'safecoderaitools';
            }

            ToolbarHelper::title(Text::_('COM_SAFECODERAITOOLS') . ' \ ' . Text::_('COM_SAFECODERAITOOLS_' . \strtoupper($view)), 'safecoder-square');

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * 
     * Set Joomla Backend buttons based on context
     *
     * @return boolean
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function SetPageButtons()
    {

        try {

            /** @var \Joomla\CMS\Application\CMSApplication $app */
            $app = Factory::getApplication();

            $view = (string) \trim(\strip_tags($app->input->get('view', '', 'CMD')));
            if (empty($view)) {
                $view = 'dashboard';
            }

            $AIToolsPromptID = $app->input->get('PromptID', 0, 'INT');
            if (Utility::isValidPositiveNumber($AIToolsPromptID) != true) {
                $AIToolsPromptID = 0;
            }

            if ($view == 'create') {
                if (Utility::isValidPositiveNumber($AIToolsPromptID) == true) {
                    ToolbarHelper::publish('create.StartOver', Text::_('COM_SAFECODERAITOOLS_START_OVER'));
                } else {
                    ToolbarHelper::addNew('create.New', Text::_('COM_SAFECODERAITOOLS_PROCESS_PROMPT'));
                }

                ToolbarHelper::custom('history.viewDashboard', 'dashboard', '', Text::_('COM_SAFECODERAITOOLS_DASHBOARD'), false);
                ToolbarHelper::custom('create.viewHistory', 'scs-history fa fa-history', '', Text::_('COM_SAFECODERAITOOLS_HISTORY'), false);
            } else if ($view == 'history') {
                ToolbarHelper::addNew('history.viewNewPrompt', Text::_('COM_SAFECODERAITOOLS_START_OVER'));
                ToolbarHelper::custom('history.viewDashboard', 'dashboard', '', Text::_('COM_SAFECODERAITOOLS_DASHBOARD'), false);
                ToolbarHelper::deleteList(Text::_('COM_SAFECODERAITOOLS_HISTORY_REMOVE_SELECTED_MSG'), 'history.RemoveSelected');
            } else if ($view == 'display') {

                if ($app->input->get('IsLightbox') == 1) {
                    ToolbarHelper::back(Text::_('COM_SAFECODERAITOOLS_BACK'), \SCS_COMPONENT_ROOT_URL . '&view=history&IsLightbox=1');
                } else {
                    ToolbarHelper::back(Text::_('COM_SAFECODERAITOOLS_BACK'), \SCS_COMPONENT_ROOT_URL . '&view=history');
                }

                ToolbarHelper::custom('display.RunAgain', 'icon-copy', '', Text::_('COM_SAFECODERAITOOLS_RUN_AGAIN'), false);

                ToolbarHelper::trash('display.Remove', Text::_('COM_SAFECODERAITOOLS_DELETE'), false);
            } else if ($view == '' || $view == 'safecoderaitools') {

                ToolbarHelper::addNew('history.viewNewPrompt', Text::_('COM_SAFECODERAITOOLS_START_OVER'));
                ToolbarHelper::custom('create.viewHistory', 'scs-history fa fa-history', '', Text::_('COM_SAFECODERAITOOLS_HISTORY'), false);
            }

            $ArticleID = (int) $app->input->get('ArticleID', 0, 'INT');
            $CategoryID = (int) $app->input->get('CategoryID', 0, 'INT');
            if (Utility::isValidPositiveNumber($ArticleID) == true || Utility::isValidPositiveNumber($CategoryID) == true) {
                ToolbarHelper::cancel('create.ClearSelection', Text::_('COM_SAFECODERAITOOLS_CLEAR_SELECTION'));
            }

            ToolbarHelper::preferences('com_safecoderaitools');

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * 
     * Process Boolean Value
     *
     * @param integer $value
     * @return integer
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function ProcessBooleanValue($value = 0)
    {

        try {

            if (Utility::isValidPositiveNumber($value) == true) {
                return 1;
            } else {
                return 0;
            }
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * 
     * Process Positive Number Value
     *
     * @param integer $value
     * @return integer
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function ProcessPositiveNumberValue($value = 0)
    {

        try {

            if (!\is_string($value) && !\is_int($value)) {
                return 0;
            }

            if (Utility::isValidPositiveNumber($value) != true) {
                return 0;
            }

            return (int) $value;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * 
     * Resize context based on characters and word limit
     *
     * @param [type] $inputString
     * @param [type] $CharactersLimit
     * @param [type] $WordLimit
     * @return string
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function ResizeContext($inputString = '', $CharactersLimit = 0, $WordLimit = 0, $SentenceLimit = 0)
    {

        try {

            if (Utility::isValidPositiveNumber($WordLimit) != true) {
                $WordLimit = 0;
            }

            if (Utility::isValidPositiveNumber($CharactersLimit) != true) {
                $CharactersLimit = 0;
            }

            if (Utility::isValidPositiveNumber($SentenceLimit) != true) {
                $SentenceLimit = 0;
            }

            if ($CharactersLimit == 0 && $WordLimit == 0 && $SentenceLimit == 0) {
                return $inputString;
            }

            $inputString = preg_replace('/[ \t]+/', ' ', $inputString);

            $result = $inputString;

            if ($SentenceLimit > 0) {
                $sentences = preg_split('/(?<=[.!?])\s+/', $result, $SentenceLimit + 1);
                array_pop($sentences);
                $result = implode(' ', $sentences);
            }

            if ($WordLimit > 0) {
                $words = explode(' ', $result);
                $result = implode(' ', array_slice($words, 0, $WordLimit));
            }

            if ($CharactersLimit > 0) {
                $result = substr($result, 0, $CharactersLimit);

                if (substr($result, -1) !== ' ' && $inputString[$CharactersLimit] !== ' ') {
                    $lastSpacePosition = strrpos($result, ' ');
                    if ($lastSpacePosition !== false) {
                        $result = substr($result, 0, $lastSpacePosition);
                    }
                }
            }

            return (string) $result;
        } catch (\Throwable $th) {
            return $inputString;
        }
    }

    /**
     * 
     * Check content merge type
     *
     * @param integer $value
     * @return integer
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function ProcessContextArticleContentType($value = 0)
    {

        try {

            if (!\is_int($value) || !\is_string($value)) {
                return 0;
            }

            $AllowedArticleMergeTypes = array(0, 1, 2, 3, 4);

            if (!\in_array($value, $AllowedArticleMergeTypes)) {
                return 0;
            }

            return (int) $value;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * 
     * Prepare Context for AI
     *
     * @param [ContextDetails] $ContextDetails
     * @param [Config] $Config
     * @return string
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function PrepareContextString($ContextDetails, $Config)
    {

        try {

            if (!$ContextDetails instanceof ContextDetails) {
                return '';
            }

            if (!$Config instanceof Config) {
                return (string) $ContextDetails->FullContent;
            }

            $ContextStringArray = array();

            if ($Config->getContextIncludeTitle() == 1 && !empty($ContextDetails->Title)) {
                $ContextStringArray[] = '<h1>' . $ContextDetails->Title . '</h1>';
            }

            if ($Config->getContextIncludeFieldContent() == 1 && $Config->getContextIncludeFieldBeforeContent() == 1 && !empty($ContextDetails->fieldValuesString)) {
                $ContextStringArray[] = $ContextDetails->fieldValuesString;
            }

            if ($Config->getContextArticleContentType() == 1 && !empty($ContextDetails->FullContentReverse)) {
                $ContextStringArray[] = $ContextDetails->FullContentReverse;
            } else if ($Config->getContextArticleContentType() == 2 && !empty($ContextDetails->IntroContent)) {
                $ContextStringArray[] = $ContextDetails->IntroContent;
            } else if ($Config->getContextArticleContentType() == 3 && !empty($ContextDetails->Content)) {
                $ContextStringArray[] = $ContextDetails->Content;
            } else if (!empty($ContextDetails->FullContent)) {
                $ContextStringArray[] = $ContextDetails->FullContent;
            }

            if ($Config->getContextIncludeFieldContent() == 1 && $Config->getContextIncludeFieldBeforeContent() != 1 && !empty($ContextDetails->fieldValuesString)) {
                $ContextStringArray[] = $ContextDetails->fieldValuesString;
            }

            if (Utility::IsValidNonEmptyArray($ContextStringArray) != true) {
                return '';
            }

            $ContextStringValue = \implode("\n", $ContextStringArray);
            if (empty($ContextStringValue)) {
                return '';
            }

            if (!empty($Config->getContextPreText())) {
                $ContextStringValue = $Config->getContextPreText() . "\n" . $ContextStringValue;
            }

            if (!empty($Config->getContextPostText())) {
                $ContextStringValue = $ContextStringValue . "\n" . $Config->getContextPostText();
            }

            $ContextStringValue = \trim($ContextStringValue);
            $ContextStringValue = self::ResizeContext($ContextStringValue, $Config->getContextCharacterLimit(), $Config->getContextWordLimit(), $Config->getContextSentenceLimit());
            if (empty($ContextStringValue)) {
                return '';
            }

            $converter = new HtmlConverter();

            $markdown = trim(strip_tags($converter->convert($ContextStringValue)));

            return (string) $markdown;
        } catch (\Throwable $th) {
            return '';
        }
    }

    /**
     * 
     * Remove page decorations when in lightbox
     *
     * @return boolean
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function RemovePageDecorations()
    {

        try {

            /** @var \Joomla\CMS\Application\CMSApplication $app */
            $app = Factory::getApplication();

            $doc = $app->getDocument();

            $doc->addStyleDeclaration('

                #header, #sidebar-wrapper {
                    display: none !important;
                    visibility: hidden !important;
                    height: 0px !important;
                    width: 0px !important;
                    opacity: 0 !important;
                }

            ');

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * 
     * Prepare result string for display on page
     *
     * @param [type] $string
     * @return string
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function PrepareDisplayString($string = '')
    {

        try {

            $string = trim(strip_tags($string));
            if (empty($string)) {
                return '';
            }

            $string = str_replace("\t", "\n", $string);

            $lines = explode("\n", $string);

            foreach ($lines as $key => $line) {

                $line = trim($line);
                $line = preg_replace('/\s+/', ' ', $line);

                $lines[$key] = $line;
            }

            if (Utility::IsValidNonEmptyArray($lines) != true) {
                return $string;
            }

            $lines = preg_replace('/(\n\s*){3,}/', "\n\n", \implode("\n", $lines));

            if (empty($lines)) {
                return $string;
            }

            return $lines;
        } catch (\Throwable $th) {
            return $string;
        }
    }

    /**
     * 
     * Replace multiple tabs with single new line
     *
     * @param [type] $string
     * @return string
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function ReplaceMultipleTabsWithOneNewLine($string)
    {

        try {

            if (!\is_string($string) && !\is_int($string)) {
                return $string;
            }

            $string = \trim($string);
            if (empty($string)) {
                return $string;
            }

            $lines = explode("\n", $string);
            if (Utility::IsValidNonEmptyArray($lines) != true) {
                return $string;
            }

            foreach ($lines as $key => $line) {
                $line = \trim($line);
                $line = preg_replace('/\t+/', "\n", $line);
                $lines[$key] = $line;
            }

            $string = implode("\n", $lines);

            return $string;
        } catch (\Throwable $th) {
            return $string;
        }
    }

    /**
     * 
     * Build json of error message
     *
     * @param string $message
     * @param string $type
     * @param integer $code
     * @return string
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function BuildErrorArrayString($message = '', $type = '', $code = 0)
    {


        try {

            if (empty($message) && Utility::isValidPositiveNumber($code)) {
                $message = Utility::ReturnResponseMessageBasedOnCode($code);
            }

            $errorArr = array();
            $errorArr['error'] = array();
            $errorArr['error']['message'] = $message;
            $errorArr['error']['type'] = $type;
            $errorArr['error']['param'] = null;
            $errorArr['error']['code'] = $code;

            return \json_encode($errorArr, \JSON_PRETTY_PRINT);
        } catch (\Throwable $th) {
            return '';
        }
    }

    /**
     * 
     * Load backend resources
     *
     * @return void
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function LoadPageResources()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        $doc = $app->getDocument();
        $doc->addScript('https://cdn.jsdelivr.net/npm/chart.js');
        $doc->addScript('https://cdn.jsdelivr.net/npm/marked/marked.min.js');

        $doc->addStyleDeclaration('
        
            .header .page-title {
                line-height: 30px;
            }

            .icon-safecoder-square {
                text-align:center;
                background-image: url(\'' . Uri::root() . 'media/com_safecoderaitools/component/main-icon-red.png\');
                height: 30px;
                width: 30px;
                background-position: center center;
                background-repeat: no-repeat;
                background-size: 30px;
                display: block;
                float: left;
            }

            .container-main {
                background-image: url(\'' . Uri::root() . 'media/com_safecoderaitools/component/safe-coder-logo-text.png\');
                background-repeat: no-repeat;
                background-position: right 2rem bottom 2rem;
                padding-bottom: 5rem;
            }

            @media(max-width:768px) {
                .container-main {
                    background-position: right 1rem bottom 5rem;
                    padding-bottom: 7rem;
                }
            }

            #wrapper {
                overflow-x: hidden !important;
            }

        ');

        HTMLHelper::_('jquery.framework');
        HTMLHelper::_('script', 'system/choices.min.js', ['version' => 'auto', 'relative' => true]);
        HTMLHelper::_('stylesheet', 'system/choices.min.css', ['version' => 'auto', 'relative' => true]);
    }

    /**
     * 
     * Load Title from Article ID
     *
     * @param integer $id - Article ID
     * @return string
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function LoadArticleTitleByID($id = 0)
    {

        if (Utility::isValidPositiveNumber($id) != true) {
            return Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE');
        }

        try {

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);

            $query->select($db->quoteName('title'));
            $query->from($db->quoteName('#__content'));
            $query->where($db->quoteName('id') . ' = ' . $db->quote($id));
            $db->setQuery($query);
            $result = $db->loadResult();

            if (empty($result)) {
                return Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE');
            }

            return $result;
        } catch (\Throwable $th) {
            return Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE');
        }
    }

    /**
     * 
     * Load Title from Category ID
     *
     * @param integer $id - category id
     * @return string
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function LoadCategoryTitleByID($id = 0)
    {

        if (Utility::isValidPositiveNumber($id) != true) {
            return Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE');
        }

        try {

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);

            $query->select($db->quoteName('title'));
            $query->from($db->quoteName('#__categories'));
            $query->where($db->quoteName('id') . ' = ' . $db->quote($id));
            $db->setQuery($query);
            $result = $db->loadResult();

            if (empty($result)) {
                return Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE');
            }

            return $result;
        } catch (\Throwable $th) {
            return Text::_('COM_SAFECODERAITOOLS_NOT_AVAILABLE');
        }
    }

    /**
     * 
     * Check component settings
     *
     * @return boolean
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function CheckSettings()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        $view = $app->input->get('view', '', 'CMD');

        if ($view == '') {
            $app->redirect(\SCS_COMPONENT_ROOT_URL . '&view=safecoderaitools', 301);
        }

        $config = new Config();

        if (empty($config->getOpenAIAPIKey())) {
            $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_ERROR_OPEN_AI_API_KEY_EMPTY'), 'error');
            return false;
        }

        return true;
    }

    /**
     * 
     * Set Cache, No Cache headers
     *
     * @return boolean
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function SetHeader()
    {

        header('Cache-Control: no-cache, no-store');
        header('Pragma: no-cache');

        return true;
    }

    /**
     * 
     * Checks if the input is a valid date-time value.
     *
     * @param mixed $input The input to check.
     * @return bool Returns true if the input is a valid date-time value, otherwise false.
     * 
     * @version 1.0.0
     * @since 1.0.0
     * 
     */
    public static function IsValidDateTime($value = '')
    {
        if (!is_string($value)) {
            return false;
        }

        $value = trim(strip_tags($value));
        if (empty($value)) {
            return false;
        }

        try {

            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $value);
            if ($dateTime instanceof DateTime && $dateTime->format('Y-m-d H:i:s') === $value) {
                return true;
            }

            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

}

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

class ContextDetails
{

    private $AllowedContentTypes = array(1, 2);

    public $Exists = false;

    public $ArticleID = 0;
    public $CategoryID = 0;

    public $ContentType = 0;
    public $ContentTypeName = '';

    public $Title = '';

    public $IntroContent = '';

    public $Content = '';

    public $FullContent = '';
    public $FullContentReverse = '';

    public $fieldValuesArray = array();
    public $fieldValuesString = '';

    /**
     * Load Article/Category Details
     *
     * @return boolean
     */
    public function Load()
    {

        try {

            $this->FormatValues();

            if (Utility::isValidPositiveNumber($this->ArticleID) != true && Utility::isValidPositiveNumber($this->CategoryID) != true) {
                return false;
            }

            if (!\in_array($this->ContentType, $this->AllowedContentTypes)) {
                return false;
            }

            if ($this->ContentType == 1) {
                return $this->LoadArticle();
            } else {
                return $this->LoadCategory();
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Load Article Content
     *
     * @return boolean
     */
    private function LoadArticle()
    {

        try {

            if (Utility::isValidPositiveNumber($this->ArticleID) != true) {
                $this->FormatValues();
                return false;
            }

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $columns = array();
            $columns[] = 'id';
            $columns[] = 'title';
            $columns[] = 'introtext';
            $columns[] = 'fulltext';

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select($db->quoteName($columns));
            $query->from($db->quoteName('#__content'));
            $query->where($db->quoteName('id') . ' = ' . $db->quote($this->ArticleID));
            $db->setQuery($query);
            $ArticleArr = $db->loadAssoc();

            if (Utility::IsValidNonEmptyArray($ArticleArr, 'id') != true || $ArticleArr['id'] != $this->ArticleID) {
                $this->FormatValues();
                return false;
            }

            if (Utility::IsValidNonEmptyArray($ArticleArr, 'title') == true) {
                $this->Title = $ArticleArr['title'];
            }

            if (Utility::IsValidNonEmptyArray($ArticleArr, 'introtext') == true) {
                $this->IntroContent = $ArticleArr['introtext'];
            }

            if (Utility::IsValidNonEmptyArray($ArticleArr, 'fulltext') == true) {
                $this->Content = $ArticleArr['fulltext'];
            }

            $fieldColumns = array();
            $fieldColumns[] = 't2.id';
            $fieldColumns[] = 't2.title';
            $fieldColumns[] = 't1.value';

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select($db->quoteName($fieldColumns));
            $query->from($db->quoteName('#__fields_values', 't1'));
            $query->innerJoin($db->quoteName('#__fields', 't2'), $db->quoteName('t1.field_id') . ' = ' . $db->quoteName('t2.id'));
            $query->where($db->quoteName('t1.item_id') . ' = ' . $db->quote($this->ArticleID));
            $db->setQuery($query);
            $fieldList = $db->loadAssocList();

            if (Utility::IsValidNonEmptyArray($fieldList) != true) {
                $this->FormatValues();
                return true;
            }

            $fieldResults = array();
            foreach ($fieldList as $fieldItem) {

                if (Utility::IsValidNonEmptyArray($fieldItem) != true) {
                    continue;
                }

                if (!\array_key_exists('id', $fieldItem) || !\array_key_exists('value', $fieldItem) || !\array_key_exists('title', $fieldItem)) {
                    continue;
                }

                $fieldID = \trim($fieldItem['id']);
                if (Utility::isValidPositiveNumber($fieldID) != true) {
                    continue;
                }

                $fieldValue = \trim($fieldItem['value']);
                if (empty($fieldValue)) {
                    continue;
                }

                $fieldTitle = \trim($fieldItem['title']);
                if (empty($fieldTitle)) {
                    continue;
                }

                if (!\array_key_exists($fieldID, $fieldResults)) {
                    $fieldResults[$fieldID] = array();
                    $fieldResults[$fieldID][0] = $fieldTitle;
                    $fieldResults[$fieldID][1] = array();
                }

                $fieldResults[$fieldID][1][] = '<p>- ' . $fieldValue . '</p>';
            }

            $fieldProcessedResults = array();
            $fieldProcessedStrings = array();

            if (Utility::IsValidNonEmptyArray($fieldResults)) {
                foreach ($fieldResults as $fieldVal) {

                    if (!\array_key_exists(1, $fieldVal) || !\array_key_exists(0, $fieldVal)) {
                        continue;
                    }

                    if (empty($fieldVal[0])) {
                        continue;
                    }

                    if (\is_array($fieldVal[1])) {
                        $fieldVal[1] = trim(\implode('', $fieldVal[1]));
                    }

                    if (empty($fieldVal[1])) {
                        continue;
                    }

                    $fieldVal[2] = '<p>' . $fieldVal[0] . ':</p><p>' . $fieldVal[1] . '</p>';
                    $fieldVal[2] = $fieldVal[2];

                    $fieldProcessedStrings[] = '<p>' . $fieldVal[2] . '</p>';

                    $fieldProcessedResults[] = $fieldVal;
                }
            }

            $this->fieldValuesArray = $fieldProcessedResults;

            if (Utility::IsValidNonEmptyArray($fieldProcessedStrings)) {
                $this->fieldValuesString = \implode('', $fieldProcessedStrings);
            } else {
                $this->fieldValuesString = '';
            }

            $this->FormatValues();

            return true;
        } catch (\Throwable $th) {
            $this->FormatValues();
            return false;
        }
    }

    /**
     * Load Category Content
     *
     * @return boolean
     */
    private function LoadCategory()
    {

        try {

            if (Utility::isValidPositiveNumber($this->CategoryID) != true) {
                return false;
            }

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $columns = array();
            $columns[] = 'id';
            $columns[] = 'title';
            $columns[] = 'description';

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->select($db->quoteName($columns));
            $query->from($db->quoteName('#__categories'));
            $query->where($db->quoteName('id') . ' = ' . $db->quote($this->CategoryID));
            $db->setQuery($query);
            $CategoryArr = $db->loadAssoc();

            if (Utility::IsValidNonEmptyArray($CategoryArr, 'id') != true || $CategoryArr['id'] != $this->CategoryID) {
                $this->FormatValues();
                return false;
            }

            if (Utility::IsValidNonEmptyArray($CategoryArr, 'title') == true) {
                $this->Title = $CategoryArr['title'];
            }

            if (Utility::IsValidNonEmptyArray($CategoryArr, 'description') == true) {
                $this->Content = $CategoryArr['description'];
            }

            $this->FormatValues();

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Format Content Values
     *
     * @return boolean
     */
    private function FormatValues()
    {

        try {

            $this->ArticleID = (int) \trim(\strip_tags($this->ArticleID));
            if (Utility::isValidPositiveNumber($this->ArticleID) != true) {
                $this->ArticleID = 0;
            }

            $this->CategoryID = (int) \trim(\strip_tags($this->CategoryID));
            if (Utility::isValidPositiveNumber($this->CategoryID) != true) {
                $this->CategoryID = 0;
            }

            if (Utility::isValidPositiveNumber($this->ArticleID) == true) {
                $this->ContentType = 1;
                $this->ContentTypeName = Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_ARTICLE');
            } else if (Utility::isValidPositiveNumber($this->CategoryID) == true) {
                $this->ContentType = 2;
                $this->ContentTypeName = Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_CATEGORY');
            } else {
                $this->ContentType = 0;
                $this->ContentTypeName = Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_NONE_CONTEXT');
            }

            $this->Title = trim($this->Title);

            $this->IntroContent = \trim($this->IntroContent);

            $this->Content = \trim($this->Content);

            if (empty($this->Content)) {
                $this->FullContent = $this->IntroContent;
            } else if (empty($this->IntroContent)) {
                $this->FullContent = $this->Content;
            } else {
                $this->FullContent = $this->IntroContent . $this->Content;
            }

            if (empty($this->Content)) {
                $this->FullContentReverse = $this->IntroContent;
            } else if (empty($this->IntroContent)) {
                $this->FullContentReverse = $this->Content;
            } else {
                $this->FullContentReverse = $this->Content . $this->IntroContent;
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

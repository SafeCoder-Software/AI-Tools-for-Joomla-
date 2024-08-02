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
use Joomla\Database\DatabaseInterface;

class PromptChoice
{

    public $PromptID = 0;

    public $Text = '';

    public $Index = 0;

    public $FinishReason = '';

    /**
     * Process current values
     *
     * @return void
     */
    public function ProcessValues()
    {
        $this->FormatValues();
    }

    /**
     * Insert choice in database
     *
     * @return boolean
     */
    public function Insert()
    {

        try {

            $this->FormatValues();

            if (Utility::isValidPositiveNumber($this->PromptID) != true) {
                return false;
            }

            if (empty($this->Text)) {
                return false;
            }

            if (Utility::isValidPositiveNumber($this->Index) != true) {
                $this->Index = 0;
            }

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $columns = array();
            $columns[] = 'PromptID';
            $columns[] = 'Text';
            $columns[] = 'Index';
            $columns[] = 'FinishReason';

            $values = array();
            $values[] = $db->quote($this->PromptID);
            $values[] = $db->quote($this->Text);
            $values[] = $db->quote($this->Index);
            $values[] = $db->quote($this->FinishReason);

            /** @var \Joomla\Database\Query\MysqliQuery $query */
            $query = $db->getQuery(true);
            $query->insert($db->quoteName(General::$PromptChoicesTable));
            $query->columns($db->quoteName($columns));
            $query->values(\implode(', ', $values));
            $db->setQuery($query);
            $db->execute();

            return true;
        } catch (\Throwable $th) {
            throw $th;
            return false;
        }
    }

    /**
     * Format choice values
     *
     * @return boolean
     */
    private function FormatValues()
    {

        try {

            $this->PromptID = (int) trim(\strip_tags($this->PromptID));
            if (Utility::isValidPositiveNumber($this->PromptID) != true) {
                $this->PromptID = 0;
            }

            $this->Text = General::ReplaceMultipleTabsWithOneNewLine($this->Text);

            $this->Index = (int) trim(\strip_tags($this->Index));
            if (Utility::isValidPositiveNumber($this->Index) != true) {
                $this->Index = 0;
            }

            $this->FinishReason = \trim(\strip_tags($this->FinishReason));

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

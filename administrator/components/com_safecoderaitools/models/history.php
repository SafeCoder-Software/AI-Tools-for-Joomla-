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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseInterface;
use SafeCoderSoftwareAITools\Libraries\General;
use SafeCoderSoftwareAITools\Libraries\Utility;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * 
 * History Model
 *
 */
class SafeCoderAiToolsModelHistory extends ListModel
{

    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id',
                'FullName',
                'isOK',
                'CompletionPromptTokens',
                'CompletionTokens',
                'CompletionTotalTokens',
                'OpenAIModel'
            );
        }

        parent::__construct($config);
    }

    /**
     * Populate state for limit and page
     *
     * @param string $ordering
     * @param string $direction
     * @return void
     */
    protected function populateState($ordering = 'id', $direction = 'desc')
    {
        
        parent::populateState($ordering, $direction);

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        $config = $app->getConfig();

        $limit = $this->getUserStateFromRequest($this->context . '.filter.limit', 'filter_limit', $config->get('list_limit'));
        $this->setState('list.limit', $limit);

        $start = $this->getUserStateFromRequest('list.start', 'limitstart', 0);
        $this->setState('list.start', $start);

        return;
        
    }

    /**
     * Build query for history view
     *
     * @return \Joomla\Database\DatabaseQuery $query
     */
    protected function getListQuery()
    {

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

        /** @var \Joomla\Database\DatabaseInterface $db */
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true);

        $query->select($db->quoteName($columns));
        $query->from($db->quoteName(General::$PromptTable));

        // search by user input
        $search = trim($this->getState('filter.search', ''));
        if (!empty($search)) {
            $query->where('LOWER(' . $db->quoteName('UserInput') . ') LIKE(' . $db->quote('%' . strtolower($search) . '%') . ')');
        }

        // is complete
        $isOK = trim($this->getState('filter.isOK', ''));
        if(in_array($isOK, array(1, 2))) {

            if($isOK != 1) {
                $isOK = 0;
            }

            $query->where($db->quoteName('isOK') . ' = ' . $db->quote($isOK));

        }

        // completion model
        $completionModel = trim($this->getState('filter.completionModel', 'none'));
        if(!empty($completionModel) && $completionModel != 'none') {
            $query->where($db->quoteName('OpenAIModel') . ' = ' . $db->quote($completionModel));
        }

        // Article Item
        $ArticleItem = trim($this->getState('filter.ArticleItem', 0));
        if(Utility::isValidPositiveNumber($ArticleItem) == true) {
            $query->where($db->quoteName('ArticleID') . ' = ' . $db->quote($ArticleItem));
        }

        // Category Item
        $CategoryItem = trim($this->getState('filter.CategoryItem', 0));
        if(Utility::isValidPositiveNumber($CategoryItem) == true) {
            $query->where($db->quoteName('CategoryID') . ' = ' . $db->quote($CategoryItem));
        }

        // User Item
        $UserIDItem = trim($this->getState('filter.UserIDItem', 0));
        if(Utility::isValidPositiveNumber($UserIDItem) == true) {
            $query->where($db->quoteName('UserID') . ' = ' . $db->quote($UserIDItem));
        }
        else if($UserIDItem == -2) {
            $query->where($db->quoteName('UserID') . ' = ' . $db->quote(0));
        }
        
        $searchFullName = trim($this->getState('filter.searchFullName', ''));
        if(!empty($searchFullName)) {
            $query->where('LOWER(' . $db->quoteName('FullName') . ') LIKE(' . $db->quote('%' . strtolower($searchFullName) . '%') . ')');
        }

        $searchCompletionID = trim($this->getState('filter.searchCompletionID', ''));
        if(!empty($searchCompletionID)) {
            $query->where('LOWER(' . $db->quoteName('CompletionID') . ') LIKE(' . $db->quote('%' . strtolower($searchCompletionID) . '%') . ')');
        }

        $orderCol = $this->state->get('list.ordering', 'id');
        $orderDirn = $this->state->get('list.direction', 'ASC');
        $query->order($db->quoteName($orderCol) . ' ' . $orderDirn);


        return $query;
    }
}

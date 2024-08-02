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
use SafeCoderSoftwareAITools\Libraries\General;
use SafeCoderSoftwareAITools\Libraries\PromptData;
use SafeCoderSoftwareAITools\Libraries\Utility;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SafeCoderAIToolsControllerHistory extends BaseController
{

    /**
     * Remove selected items from the database
     *
     * @return void
     */
    public function RemoveSelected()
    {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        General::SetHeader();

        $IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
        if($IsLightbox == 1) {
            $IsLightbox = '&IsLightbox=1';
        }
        else {
            $IsLightbox = '';
        }

        $cid = $app->input->get('cid', array(), 'ARRAY');
        if (Utility::IsValidNonEmptyArray($cid) != true) {
            $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_REMOVE_SELECTED_EMPTY_LIST'), 'error');
            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=history' . $IsLightbox);
            return;
        }

        try {

            foreach ($cid as $key => $id) {

                if (Utility::isValidPositiveNumber($id) != true) {
                    continue;
                }

                $promptObj = new PromptData($id);
                if ($promptObj->id != $id) {
                    continue;
                }

                $promptObj->Delete();
            }

            $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_REMOVE_SELECTED_OK'), 'success');
            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=history' . $IsLightbox);
            return;
        } catch (\Throwable $th) {
            $app->enqueueMessage(Text::_('COM_SAFECODERAITOOLS_REMOVE_SELECTED_EMPTY_LIST'), 'error');
            $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=history' . $IsLightbox);
            return;
        }
    }

    /**
     * Go to new prompt without contexgt
     *
     * @return void
     */
    public function viewNewPrompt() {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        General::SetHeader();

        $IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
        if($IsLightbox == 1) {
            $IsLightbox = '&IsLightbox=1';
        }
        else {
            $IsLightbox = '';
        }

        $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=create' . $IsLightbox);
        return;

    }

    /**
     * Go to dashboard
     *
     * @return void
     */
    public function viewDashboard() {

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        General::SetHeader();

        $IsLightbox = $app->input->get('IsLightbox', 0, 'INT');
        if($IsLightbox == 1) {
            $IsLightbox = '&IsLightbox=1';
        }
        else {
            $IsLightbox = '';
        }

        $this->setRedirect(SCS_COMPONENT_ROOT_URL . '&view=safecoderaitools' . $IsLightbox);
        return;

    }
}

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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use SafeCoderSoftwareAITools\Libraries\PromptData;
use SafeCoderSoftwareAITools\Libraries\Utility;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$listOrder = $this->escape($this->state->get('list.ordering', 'id'));
$listDirn = $this->escape($this->state->get('list.direction', 'DESC'));

?>

<form action="index.php?option=com_safecoderaitools&view=history" method="POST" id="adminForm" name="adminForm">
    <div class="filters">
        <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this), '', array('component' => 'none')); ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="2%">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="50%">
                        <?php echo Text::_('COM_SAFECODERAITOOLS_HISTORY_TABLE_USER_INPUT'); ?>
                    </th>
                    <th width="2%">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SAFECODERAITOOLS_HISTORY_TABLE_IS_OK', 'isOK', $listDirn, $listOrder); ?>
                    </th>
                    <th width="2%">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SAFECODERAITOOLS_HISTORY_TABLE_COMPLETION_MODEL', 'OpenAIModel', $listDirn, $listOrder); ?>
                    </th>
                    <th width="2%">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SAFECODERAITOOLS_HISTORY_TABLE_COMPLETION_PROMPT_TOKENS', 'CompletionPromptTokens', $listDirn, $listOrder); ?>
                    </th>
                    <th width="2%">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SAFECODERAITOOLS_HISTORY_TABLE_COMPLETION_RETURN_TOKENS', 'CompletionTokens', $listDirn, $listOrder); ?>
                    </th>
                    <th width="2%">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SAFECODERAITOOLS_HISTORY_TABLE_COMPLETION_TOTAL_TOKENS', 'CompletionTotalTokens', $listDirn, $listOrder); ?>
                    </th>
                    <th width="2%">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SAFECODERAITOOLS_HISTORY_FULL_NAME', 'FullName', $listDirn, $listOrder); ?>
                    </th>
                    <th width="2%">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SAFECODERAITOOLS_HISTORY_TABLE_ID', 'id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="9">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($this->items)) : ?>
                    <?php
                    foreach ($this->items as $i => $row) :
                        /** @var PromptData $prompt */
                        $row = Utility::ArrayValuesToObject((array) $row, new PromptData());
                    ?>
                        <tr>
                            <!-- Selection boxes -->
                            <td>
                                <?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
                            </td>
                            <!-- User Input -->
                            <td>
                                <a href="index.php?option=com_safecoderaitools&view=display&PromptID=<?php echo $row->id . $this->IsLightboxParam; ?>">
                                    <?php echo $row->UserInput; ?>
                                </a>
                                <div class="small break-word">
                                    <?php

                                    if (Utility::isValidPositiveNumber($row->ArticleID) == true) {
                                        echo Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_ARTICLE');
                                    } else if (Utility::isValidPositiveNumber($row->CategoryID) == true) {
                                        echo Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_CATEGORY');
                                    } else {
                                        echo Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_NONE');
                                    }
                                    ?>

                                    <?php if (Utility::isValidPositiveNumber($row->ArticleID) == true) : ?>
                                        <a href="index.php?option=com_content&task=article.edit&id=<?php echo $row->ArticleID; ?>">
                                            : <?php echo Text::_('COM_SAFECODERAITOOLS_TABLE_OPEN_ARTICLE'); ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (Utility::isValidPositiveNumber($row->CategoryID) == true) : ?>
                                        <a href="index.php?option=com_categories&task=category.edit&id=<?php echo $row->CategoryID; ?>&extension=com_content">
                                            : <?php echo Text::_('COM_SAFECODERAITOOLS_TABLE_OPEN_CATEGORY'); ?>
                                        </a>
                                    <?php endif; ?>

                                </div>
                            </td>
                            <!-- Completion Model -->
                            <td class="text-center">
                                <?php if ($row->IsOKValue == true) : ?>
                                    <span class="icon-publish text-success"></span>
                                <?php else : ?>
                                    <span class="icon-unpublish text-danger"></span>
                                <?php endif; ?>
                            </td>
                            <!-- Completion Model -->
                            <td>
                                <?php echo $row->OpenAIModel; ?>
                            </td>
                            <!-- Prompt Tokens -->
                            <td class="text-center">
                                <?php echo $row->CompletionPromptTokens; ?>
                            </td>
                            <!-- Completion Tokens -->
                            <td class="text-center">
                                <?php echo $row->CompletionTokens; ?>
                            </td>
                            <!-- Total Tokens -->
                            <td class="text-center">
                                <?php echo $row->CompletionTotalTokens; ?>
                            </td>
                            <!-- Full Name -->
                            <td>
                                <?php echo $row->FullName; ?>
                            </td>
                            <!-- Prompt ID -->
                            <td>
                                <?php echo $row->id; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9">
                            <?php echo Text::_('COM_SAFECODERAITOOLS_TABLE_NO_RESULTS'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="IsLightbox" value="<?php echo $this->IsLightbox; ?>" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
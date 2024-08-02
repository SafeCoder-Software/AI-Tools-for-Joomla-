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

use Joomla\CMS\Language\Text;
use SafeCoderSoftwareAITools\Libraries\Config;
use SafeCoderSoftwareAITools\Libraries\Utility;


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/** @var Config $config */
$config = $this->AIToolsConfig;

$PredefinedList = $config->getPredefinedPromptsList();
if($config->getPluginPredefinedPromptListReversed() == 1) {
    $PredefinedList = array_reverse($PredefinedList);
}

?>

<form id="adminForm" name="adminForm" method="POST" action="index.php?option=com_safecoderaitools&view=create">

    <?php if (Utility::isValidPositiveNumber($this->ArticleID) == true || Utility::isValidPositiveNumber($this->CategoryID) == true) : ?>
        <div class="row">
            <div class="col">
                <div class="alert alert-primary" role="alert">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_PROMPT_RESPONSE_BASED_ON'); ?><?php echo $this->AIToolsContext->Title; ?>
                    (
                    <?php
                    if (Utility::isValidPositiveNumber($this->ArticleID) == true) {
                        echo Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_ARTICLE');
                    } else {
                        echo Text::_('COM_SAFECODERAITOOLS_PROMPT_DATA_TYPE_NAME_CATEGORY');
                    }
                    ?>
                    )
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row mb-4">
        <div class="col controls">
            <h3 class="mb-2"><?php echo Text::_('COM_SAFECODERAITOOLS_ASK_ME_ANYTHING'); ?></h3>
            <?php if (Utility::IsValidNonEmptyArray($PredefinedList) == true) : ?>
                <select id="PredefinedPrompts" class="form-select mb-2">
                    <option value="-1" <?php if ($this->PredefinedPrompt < 0) : ?>selected<?php endif; ?>><?php echo Text::_('COM_SAFECODERAITOOLS_SELECT_PREDEFINED_PROMPT') ?></option>
                    <?php foreach ($PredefinedList as $key => $promptItem) : ?>
                        <?php
                        if ($key == $this->PredefinedPrompt) {
                            $sel = ' selected';
                        } else {
                            $sel = '';
                        }
                        ?>
                        <option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $promptItem['promptTitle']; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <textarea class="form-control col-12 scs-height-textarea" name="contentValue" id="contentValue" placeholder="<?php echo htmlspecialchars(Text::_('COM_SAFECODERAITOOLS_ASK_ME_PLACEHOLDER'), ENT_QUOTES); ?>"></textarea>
        </div>
    </div>
    <?php if ($config->getPromptToolAdvancedOptions() == 1) : ?>
        <div class="row mb-4">
            <div class="col controls">
                <h3 class="mb-4"><?php echo Text::_('COM_SAFECODERAITOOLS_ADVANCED_OPTIONS') ?></h3>
                <div class="main-card" style="padding: 20px">
                    <div class="mb-2">
                        <label for="OpenAIModel"><?php echo Text::_('COM_SAFECODERAITOOLS_NEW_MODEL_LABEL'); ?></label>
                        <select name="OpenAIModel" id="OpenAIModel" class="form-select">
                            <?php
                                $engines = array('text-davinci-003', 'text-davinci-002', 'text-curie-001', 'text-babbage-001', 'text-ada-001');
                                foreach ($engines as $key => $engine) {

                                    if(empty($engine)) {
                                        continue;
                                    }

                                    $selected = '';
                                    if($engine == $config->getOpenAIModel()) {
                                        $selected = ' selected';
                                    }

                                    echo '<option value="' . $engine . '"' . $selected . '>' . $engine . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="OpenAIMaxTokens"><?php echo Text::_('COM_SAFECODERAITOOLS_NEW_MAX_TOKENS_LABEL'); ?></label>
                        <input type="number" id="OpenAIMaxTokens" name="OpenAIMaxTokens" value="<?php echo $config->getOpenAIMaxTokens(); ?>" step="1" class="form-control" max="4097" />
                    </div>
                    <div class="mb-2">
                        <label for="OpenAITemperature"><?php echo Text::_('COM_SAFECODERAITOOLS_NEW_TEMPERATURE_LABEL'); ?></label>
                        <input type="number" id="OpenAITemperature" name="OpenAITemperature" value="<?php echo $config->getOpenAITemperature(); ?>" step="0.1" min="0" max="2" class="form-control" />
                    </div>
                    <div class="mb-2">
                        <label for="OpenAITop_P"><?php echo Text::_('COM_SAFECODERAITOOLS_NEW_TOP_P_LABEL'); ?></label>
                        <input type="number" id="OpenAITop_P" name="OpenAITop_P" value="<?php echo $config->getOpenAITop_P(); ?>" step="0.1" min="0" max="1" class="form-control" />
                    </div>
                    <div class="mb-2">
                        <label for="OpenAIIterations"><?php echo Text::_('COM_SAFECODERAITOOLS_NEW_CHOICES_N_LABEL'); ?></label>
                        <input type="number" id="OpenAIIterations" name="OpenAIIterations" value="<?php echo $config->getOpenAIIterations(); ?>" step="1" min="0" max="10" class="form-control" />
                    </div>
                    <div class="mb-2">
                        <label for="OpenAIPresencePenalty"><?php echo Text::_('COM_SAFECODERAITOOLS_NEW_PRESENCE_PENALTY_LABEL'); ?></label>
                        <input type="number" id="OpenAIPresencePenalty" name="OpenAIPresencePenalty" value="<?php echo $config->getOpenAIPresencePenalty(); ?>" step="0.1" min="-2" max="2" class="form-control" />
                    </div>
                    <div class="mb-2">
                        <label for="OpenAIFrequencyPenalty"><?php echo Text::_('COM_SAFECODERAITOOLS_NEW_FREQUENCY_PENALTY_LABEL'); ?></label>
                        <input type="number" id="OpenAIFrequencyPenalty" name="OpenAIFrequencyPenalty" value="<?php echo $config->getOpenAIFrequencyPenalty(); ?>" step="0.1" min="-2" max="2" class="form-control" />
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <input type="hidden" id="OpenAIMaxTokens" name="OpenAIMaxTokens" value="<?php echo $config->getOpenAIMaxTokens(); ?>" />
    <?php endif; ?>
    <input type="hidden" name="ArticleID" value="<?php echo $this->ArticleID; ?>" />
    <input type="hidden" name="CategoryID" value="<?php echo $this->CategoryID; ?>" />
    <input type="hidden" name="IsLightbox" value="<?php echo $this->IsLightbox; ?>" />
    <input type="hidden" id="SubmitTask" name="task" value="" />
    <input type="hidden" name="boxchecked" value="" />
</form>

<?php if (Utility::IsValidNonEmptyArray($config->getPredefinedPromptsList())) : ?>
    <script type="text/javascript">
        const selectBox = document.getElementById("PredefinedPrompts");
        const AvailableSelections = <?php echo json_encode($PredefinedList); ?>;
        const contentValue = document.getElementById("contentValue");
        const MaxTokens = document.getElementById("OpenAIMaxTokens");

        <?php if($this->PredefinedPrompt > -1): ?>
            if(contentValue) {

                if(<?php echo $this->PredefinedPrompt; ?> in AvailableSelections) {
                    
                    contentValue.value = AvailableSelections[<?php echo $this->PredefinedPrompt; ?>]['promptContent'];
                    MaxTokens.value = AvailableSelections[<?php echo $this->PredefinedPrompt; ?>]['promptMaxTokens'];
                }

                if(contentValue.value.length > 0) {

                    let SubmitTask = document.getElementById('SubmitTask');
                    if(SubmitTask) {
                        SubmitTask.value = 'create.New';
                    }

                    let form = document.getElementById('adminForm');
                    form.submit();
                }

            }
            
        <?php endif; ?>

        selectBox.addEventListener("change", function() {

            if (contentValue && MaxTokens) {

                let selectedValue = selectBox.value;
                if (selectedValue in AvailableSelections) {

                    if ('promptContent' in AvailableSelections[selectedValue]) {
                        contentValue.value = AvailableSelections[selectedValue]['promptContent'];
                        MaxTokens.value = AvailableSelections[selectedValue]['promptMaxTokens'];
                    } else {
                        contentValue.value = '';
                        MaxTokens.value = '<?php echo $config->getOpenAIMaxTokens(); ?>';
                    }

                } else {
                    contentValue.value = '';
                    MaxTokens.value = '<?php echo $config->getOpenAIMaxTokens(); ?>';
                }

            }

        });
    </script>
<?php endif; ?>

<style>
    .scs-height-textarea {
        height: 200px;
    }
    .button-scs-history::before {
        content: "" !important;
    }
</style>
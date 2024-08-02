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
use SafeCoderSoftwareAITools\Libraries\General;
use SafeCoderSoftwareAITools\Libraries\PromptChoice;
use SafeCoderSoftwareAITools\Libraries\PromptData;
use SafeCoderSoftwareAITools\Libraries\Utility;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/** @var PromptData $prompt */
$prompt = $this->AIToolsPromptObj;

/** @var Config $config */
$config = $this->AIToolsConfig;

?>

<?php if ($config->getGeneralTypeAnimationEnabled() == 1) : ?>
    <script type="text/javascript">
        function typeTextElement(ChoiceNo = 0) {

            if (isNaN(ChoiceNo)) {
                return;
            }

            const textElement = document.getElementById('Choice' + ChoiceNo);
            textElement.classList.remove('scs-hidden');
            const text = textElement.textContent;
            let i = 0;
            textElement.textContent = '';

            const intervalId = setInterval(function() {
                textElement.textContent += text.charAt(i);
                i++;

                if (i >= text.length) {
                    clearInterval(intervalId);
                }
            }, 50);
        }
    </script>
<?php endif; ?>

<form id="adminForm" name="adminForm" method="POST" action="index.php?option=com_safecoderaitools&view=create" class="scs-hidden">
    <input type="hidden" name="ArticleID" value="<?php echo $prompt->ArticleID; ?>" />
    <input type="hidden" name="CategoryID" value="<?php echo $prompt->CategoryID; ?>" />
    <input type="hidden" name="IsLightbox" value="<?php echo $this->IsLightbox; ?>" />
    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="">
</form>

<h3><?php echo nl2br($prompt->UserInput); ?></h3>

<?php if (Utility::IsValidNonEmptyArray($prompt->ChoiceList) == true) : ?>
    <div class="list-group mt-4">
        <?php $x = 0; ?>
        <?php foreach ($prompt->ChoiceList as $choice) : ?>
            <?php
            if (!$choice instanceof PromptChoice) {
                continue;
            }

            $x++;
            ?>
            <li class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between mb-2">
                    <?php if ($prompt->OpenAIIterations == 1) : ?>
                        <h5 class="mb-1"><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_CHOICE_SINGLE'); ?></h5>
                    <?php else : ?>
                        <h5 class="mb-1"><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_CHOICE_LABEL'); ?><?php echo ($choice->Index + 1); ?></h5>
                    <?php endif; ?>
                    <small>
                        <a id="ChoiceCopy<?php echo $x; ?>" class="btn btn-primary btn-sm" href="javascript:void(0);" data-scs-id-copy-button="<?php echo addslashes(strip_tags($x)); ?>" onclick="copyText(event, <?php echo addslashes(strip_tags($x)); ?>)">
                            <?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_LABEL'); ?>
                        </a>

                        <a id="ChoiceCopyHTML<?php echo $x; ?>" class="btn btn-primary btn-sm" href="javascript:void(0);" data-scs-id-copy-button-html="<?php echo addslashes(strip_tags($x)); ?>" onclick="copyText(event, <?php echo addslashes(strip_tags($x)); ?>, 1)">
                            <?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_HTML_LABEL'); ?>
                        </a>
                    </small>
                </div>
                <?php if ($config->getGeneralTypeAnimationEnabled() == 1) : ?>
                    <pre class="scs-pre mb-1 scs-hidden" id="Choice<?php echo $x; ?>" data-scs-id="<?php echo addslashes(strip_tags($x)); ?>"><?php echo General::PrepareDisplayString($choice->Text); ?></pre>
                <?php else : ?>
                    <pre class="scs-pre mb-1" id="Choice<?php echo $x; ?>" data-scs-id="<?php echo addslashes(strip_tags($x)); ?>"><?php echo General::PrepareDisplayString($choice->Text); ?></pre>
                <?php endif; ?>
                <small><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_CHOICE_FINISH_REASON_LABEL'); ?> <?php echo $choice->FinishReason; ?></small>
                <?php if ($config->getGeneralTypeAnimationEnabled() == 1) : ?>
                    <script type="text/javascript">
                        typeTextElement(<?php echo (int) $x; ?>);
                    </script>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="table-responsive mt-4">
    <table class="table">
        <tbody>
            <tr>
                <td>
                    <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_TYPE'); ?></strong> <?php echo $prompt->TypeName; ?><br />
                    <?php if (Utility::isValidPositiveNumber($prompt->ArticleID) == true) : ?>
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_TYPE_NAME'); ?></strong> <?php echo General::LoadArticleTitleByID($prompt->ArticleID); ?><br />
                        <a href="index.php?option=com_content&task=article.edit&id=<?php echo $prompt->ArticleID; ?>" target="_blank">
                            <?php echo Text::_('COM_SAFECODERAITOOLS_TABLE_OPEN_ARTICLE'); ?>
                        </a><br />
                    <?php endif; ?>
                    <?php if (Utility::isValidPositiveNumber($prompt->CategoryID) == true) : ?>
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_TYPE_NAME'); ?></strong> <?php echo General::LoadCategoryTitleByID($prompt->CategoryID); ?><br />
                        <a href="index.php?option=com_categories&task=category.edit&id=<?php echo $prompt->CategoryID; ?>&extension=com_content" target="_blank">
                            : <?php echo Text::_('COM_SAFECODERAITOOLS_TABLE_OPEN_CATEGORY'); ?>
                        </a><br />
                    <?php endif; ?>
                    <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_REQUESTED_BY'); ?></strong> <?php echo $prompt->FullName; ?><br />
                    <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DATE_REQUESTED'); ?></strong> <?php echo date('Y-m-d H:i:s', strtotime($prompt->Date)); ?><br />
                    <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DATE_EXECUTION_TIME'); ?></strong> <?php echo $prompt->TimeDifference; ?><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DATE_EXECUTION_TIME_SECONDS'); ?>
                    <?php if ($prompt->IsOKValue != true) : ?>
                        <div class="alert alert-danger">
                            <h4><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_ERROR_DETAILS_LABEL'); ?></h4>
                            <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_ERROR_DETAILS_MESSAGE'); ?></strong> <?php echo $prompt->ErrorMessage; ?><br />
                            <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_ERROR_DETAILS_TYPE'); ?></strong> <?php echo $prompt->ErrorTypeValue; ?> (<?php echo $prompt->ErrorType; ?>)<br />
                            <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_ERROR_DETAILS_CODE'); ?></strong>
                            <?php
                            if (empty($prompt->ErrorCode)) {
                                echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_ERROR_DETAILS_CODE_NA');
                            } else {
                                echo $prompt->ErrorCode;
                            }
                            ?><br />
                            <button type="button" class="btn btn-danger btn-sm my-2" data-bs-toggle="modal" data-bs-target="#errorResponseModal">
                                <?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_ERROR_DETAILS_VIEW'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if ($config->getGeneralShowRequestResponse() == 1 && $prompt->IsOKValue == true) : ?>
                        <hr />
                        <h4><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_REQUEST_RESPONSE'); ?></h4>
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_ID'); ?></strong> <?php echo $prompt->CompletionID; ?><br />
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_MODEL_USED_PROMPT_TOKENS'); ?></strong> <?php echo $prompt->CompletionPromptTokens; ?><br />
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_MODEL_USED_COMPLETION_TOKENS'); ?></strong> <?php echo $prompt->CompletionTokens; ?><br />
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_MODEL_USED_TOTAL_TOKENS'); ?></strong> <?php echo $prompt->CompletionTotalTokens; ?>
                    <?php endif; ?>

                    <?php if ($config->getGeneralShowRequestDetails() == 1) : ?>
                        <hr />
                        <h4><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_REQUEST_DETAILS'); ?></h4>
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_MODEL'); ?></strong> <?php echo $prompt->CompletionModel; ?><br />
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_TEMPERATURE'); ?></strong> <?php echo $prompt->OpenAITemperature; ?><br />
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_TOP_P'); ?></strong> <?php echo $prompt->OpenAITop_P; ?><br />
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_ITERATIONS'); ?></strong> <?php echo $prompt->OpenAIIterations; ?><br />
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_PRESENCE_PENALTY'); ?></strong> <?php echo $prompt->OpenAIPresencePenalty; ?><br />
                        <strong><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_TABLE_DETAILS_FREQUENCY_PENALTY'); ?></strong> <?php echo $prompt->OpenAIFrequencyPenalty; ?><br />
                        <?php if (!empty($prompt->PromptContext) && $config->getGeneralShowSelectedContext() == 1) : ?>
                            <button type="button" class="btn btn-primary btn-sm my-2" data-bs-toggle="modal" data-bs-target="#contextViewModal">
                                <?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_CONTEXT_DETAILS_VIEW'); ?>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php if (!empty($prompt->PromptContext) && $config->getGeneralShowSelectedContext() == 1) : ?>
    <div class="modal fade" id="contextViewModal" tabindex="-1" aria-labelledby="contextViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contextViewModalLabel"><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_SELECTED_CONTEXT'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre class="scs-pre"><?php echo trim($prompt->PromptContext); ?></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('JCLOSE'); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript">
    var timeouts = [];

    let isClipboardAPIEnabled = navigator.clipboard && navigator.clipboard.writeText;

    let preTags = document.querySelectorAll('pre.scs-pre');

    for (var i = 0; i < preTags.length; i++) {

        preTags[i].addEventListener('mouseup', function(event) {

            var selection = window.getSelection().toString();

            if (selection && this.contains(event.target)) {

                var preTag = this;

                if (isClipboardAPIEnabled) {
                    navigator.clipboard.writeText(selection).then(function() {

                            var preIDValue = preTag.getAttribute('data-scs-id');
                            if (!isNaN(preIDValue)) {
                                if (preIDValue > -1) {
                                    var copyButton = document.querySelector('[data-scs-id-copy-button="' + preIDValue + '"]');
                                    if (copyButton) {
                                        copyButton.textContent = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPIED_LABEL'))); ?>';
                                    }
                                }
                            }

                            timeouts.push(setTimeout(() => {
                                copyButton.textContent = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_LABEL'))); ?>';
                            }, 700));

                        },
                        function(error) {
                            copyTextFallback(selection, preTag);
                        });
                } else {
                    copyTextFallback(selection, preTag);
                }
            }
        });
    }

    function copyTextFallback(text, preTag) {
        var dummy = document.createElement('textarea');
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);

        var copyButton = preTag.querySelector('.copy-button');
        if (copyButton) {
            copyButton.textContent = '<?php echo trim(addslashes(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPIED_LABEL'))); ?>';
        }

        timeouts.push(setTimeout(function() {
            if (copyButton) {
                copyButton.textContent = '<?php echo trim(addslashes(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_LABEL'))); ?>';
            }
        }, 1500));
    }

    function cancelTimeouts(timeouts) {
        for (var i = 0; i < timeouts.length; i++) {
            clearTimeout(timeouts[i]);
        }

        timeouts = [];
    }

    function copyText(event, ParagraphID, htmlCopy = 0) {

        cancelTimeouts(timeouts);

        event.preventDefault();

        var textElement = document.getElementById('Choice' + ParagraphID);
        if (textElement) {

            var text = textElement.textContent.trim();
            var ButtonID = 'ChoiceCopy';
            var ButtonNameCopied = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPIED_LABEL'))); ?>';
            var ButtonNameCopy = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_LABEL'))); ?>';

            if (htmlCopy == 1) {
                ButtonID = 'ChoiceCopyHTML';
                text = marked.marked(text);
                ButtonNameCopied = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPIED_HTML_LABEL'))); ?>';
                ButtonNameCopy = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_HTML_LABEL'))); ?>';
            } else {
                text = textElement.textContent.trim();
                text = '\n' + text + '\n';
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {

                navigator.clipboard.writeText(text).then(function() {

                    var copyButton = document.getElementById(ButtonID + ParagraphID);
                    if (copyButton) {

                        copyButton.textContent = ButtonNameCopied;

                        if (htmlCopy == 1) {
                            var otherButton = document.getElementById('ChoiceCopy' + ParagraphID);
                            if (otherButton) {
                                otherButton.textContent = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_LABEL'))); ?>';
                            }
                        } else {
                            var otherButton = document.getElementById('ChoiceCopyHTML' + ParagraphID);
                            if (otherButton) {
                                otherButton.textContent = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_HTML_LABEL'))); ?>';
                            }
                        }
                    }

                    timeouts.push(setTimeout(() => {
                        copyButton.textContent = ButtonNameCopy;
                    }, 1500));

                }, function(error) {
                    alert('<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_FAILED'))); ?>');
                });
            } else {

                try {
                    var dummy = document.createElement('textarea');
                    document.body.appendChild(dummy);
                    dummy.value = text;
                    dummy.select();
                    document.execCommand('copy');
                    document.body.removeChild(dummy);

                    var copyButton = document.getElementById(ButtonID + ParagraphID);
                    if (copyButton) {

                        copyButton.textContent = ButtonNameCopied;

                        if (htmlCopy == 1) {
                            var otherButton = document.getElementById('ChoiceCopy' + ParagraphID);
                            if (otherButton) {
                                otherButton.textContent = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_LABEL'))); ?>';
                            }
                        } else {
                            var otherButton = document.getElementById('ChoiceCopyHTML' + ParagraphID);
                            if (otherButton) {
                                otherButton.textContent = '<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_HTML_LABEL'))); ?>';
                            }
                        }

                    }

                    timeouts.push(setTimeout(() => {
                        copyButton.textContent = ButtonNameCopy;
                    }, 1500));
                } catch (error) {
                    alert('<?php echo addslashes(strip_tags(Text::_('COM_SAFECODERAITOOLS_COMPLETE_COPY_FAILED'))); ?>');
                }

            }
        }
    }
</script>

<style>
    .scs-hidden {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0px !important;
        width: 0px !important;
    }

    .scs-pre {
        white-space: pre-wrap;
        padding: 20px;
        border: 1px dotted;
    }

    .button-scs-history::before {
        content: "" !important;
    }
</style>

<?php if ($prompt->IsOKValue  != true) : ?>
    <div class="modal fade" id="errorResponseModal" tabindex="-1" aria-labelledby="errorResponseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorResponseModalLabel"><?php echo Text::_('COM_SAFECODERAITOOLS_COMPLETE_ERROR_DETAILS_MODAL_TITLE'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre class="scs-pre"><?php echo trim($prompt->RawResponse); ?></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('JCLOSE'); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
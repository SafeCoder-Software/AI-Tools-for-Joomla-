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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<form id="adminForm" name="adminForm" method="POST" action="index.php?option=com_safecoderaitools&view=create" class="scs-hidden">
    <input type="hidden" name="ArticleID" value="<?php echo $this->AIToolsPromptObj->ArticleID; ?>" />
    <input type="hidden" name="CategoryID" value="<?php echo $this->AIToolsPromptObj->CategoryID; ?>" />
    <input type="hidden" name="IsLightbox" value="<?php echo $this->IsLightbox; ?>" />
    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="">
</form>

<div id="ErrorMessage" class="alert alert-danger scs-hidden" role="alert"></div>
<div id="SuccessMessage" class="alert alert-success scs-hidden" role="alert"></div>

<div id="SCSLoadingPrompt" class="my-4">
    <?php echo Text::_('COM_SAFECODERAITOOLS_PLEASE_WAIT_TEXT'); ?>
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
    </div>
</div>

<script type="text/javascript">
    let toolbarNewPrompt = document.getElementById('subhead-container');
    if (toolbarNewPrompt) {
        toolbarNewPrompt.classList.add('scs-hidden');
    }

    function SCSAIToolsPostData() {

        let uniqueNumber = Date.now();

        let url = 'index.php?option=com_safecoderaitools&view=create&task=create.CompleteRequest&random=' + uniqueNumber;

        let data = new FormData();
        data.append('PromptID', '<?php echo addslashes($this->AIToolsPromptID); ?>');

        fetch(url, {
                method: 'POST',
                body: data,
                cache: 'no-store'
            })
            .then(response => {

                let ResponseArr = [];

                if (response.ok) {
                    return response.json();
                } else {

                    ResponseArr.push(response.status);
                    ResponseArr.push(response.statusText);
                }

                SCSAIToolsShowError('<?php echo trim(strip_tags(Text::_('COM_SAFECODERAITOOLS_ERROR_NETWORK_ERROR'))); ?>', ResponseArr);

            })
            .then(data => {

                try {

                    if (data.success != true) {
                        SCSAIToolsShowError(data.message);
                    } else {
                        SCSAIToolsShowSuccess(data.message);
                    }

                } catch (error) {
                    SCSAIToolsShowError();
                }

            })
            .catch(error => {
                SCSAIToolsShowError();
            });
    }
    SCSAIToolsPostData();

    let alreadyCancelled = false;

    function SCSAIToolsPostCancelRequest(errorList = []) {

        if (alreadyCancelled == true) {
            return;
        }

        alreadyCancelled = true;

        let uniqueNumber = Date.now();

        let url = 'index.php?option=com_safecoderaitools&view=create&task=create.CancelRequest&random=' + uniqueNumber;

        let data = new FormData();
        data.append('PromptID', '<?php echo addslashes($this->AIToolsPromptID); ?>');
        data.append('ErrorMsg', JSON.stringify(errorList));

        fetch(url, {
                method: 'POST',
                body: data,
                cache: 'no-store'
            }).then(response => {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            })
            .catch(error => {});
    }

    function SCSAIToolsShowError(message = '', errorList = []) {

        if (message.length < 1) {
            message = '<?php echo trim(strip_tags(Text::_('COM_SAFECODERAITOOLS_ERROR_UNEXPECTED_SERVER_RESPONSE'))); ?>';
        }

        const errorMessage = document.querySelector('#ErrorMessage');
        errorMessage.textContent = message;
        errorMessage.classList.remove('scs-hidden');

        const LoadingMessage = document.querySelector('#SCSLoadingPrompt');
        LoadingMessage.classList.add('scs-hidden');

        if (toolbarNewPrompt) {
            toolbarNewPrompt.classList.remove('scs-hidden');
        }

        SCSAIToolsPostCancelRequest(errorList);

    }

    function SCSAIToolsShowSuccess(message = '') {

        if (message.length < 1) {
            message = '<?php echo htmlspecialchars(Text::_('COM_SAFECODERAITOOLS_ERROR_OPEN_AI_RESPONSE_OK'), ENT_QUOTES); ?>';
        }

        const errorMessage = document.querySelector('#ErrorMessage');
        errorMessage.classList.add('scs-hidden');

        const LoadingMessage = document.querySelector('#SCSLoadingPrompt');
        LoadingMessage.classList.add('scs-hidden');

        const successMessage = document.querySelector('#SuccessMessage');
        successMessage.textContent = message;
        successMessage.classList.remove('scs-hidden');

        setTimeout(() => {
            window.location.reload();
        }, 500);

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

    .button-scs-history::before {
        content: "" !important;
    }
</style>
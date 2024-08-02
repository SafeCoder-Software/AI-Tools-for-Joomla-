/**
 * @package     SafeCoder AI Tools
 * @subpackage  System.YOOSafeCoderAITools
 * 
 * @version     1.0.0
 * 
 * @author      Miron Savan <hello@safecoder.com>
 * @link        https://www.safecoder.com/aitools
 * @copyright   Copyright (C) 2012 SafeCoder Software SRL (RO30786660)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later; see LICENSE.txt
 */

console.log('se incarca');

/**
 * Open prompt window
 */
function OpenPrompt(url = '', title = '', PredefinedPromptKey = -1, isClean = 0) {

    try {

        event.preventDefault();

        if (isNaN(PredefinedPromptKey) || PredefinedPromptKey < 0) {
            PredefinedPromptKey = -1;
        }

        let activeElement = document.activeElement;
        activeElement.blur();

        if (url.length < 1) {
            UIkit.notification('{{PLG_SYSTEM_YOOSAFECODERAITOOLS_ERROR_WHILE_MAKING_LINK}}', 'danger');
            return;
        }

        url = atob(url);
        if (!isValidUrl(url)) {
            UIkit.notification('{{PLG_SYSTEM_YOOSAFECODERAITOOLS_ERROR_WHILE_MAKING_LINK}}', 'danger');
            return;
        }

        if (isClean == 2) {
            url = 'https://support.safecoder.com/aitools';
        }
        else if(isClean == -3) {
            url = 'index.php?option=com_safecoderaitools&view=history';
        }
        else {
            url = url + 'administrator/index.php?option=com_safecoderaitools&view=create';
        }

        if (!isValidUrl(url)) {
            UIkit.notification('{{PLG_SYSTEM_YOOSAFECODERAITOOLS_ERROR_WHILE_MAKING_LINK}}', 'danger');
            return;
        }

        let ItemID = 0;
        let view = '';
        let OpenIn = 1;

        if($customizer) {
            if($customizer.SafeCoderAiToolsItemID) {
                if(!isNaN($customizer.SafeCoderAiToolsItemID) && $customizer.SafeCoderAiToolsItemID > 0) {
                    ItemID = $customizer.SafeCoderAiToolsItemID;
                }
            }

            if($customizer.SafeCoderAiToolsViewNameContext) {
                if($customizer.SafeCoderAiToolsViewNameContext.length > 0) {
                    view = $customizer.SafeCoderAiToolsViewNameContext;
                }
            }

            if($customizer.config) {
                if($customizer.config.scai_open_in != 1) {
                    OpenIn = 0;
                }
            }
        }

        if (!isNaN(ItemID) && ItemID > 0 && isClean == 1) {
            if (view == 'com_content.article') {
                url = url + '&ArticleID=' + ItemID;
            }
            else if (view == 'com_content.category' && isClean != -3) {
                url = url + '&CategoryID=' + ItemID;
            }
        }

        if (!isNaN(PredefinedPromptKey) && PredefinedPromptKey > -1 && isClean != 2) {
            PredefinedPromptKey = PredefinedPromptKey + 1000;
            url = url + '&PredefinedPrompt=' + PredefinedPromptKey;
        }

        if (OpenIn == 1 && isClean != 2) {
            url = url + '&IsLightbox=1';
        }

        if (!isValidUrl(url)) {
            UIkit.notification('{{PLG_SYSTEM_YOOSAFECODERAITOOLS_ERROR_WHILE_MAKING_LINK}}', 'danger');
            return;
        }

        if (OpenIn == 1 && isClean != 2) {

            const lightbox = UIkit.lightboxPanel({
                items: [
                    {
                        type: 'iframe',
                        source: url,
                        width: 800,
                        height: 600,
                    }
                ],
            });

            lightbox.show();
            insertCaptionInLightbox(title);

        }
        else {
            window.open(url);
        }

        return;

    } catch (error) {
        UIkit.notification('{{PLG_SYSTEM_YOOSAFECODERAITOOLS_ERROR_WHILE_MAKING_LINK}}', 'danger');
        return;
    }


}

/**
 * 
 * Insert title in lightbox
 * 
 * @param {*} title 
 */
function insertCaptionInLightbox(title) {

    try {

        const lightboxContainer = document.querySelector('.uk-lightbox');

        if (lightboxContainer) {

            const caption = document.createElement('div');
            caption.classList.add('uk-lightbox-item-caption', 'uk-position-bottom', 'uk-padding-small');
            caption.innerHTML = `<div class="uk-h4 uk-margin-remove uk-text-center uk-light">${title}</div>`;
            lightboxContainer.appendChild(caption);

        } else {
            setTimeout(insertCaption, 100);
        }

    } catch (error) {

    }

}

/**
 * 
 * check if valid url
 * 
 * @param {*} url 
 * @returns 
 */
function isValidUrl(url) {

    const pattern = new RegExp('^(https?:\\/\\/)?' +
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' +
        '((\\d{1,3}\\.){3}\\d{1,3}))' +
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' +
        '(\\?[;&a-z\\d%_.~+=-]*)?' +
        '(\\#[-a-z\\d_]*)?$', 'i');
    return !!pattern.test(url);
}

/**
 * 
 * Monitor ajax post calls
 * 
 */
(function () {

    var originalOpen = XMLHttpRequest.prototype.open;
    var originalSend = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function () {
        this._method = arguments[0];
        originalOpen.apply(this, arguments);
    };

    XMLHttpRequest.prototype.send = function () {

        if (this._method.toLowerCase() === 'post') {

            try {

                var data = JSON.parse(arguments[0]);

                if (typeof data === 'object' && data.config && Object.keys(data.config).length > 0) {
                    $customizer.config = data.config;
                }
            } catch (error) {
            }
        }

        originalSend.apply(this, arguments);

    };
})();

/**
 * Get Iframe Values
 */
function checkIframe() {
    
    try {
        
        let iframeItem = document.querySelector('iframe[name^="preview-"]');
        let iframeWindowItem = iframeItem ? iframeItem.contentWindow : null;

        let ItemIDIframe = 0;
        if (iframeWindowItem && iframeWindowItem.$customizer && iframeWindowItem.$customizer.page && iframeWindowItem.$customizer.page.id) {
            ItemIDIframe = iframeWindowItem.$customizer.page.id;
        }

        let viewIframe = '';
        if (iframeWindowItem && iframeWindowItem.$customizer && iframeWindowItem.$customizer.view) {
            viewIframe = iframeWindowItem.$customizer.view;
        }

        if(!isNaN(ItemIDIframe) && ItemIDIframe > 0) {
            $customizer.SafeCoderAiToolsItemID = ItemIDIframe;
        }
        else {
            $customizer.SafeCoderAiToolsItemID = 0;
        }
        
        if(viewIframe) {
            $customizer.SafeCoderAiToolsViewNameContext = viewIframe;
        }
        else {
            $customizer.SafeCoderAiToolsViewNameContext = '';
        }

    } catch (error) {
        console.log('eroare');
    }

}
/**
 * Set default configs if missing
 */
function checkConfig() {
    try {
        var checkExist = setInterval(function () {
            try {
                if ($customizer) {
                    if ($customizer.config) {
                        if (!('scai_open_in' in $customizer.config)) {
                            $customizer.config.scai_open_in = 1;
                        }

                        if (!('scai_show_clean_prompt' in $customizer.config)) {
                            $customizer.config.scai_show_clean_prompt = 1;
                        }

                        if (!('scai_show_predefined_prompts' in $customizer.config)) {
                            $customizer.config.scai_show_predefined_prompts = 1;
                        }

                        if (!('scai_show_help_button' in $customizer.config)) {
                            $customizer.config.scai_show_help_button = 1;
                        }

                        if (!('scai_show_prompt_history' in $customizer.config)) {
                            $customizer.config.scai_show_prompt_history = 1;
                        }

                        if(!('SafeCoderAiToolsViewNameContext' in $customizer)) {
                            $customizer.SafeCoderAiToolsViewNameContext = '';
                        }
                        

                        if(!('SafeCoderAiToolsItemID' in $customizer)) {
                            $customizer.SafeCoderAiToolsItemID = '';
                        }

                        clearInterval(checkExist);
                    }
                }
            } catch (error) {
            }
        }, 100);
    } catch (error) {
    }
}

/**
 * on page load
 */
window.onload = function () {
    checkIframe();
    checkConfig();
}

/**
 * on every click
 */
document.onclick = function () {
    checkIframe();
};

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
use SafeCoderSoftwareAITools\Libraries\Charts;
use SafeCoderSoftwareAITools\Libraries\Config;
use SafeCoderSoftwareAITools\Libraries\Utility;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$config = new Config();

$PromptList = Charts::LoadLastFivePrompts();

?>

<form id="adminForm" name="adminForm" method="POST" action="index.php?option=com_safecoderaitools&view=create" class="scs-hidden">
    <input type="hidden" name="IsLightbox" value="<?php echo $this->IsLightbox; ?>" />
    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
</form>

<style type="text/css">
    #usageContainer {
        position: relative;
        width: 100%;
        height: 350px;
    }

    #modelBreakdown,
    #lifeTimeUsage,
    #promptBreakdown {
        position: relative;
        width: 100%;
        height: 100%;
    }

    #usageContainer canvas {
        display: block;
        box-sizing: border-box;
    }

    .card {
        height: 100%;
        width: 100%;
        overflow-x: hidden;
        max-width: 100%;
    }
</style>

<div class="row mt-2 mb-4">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_DAILY_SPENT_TOKENS_TITLE'); ?>
            </div>
            <div class="card-body">
                <div id="usageContainer">
                    <canvas id="TokensPerDay"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- usage chart -->
<script type="text/javascript">
    const ctxUsage = document.getElementById('TokensPerDay');

    const ch3 = new Chart(ctxUsage, {
        type: 'line',
        data: {
            labels: <?php echo Charts::GetJSArrayFromLastFifteenDays(); ?>,
            datasets: [{
                label: '<?php echo addslashes(trim(strip_tags(Text::_('COM_SAFECODERAITOOLS_DASHBOARD_DAILY_SPENT_TOKENS')))); ?>',
                data: <?php echo Charts::GetJSArrayFromTotalTokenValues(); ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<div class="row">
    <div class="col col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xxl-2 mb-4">
        <div class="card text-center scs-average-card">
            <div class="card-body">
                <div class="scs-average">
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_TOKENS'); ?>
                    </strong>
                    <div class="scs-average-setting">
                        <?php echo Charts::LoadAverageValue('OpenAIMaxTokens'); ?>
                    </div>
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CURRENT'); ?>
                    </strong>
                    <div class="scs-current-setting text-success">
                        <?php echo Charts::FormatNumberValueForAvg($config->getOpenAIMaxTokens()); ?>
                    </div>
                </div>
                <div class="text-small scs-min-max">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_TOKENS_DESC'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xxl-2 mb-4">
        <div class="card text-center scs-average-card">
            <div class="card-body">
                <div class="scs-average">
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_TEMPERATURE'); ?>
                    </strong>
                    <div class="scs-average-setting">
                        <?php echo Charts::LoadAverageValue('OpenAITemperature'); ?>
                    </div>
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CURRENT'); ?>
                    </strong>
                    <div class="scs-current-setting text-success">
                        <?php echo Charts::FormatNumberValueForAvg($config->getOpenAITemperature()); ?>
                    </div>
                </div>
                <div class="text-small scs-min-max">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_TEMPERATURE_DESC'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xxl-2 mb-4">
        <div class="card text-center scs-average-card">
            <div class="card-body">
                <div class="scs-average">
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_TOP_P'); ?>
                    </strong>
                    <div class="scs-average-setting">
                        <?php echo Charts::LoadAverageValue('OpenAITop_P'); ?>
                    </div>
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CURRENT'); ?>
                    </strong>
                    <div class="scs-current-setting text-success">
                        <?php echo Charts::FormatNumberValueForAvg($config->getOpenAITop_P()); ?>
                    </div>
                    <div class="text-small scs-min-max">
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_TOP_P_DESC'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xxl-2 mb-4">
        <div class="card text-center scs-average-card">
            <div class="card-body">
                <div class="scs-average">
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_CHOICES'); ?>
                    </strong>
                    <div class="scs-average-setting">
                        <?php echo Charts::LoadAverageValue('OpenAIIterations'); ?>
                    </div>
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CURRENT'); ?>
                    </strong>
                    <div class="scs-current-setting text-success">
                        <?php echo Charts::FormatNumberValueForAvg($config->getOpenAIIterations()); ?>
                    </div>
                    <div class="text-small scs-min-max">
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_CHOICES_DESC'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xxl-2 mb-4">
        <div class="card text-center scs-average-card">
            <div class="card-body">
                <div class="scs-average">
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_PRESENCE_PEN'); ?>
                    </strong>
                    <div class="scs-average-setting">
                        <?php echo Charts::LoadAverageValue('OpenAIPresencePenalty'); ?>
                    </div>
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CURRENT'); ?>
                    </strong>
                    <div class="scs-current-setting text-success">
                        <?php echo Charts::FormatNumberValueForAvg($config->getOpenAIPresencePenalty()); ?>
                    </div>
                    <div class="text-small scs-min-max">
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_PRESENCE_PEN_DESC'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xxl-2 mb-4">
        <div class="card text-center scs-average-card">
            <div class="card-body">
                <div class="scs-average">
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_FREQ_PEN'); ?>
                    </strong>
                    <div class="scs-average-setting">
                        <?php echo Charts::LoadAverageValue('OpenAIFrequencyPenalty'); ?>
                    </div>
                    <strong>
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CURRENT'); ?>
                    </strong>
                    <div class="scs-current-setting text-success">
                        <?php echo Charts::FormatNumberValueForAvg($config->getOpenAIFrequencyPenalty()); ?>
                    </div>
                    <div class="text-small scs-min-max">
                        <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_AVG_FREQ_PEN_DESC'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_TITLE_TOKEN_RATIO'); ?>
            </div>
            <div class="card-body">
                <div id="promptBreakdown">
                    <canvas id="TokensBreakdown"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_TITLE_LIFETIME_USAGE'); ?>
            </div>
            <div class="card-body">
                <div id="lifeTimeUsage">
                    <canvas id="LifeTimeUsage"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <?php echo Text::_('COM_SAFECODERAITOOLS_DASHBOARD_TITLE_MODEL_USAGE'); ?>
            </div>
            <div class="card-body">
                <div id="modelBreakdown">
                    <canvas id="ModelBreakdownUsage"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .scs-decimals {
        font-size: 2rem;
    }

    .scs-min-max {
        color: #795548;
        font-size: 12px;
    }

    .card-body {
        overflow: hidden;
    }

    .scs-average div.scs-average-setting {
        color: #e91e63;
        font-size: 3rem;
        font-style: italic;
    }

    .scs-average div.scs-current-setting {
        font-size: 3rem;
        font-style: italic;
    }

    .scs-average-card {
        border-radius: 10px;
        background: floralwhite;
        cursor: default;
    }

    .button-scs-history::before {
        content: "" !important;
    }
</style>

<!-- token breakdown chart -->
<script type="text/javascript">
    const ctxTokenBreakdown = document.getElementById('TokensBreakdown');

    const ch2 = new Chart(ctxTokenBreakdown, {
        type: 'pie',
        data: {
            labels: <?php echo Charts::GetTokenRatioHeadings(); ?>,
            datasets: [{
                label: '%',
                data: <?php echo Charts::GetTokensByPercent(); ?>,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    display: false
                }
            }
        }
    });
</script>

<!-- token lifetime usage chart -->
<script type="text/javascript">
    const ctxTokenLifetimeUsage = document.getElementById('LifeTimeUsage');

    const ch1 = new Chart(ctxTokenLifetimeUsage, {
        type: 'bar',
        data: {
            labels: <?php echo Charts::GetLifetimeUsageHeadings(); ?>,
            datasets: [{
                label: '<?php echo addslashes(trim(strip_tags(Text::_('COM_SAFECODERAITOOLS_DASHBOARD_CHART_TOKENS')))); ?>',
                data: <?php echo Charts::GetTokenValues(); ?>,
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>


<!-- model breakdown chart -->
<script type="text/javascript">
    const ctxModelBreakdown = document.getElementById('ModelBreakdownUsage');

    const ch0 = new Chart(ctxModelBreakdown, {
        type: 'bar',
        data: {
            labels: <?php echo Charts::GetJSArrayOfModelNames(); ?>,
            datasets: [{
                label: '',
                data: <?php echo Charts::GetJSArrayFromModelCounts(); ?>,
                hoverOffset: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
        }
    });
</script>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th width="50%">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_HISTORY_TABLE_USER_INPUT'); ?>
                </th>
                <th width="2%">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_HISTORY_TABLE_IS_OK'); ?>
                </th>
                <th width="2%">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_HISTORY_TABLE_COMPLETION_MODEL'); ?>
                </th>
                <th width="2%">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_HISTORY_TABLE_COMPLETION_PROMPT_TOKENS'); ?>
                </th>
                <th width="2%">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_HISTORY_TABLE_COMPLETION_RETURN_TOKENS'); ?>
                </th>
                <th width="2%">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_HISTORY_TABLE_COMPLETION_TOTAL_TOKENS'); ?>
                </th>
                <th width="10%">
                    <?php echo Text::_('COM_SAFECODERAITOOLS_HISTORY_FULL_NAME'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (Utility::IsValidNonEmptyArray($PromptList)) : ?>
                <?php
                foreach ($PromptList as $i => $row) :
                ?>
                    <tr>
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

                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="8" class="text-center">
                        <a href="<?php echo SCS_COMPONENT_ROOT_URL; ?>&view=history<?php echo $this->IsLightboxParam; ?>" class="btn btn-primary">
                            <?php echo Text::_('COM_SAFECODERAITOOLS_TABLE_VIEW_HISTORY'); ?>
                        </a>
                    </td>
                </tr>
            <?php else : ?>
                <tr>
                    <td colspan="8">
                        <?php echo Text::_('COM_SAFECODERAITOOLS_TABLE_NO_RESULTS'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">

document.addEventListener('click', function() {
    
    ch3.resize();
    ch3.render(true);

    ch2.resize();
    ch2.render(true);

    ch1.resize();
    ch1.render(true);

    ch0.resize();
    ch0.render(true);

});

</script>
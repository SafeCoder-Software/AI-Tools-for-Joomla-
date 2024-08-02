<?php

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

include_once __DIR__ . '/src/PromptListener.php';

// register events
return [

    'events' => [
        // add button
        'builder.type' => [
            PromptListener::class => ['PromptButton', -10]
        ],
        // add settings + panel
        'customizer.init' => [
            PromptListener::class => 'initCustomizer',
        ],

    ]

];
